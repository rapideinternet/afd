<?php

namespace SIVI\AFD\Parsers;

use SIVI\AFD\Exceptions\InvalidParseException;
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
     */
    public function __construct(
        MessageRepository $messageRepository,
        EntityRepository $entityRepository,
        AttributeRepository $attributeRepository
    ) {
        $this->messageRepository   = $messageRepository;
        $this->entityRepository    = $entityRepository;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @throws InvalidParseException
     */
    public function parse(string $xmlString): Message
    {
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);

        if ($xml === false) {
            throw new InvalidParseException('Failed to parse a string to SimpleXMLElement');
        }

        $message = $this->processMessage($xml->getName(), $xml);

        if (empty($message->getMessageId())) {
            $message->setMessageId(md5($xmlString));
        }

        return $message;
    }

    /**
     * @param $name
     */
    public function processMessage($name, $nodes): Message
    {
        $message = $this->messageRepository->getByLabel($name);

        $messageContent = $this->serialiseNode($nodes);
        if ($messageContent !== null) {
            $message->setMessageContentHash(md5($messageContent));
        }

        //Loop over nodes
        foreach ($nodes as $key => $node) {
            $this->processNode($message, $key, $node);
        }

        return $message;
    }

    /**
     * @param $key
     * @param $node
     */
    public function processNode(Message $message, $key, $node)
    {
        //Determine if it is an entity
        if ($this->isEntity($key) && ($entity = $this->processEntity($key, $node)) instanceof Entity) {
            $message->addEntity($entity);
        } elseif ($this->isSubmessage($key)) {
            $submessage = $this->processMessage($key, $node);
            $submessageContent = $this->serialiseNode($node);
            if ($submessageContent !== null) {
                $hash = md5($submessageContent);
                $submessage->setMessageContentHash($hash);
                $submessage->setMessageId($this->appendHashToMessageId($submessage->getMessageId(), $hash));
            }
            $message->addSubmessage($submessage);
        }
    }

    /**
     * @param $key
     *
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
     */
    protected function processAttribute($attributeLabel, $value): ?Attribute
    {
        return $this->attributeRepository->getByLabel($attributeLabel, $this->processValue($value));
    }

    /**
     * @param $key
     *
     * @return bool
     */
    protected function isSubmessage($key)
    {
        //TODO: Is in list of possible sub messages
        return true;
    }

    protected function serialiseNode($node): ?string
    {
        if ($node instanceof \SimpleXMLElement) {
            $xml = $node->asXML();

            if ($xml !== false) {
                return $xml;
            }
        }

        if (is_scalar($node) || (is_object($node) && method_exists($node, '__toString'))) {
            return (string)$node;
        }

        return null;
    }

    protected function appendHashToMessageId(?string $messageId, string $hash): string
    {
        $messageId = $messageId ?? '';

        if ($messageId === '') {
            return $hash;
        }

        return sprintf('%s-%s', $messageId, $hash);
    }
}
