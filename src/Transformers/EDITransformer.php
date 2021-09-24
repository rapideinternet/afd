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

    const EOL = EDIParser::EOL . "\n";

    /**
     * @param Message $message
     * @return string
     * @throws EDIException
     */
    public function transform(Message $message): string
    {
        return $this->transformMessage($message);
    }

    /**
     * @param Message $message
     * @return string
     * @throws EDIException
     */
    public function transformMessage(Message $message): string
    {
        $batchRows = $this->buildHeader($message);
        $messageRows = $this->buildMessageHeader($message);

        /** @var Entity[] $entityGroup */
        foreach ($message->getEntities() as $entityGroup) {
            foreach ($entityGroup as $orderNumber => $entity) {
                $messageRows = array_merge($messageRows, $this->buildEntity($entity, $orderNumber));
            }
        }

        $messageRows[] = $this->buildMessageFooter($message, count($messageRows) + 1);
        $batchRows = array_merge($batchRows, $messageRows);
        $batchRows[] = $this->buildFooter($message);

        return sprintf('%s%s', implode(self::EOL, $batchRows), self::EOL);
    }

    /**
     * @param Message $message
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
     * @param Message $message
     * @return array
     * @throws EDIException
     */
    protected function buildMessageHeader(Message $message)
    {
        return [
            vsprintf('UNH+%s+INSLBW:1:0:IN:%s', [
                $message->getMessageId(),
                $this->getTypeByLabel($message->getLabel()),
            ])
        ];
    }

    /**
     * @param $label
     * @return string
     * @throws EDIException
     */
    protected function getTypeByLabel($label)
    {
        switch ($label) {
            case Messages::CONTRACT_MESSAGE:
                return EDIParser::MESSAGE_TYPE_CONTRACT;
                break;
            case Messages::PROLONGATION:
                return EDIParser::MESSAGE_TYPE_PROLONGATION;
                break;
            case Messages::MUTATION:
                return EDIParser::MESSAGE_TYPE_MUTATION;
                break;
            default:
                throw new EDIException('Could not determine message type by label');
        }
    }

    /**
     * @param Entity $entity
     * @return array
     */
    protected function buildEntity(Entity $entity, $orderNumber)
    {
        $transformedEntity = [
            vsprintf('ENT+%s+%s', [
                $entity->getLabel(),
                $orderNumber,
            ])
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
     * @param Message $message
     * @return string
     * @throws EDIException
     */
    protected function buildMessageFooter(Message $message, $rowCount)
    {
        return vsprintf('UNT+%s+%s', [
            $rowCount,
            $message->getMessageId(),
        ]);
    }

    /**
     * @param Message $message
     * @return string
     * @throws EDIException
     */
    protected function buildFooter(Message $message)
    {
        return vsprintf('UNZ+1+%s', [
            $message->getMessageId(),
        ]);
    }
}
