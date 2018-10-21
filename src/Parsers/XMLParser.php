<?php


namespace SIVI\AFD\Parsers;


use SIVI\AFD\Models\Entity;
use SIVI\AFD\Models\Message;
use SIVI\AFD\Repositories\Contracts\AttributeRepository;
use SIVI\AFD\Repositories\Contracts\EntityRepository;
use SIVI\AFD\Repositories\Contracts\MessageRepository;

class XMLParser extends Parser implements \SIVI\AFD\Parsers\Contracts\XMLParser
{
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

    public function parse($xmlString): Message
    {
        $xml = simplexml_load_string($xmlString);

        return $this->processMessage($xml->getName(), $xml);
    }

    public function processNode(Message $message, $key, $node)
    {
        //Determine if it is an entity
        if ($this->isEntity($key)) {
            $message->addEntity($this->processEntity($key, $node));
        } elseif ($this->isSubmessage($key)) {
            $message->addSubmessage($this->processMessage($key, $node));
        }
    }

    /**
     * @param $name
     * @return Message
     */
    public function processMessage($name, $nodes): Message
    {
        $message = $this->messageRepository->getByLabel($name);

        //Loop over nodes
        foreach ($nodes as $key => $node) {
            $this->processNode($message, $key, $node);
        }

        return $message;
    }

    public function processEntity($entityLabel, $nodes): Entity
    {
        $entity = $this->entityRepository->getByLabel($entityLabel);

        foreach ($nodes as $nodeLabel => $node) {

            if ($this->isEntity($nodeLabel)) {
                $entity->addSubEntity($this->processEntity($nodeLabel, $node));
            } else {
                $entity->addAttribute($this->processAttribute($nodeLabel, $node));
            }
        }

        return $entity;
    }

    protected function processAttribute($attributeLabel, $value)
    {
        return $this->attributeRepository->getByLabel($attributeLabel, $this->processValue($value));
    }

    protected function processValue(\SimpleXMLElement $value)
    {
        $value = (array)$value;

        if (count($value) == 1) {
            return array_first($value);
        }

        if (count($value) > 1) {
            return $value;
        }

        return null;
    }

    protected function isEntity($key)
    {
        //Is length 2
        if (strlen($key) == 2) {
            //Is in list of possible entities


            return true;
        }
    }

    protected function isSubmessage($key)
    {
        //Is in list of possible sub messages
        return true;
    }

}
