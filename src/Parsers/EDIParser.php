<?php


namespace SIVI\AFD\Parsers;


use SIVI\AFD\Enums\EntityTypes;
use SIVI\AFD\Enums\Messages;
use SIVI\AFD\Models\Attribute;
use SIVI\AFD\Models\Entity;
use SIVI\AFD\Models\Message;
use SIVI\AFD\Parsers\Contracts\EDIParser as EDIParserContract;
use SIVI\AFD\Repositories\Contracts\AttributeRepository;
use SIVI\AFD\Repositories\Contracts\EntityRepository;
use SIVI\AFD\Repositories\Contracts\MessageRepository;

class EDIParser extends Parser implements EDIParserContract
{

    const SEPERATOR = "+";
    const EOL = "'";
    const ENTITY = "ENT";
    const SENDER_DETAILS = "UNB";
    const INSURANCE_DETAILS = "PP";


    /**
     * @var MessageRepository
     */
    protected $messageRepository;
    /**
     * @var EntityRepository
     */
    protected $entityRepository;
    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * XMLParser constructor.
     * @param MessageRepository $messageRepository
     */
    public function __construct(
        MessageRepository $messageRepository,
        EntityRepository $entityRepository,
        AttributeRepository $attributeRepository
    ) {
        $this->messageRepository = $messageRepository;
        $this->entityRepository = $entityRepository;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @param $xmlString
     * @return Message
     */
    public function parse($ediContent): Message
    {
        $data = $this->formatEDIDataToArray($ediContent);
        
        if (count($data) > 1) {
            $message = $this->messageRepository->getByLabel(Messages::BATCH);

            foreach ($data as $entities) {
                $message->addSubmessage($this->processMessage($entities));
            }

        } else {
            $message = $this->processMessage($data);
        }

        return $message;
    }

    protected function formatEDIDataToArray($data)
    {
        $entityCode = null;
        $level = 0;

        $batch = -1;
        $batches = [];

        $started = false;
        foreach (explode(self::EOL, $data) as $line) {
            $line = trim($line, "\r\n");

            if (substr_count($line, '+') < 2) {
                continue;
            }

            list($id, $key, $value) = explode(self::SEPERATOR, $line);

            if ($id == self::SENDER_DETAILS) {
                $isBatch = true;
            }

            if (!$started && $id == self::ENTITY && $key == EntityTypes::MESSAGE_DETAILS) {
                $started = true;
            }

            if (!$started) {
                continue;
            }

            if ($id == self::ENTITY && $key == EntityTypes::MESSAGE_DETAILS) {
                $batch++;
                $batches[$batch] = [];
            }

            if ($id == self::ENTITY) {
                $entityCode = $key;
            }

            if ($id == self::ENTITY && !array_key_exists($entityCode, $batches[$batch])) {
                $batches[$batch][$entityCode] = [];
                $level = 0;
            } else {
                if ($id == self::ENTITY) {
                    $level = count($batches[$batch][$entityCode]);
                }
            }

            if ($id == self::ENTITY && !array_key_exists($level, $batches[$batch][$entityCode])) {
                $batches[$batch][$entityCode] = [];
            }


            if ($id != self::ENTITY && $id !== 'UNT' && $id !== 'UNH' && $id !== 'UNZ') {
                $batches[$batch][$entityCode][$key][$level] = trim($value);
            }
        }

        return $batches;
    }

    /**
     * @param array $message
     * @return Message
     */
    protected function processMessage(array $entities): Message
    {
        $message = $this->messageRepository->getByLabel(Messages::CONTRACT);

        foreach ($entities as $entityLabel => $attributes) {
            $message->addEntity($this->processEntity($entityLabel, $attributes));
        }

        return $message;
    }

    /**
     * @param $entityLabel
     * @param array $attributes
     * @return Entity
     */
    protected function processEntity($entityLabel, array $attributes): Entity
    {
        $entity = $this->entityRepository->getByLabel($entityLabel);

        foreach ($attributes as $attributeLabel => $values) {
            $entity->addAttribute($this->processAttribute($entityLabel, $attributeLabel, $values));
        }

        return $entity;
    }

    /**
     * @param $entityLabel
     * @param $attributeLabel
     * @param array $values
     * @return Attribute
     */
    protected function processAttribute($entityLabel, $attributeLabel, array $values): Attribute
    {
        return $this->attributeRepository->getByLabel(sprintf('%s_%s', $entityLabel, $attributeLabel),
            $this->processValue($values));
    }


}
