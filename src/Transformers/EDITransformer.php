<?php

namespace SIVI\AFD\Transformers;

use SIVI\AFD\Enums\Messages;
use SIVI\AFD\Exceptions\EDIException;
use SIVI\AFD\Models\Attribute;
use SIVI\AFD\Models\Entity;
use SIVI\AFD\Models\Message;
use SIVI\AFD\Parsers\EDIParser;

class EDITransformer implements \SIVI\AFD\Transformers\Contracts\EDITransformer
{
    public const EOL = EDIParser::EOL . "\n";

    /**
     * @throws EDIException
     */
    public function transform(Message $message): string
    {
        return $this->transformMessage($message);
    }

    /**
     * @throws EDIException
     */
    public function transformMessage(Message $message): string
    {
        $batchRows   = $this->buildHeader($message);
        $messageRows = $this->buildMessageHeader($message);

        /** @var Entity[] $entityGroup */
        foreach ($message->getEntities() as $entityGroup) {
            foreach ($entityGroup as $orderNumber => $entity) {
                $messageRows = array_merge($messageRows, $this->buildEntity($entity, $orderNumber));
            }
        }

        $messageRows[] = $this->buildMessageFooter($message, count($messageRows) + 1);
        $batchRows     = array_merge($batchRows, $messageRows);
        $batchRows[]   = $this->buildFooter($message);

        return sprintf('%s%s', implode(self::EOL, $batchRows), self::EOL);
    }

    /**
     * @return array
     */
    protected function buildHeader(Message $message)
    {
        return [
            'UNA:+.? ',
            vsprintf('UNB+UNOB:1+%s+%s+%s+%s', [
                $message->getSender(),
                $message->getReceiver(),
                $message->getDateTime()->format(EDIParser::HEADER_DATE_TIME_FORMAT),
                $message->getMessageId(),
            ]),
        ];
    }

    /**
     * @throws EDIException
     *
     * @return array
     */
    protected function buildMessageHeader(Message $message)
    {
        return [
            vsprintf('UNH+%s+INSLBW:1:0:IN:%s', [
                $message->getMessageId(),
                $this->getTypeByLabel($message->getLabel()),
            ]),
        ];
    }

    /**
     * @param $label
     *
     * @throws EDIException
     *
     * @return string
     */
    protected function getTypeByLabel($label)
    {
        switch ($label) {
            case Messages::CONTRACT_MESSAGE:
                return EDIParser::MESSAGE_TYPE_CONTRACT;

            case Messages::PROLONGATION:
                return EDIParser::MESSAGE_TYPE_PROLONGATION;

            case Messages::MUTATION:
                return EDIParser::MESSAGE_TYPE_MUTATION;

            default:
                throw new EDIException('Could not determine message type by label');
        }
    }

    /**
     * @return array
     */
    protected function buildEntity(Entity $entity, $orderNumber)
    {
        $transformedEntity = [
            vsprintf('ENT+%s+%s', [
                $entity->getLabel(),
                $orderNumber,
            ]),
        ];

        /** @var Attribute[] $attributeGroup */
        foreach ($entity->getAttributes() as $attributeGroup) {
            foreach ($attributeGroup as $attribute) {
                $transformedEntity[] = vsprintf('LBW+%s+%s', [
                    $attribute->getTypeLabel(),
                    $attribute->getRawValue(),
                ]);
            }
        }

        return $transformedEntity;
    }

    /**
     * @throws EDIException
     *
     * @return string
     */
    protected function buildMessageFooter(Message $message, $rowCount)
    {
        return vsprintf('UNT+%s+%s', [
            $rowCount,
            $message->getMessageId(),
        ]);
    }

    /**
     * @throws EDIException
     *
     * @return string
     */
    protected function buildFooter(Message $message)
    {
        return vsprintf('UNZ+1+%s', [
            $message->getMessageId(),
        ]);
    }
}
