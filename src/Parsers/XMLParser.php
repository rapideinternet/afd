<?php


namespace SIVI\AFD\Parsers;


use SIVI\AFD\Models\Attribute;
use SIVI\AFD\Models\Entity;
use SIVI\AFD\Models\Message;
use SIVI\AFD\Parsers\Contracts\XMLParser as XMLParserContract;
use SIVI\AFD\Repositories\Contracts\AttributeRepository;
use SIVI\AFD\Repositories\Contracts\EntityRepository;
use SIVI\AFD\Repositories\Contracts\MessageRepository;

class XMLParser extends Parser implements XMLParserContract
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

    /**
     * @param $xmlString
     * @return Message
     */
    public function parse($xmlString): Message
    {
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);

        $message = $this->processMessage($xml->getName(), $xml);

        if (empty($message->getMessageId())) {
            $message->setMessageId(md5($xmlString));
        }
        
        return $message;
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

    /**
     * @param Message $message
     * @param $key
     * @param $node
     */
    public function processNode(Message $message, $key, $node)
    {
        //Determine if it is an entity
        if ($this->isEntity($key) && ($entity = $this->processEntity($key, $node)) instanceof Entity) {
            $message->addEntity($entity);
        } elseif ($this->isSubmessage($key)) {
            $message->addSubmessage($this->processMessage($key, $node));
        }
    }

    /**
     * @param $key
     * @return bool
     */
    protected function isEntity($key)
    {
        //Is length 2
        if (strlen($key) == 2) {
            //TODO: Is in list of possible entities


            return true;
        }
    }

    /**
     * @param $entityLabel
     * @param $nodes
     * @return Entity|null
     */
    public function processEntity($entityLabel, $nodes): ?Entity
    {
        $entity = $this->entityRepository->getByLabel($entityLabel);

        foreach ($nodes as $nodeLabel => $node) {

            if ($this->isEntity($nodeLabel) && ($subEntity = $this->processEntity($nodeLabel,
                    $node)) instanceof Entity) {
                $entity->addSubEntity($subEntity);
            } elseif (($attribute = $this->processAttribute($nodeLabel, $node)) instanceof Attribute) {
                $entity->addAttribute($attribute);
            }
        }

        return $entity;
    }

    /**
     * @param $attributeLabel
     * @param $value
     * @return \SIVI\AFD\Models\Attribute|null
     */
    protected function processAttribute($attributeLabel, $value): ?Attribute
    {
        return $this->attributeRepository->getByLabel($attributeLabel, $this->processValue($value));
    }

    /**
     * @param $key
     * @return bool
     */
    protected function isSubmessage($key)
    {
        //TODO: Is in list of possible sub messages
        return true;
    }

}
