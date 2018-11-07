<?php


namespace SIVI\AFD\Transformers;

use SimpleXMLElement;
use SIVI\AFD\Models\Attribute;
use SIVI\AFD\Models\Entity;
use SIVI\AFD\Models\Message;
use SIVI\AFD\Transformers\Contracts\XMLTransformer as XMLTransformerContract;

class XMLTransformer implements XMLTransformerContract
{

    public function transform(Message $message): string
    {
        return $this->transformMessage($message)->asXML();
    }

    public function transformMessage(Message $message, SimpleXMLElement $XMLElement = null): SimpleXMLElement
    {
        if ($XMLElement === null) {
            $XMLElement = new SimpleXMLElement(sprintf('<%s/>', $message->getLabel()));
        }


        foreach ($message->getEntities() as $entities) {
            foreach ($entities as $orderNumber => $entity) {
                $element = $XMLElement->addChild($entity->getLabel());
                $this->transformEntity($element, $entity, $orderNumber);
            }
        }

        foreach ($message->getSubMessages() as $subMessages) {
            foreach ($subMessages as $subMessageOrderNumber => $subMessage) {
                $element = $XMLElement->addChild($subMessage->getLabel());
                $this->transformMessage($subMessage, $element);
            }
        }


        return $XMLElement;
    }

    public function transformEntity(SimpleXMLElement $XMLElement, Entity $entity, $orderNumber = null): SimpleXMLElement
    {

        foreach ($entity->getAttributes() as $attributes) {
            /**
             * @var int $attributeOrderNumber
             * @var Attribute $attribute
             */
            foreach ($attributes as $attribute) {
                $XMLElement->addChild($attribute->getLabel(), $attribute->getFormattedValue());
            }
        }

        foreach ($entity->getSubEntities() as $entities) {
            foreach ($entities as $subEntityOrderNumber => $subEntity) {
                $element = $XMLElement->addChild($subEntity->getLabel());
                $this->transformEntity($element, $subEntity, $subEntityOrderNumber);
            }
        }

        return $XMLElement;
    }
}
