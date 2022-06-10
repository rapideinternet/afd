<?php


namespace SIVI\AFD\Parsers;


use Carbon\Carbon;
use SIVI\AFD\Enums\Messages;
use SIVI\AFD\Exceptions\EDIException;
use SIVI\AFD\Models\Attribute;
use SIVI\AFD\Models\Entity;
use SIVI\AFD\Models\Message;
use SIVI\AFD\Parsers\Contracts\EDIParser as EDIParserContract;
use SIVI\AFD\Repositories\Contracts\AttributeRepository;
use SIVI\AFD\Repositories\Contracts\EntityRepository;
use SIVI\AFD\Repositories\Contracts\MessageRepository;

class EDIParser extends Parser implements EDIParserContract
{

    const SEPARATOR = '+';
    const EOL = '\'';
    const ENTITY = 'ENT';
    const ATTRIBUTE = 'LBW';
    const SERVICE_STRING_ADVICE = 'UNA';
    const INTERCHANGE_HEADER = 'UNB';
    const MESSAGE_HEADER = 'UNH';
    const MESSAGE_TRAILER = 'UNT';
    const INTERCHANGE_TRAILER = 'UNZ';
    const INSURANCE_DETAILS = 'PP';

    const HEADER_DATE_TIME_FORMAT = 'ymd:Hi';
    const HEADER_SEPARATOR = ':';

    const MESSAGE_TYPE_CONTRACT = 'PBI';
    const MESSAGE_TYPE_PROLONGATION = 'PPR';
    const MESSAGE_TYPE_MUTATION = 'PMB';


    public $strictValidation = false;
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
     * @param string $ediContent
     * @return Message
     * @throws EDIException
     */
    public function parse($ediContent): Message
    {
        $this->verifyThatStringContainsEDIFact($ediContent);
        
        $message = $this->messageRepository->getByLabel(Messages::BATCH);

        // TODO: incorporate this in parsing of data
        $allowedSpecialCharacters = '';
        $entityCode = null;
        /** @var Message $subMessage */
        $subMessage = null;
        $orderNumber = 0;
        $entityAttributes = [];

        foreach (explode(self::EOL, $ediContent) as $line) {
            $line = trim($line, "\r\n");

            $rowIdentifier = substr($line, 0, 3);

            if ($rowIdentifier === self::SERVICE_STRING_ADVICE) {
                $allowedSpecialCharacters = substr($line, 3);
            } elseif ($rowIdentifier === self::INTERCHANGE_HEADER) {
                $this->parseHeader($line, $message);
                if (empty($message->getMessageId())) {
                    $message->setMessageId(md5($ediContent));
                }
            } elseif ($rowIdentifier === self::INTERCHANGE_TRAILER) {
                $this->parseFooter($line, $message);
            } elseif ($rowIdentifier === self::MESSAGE_HEADER) {
                $subMessage = $this->getSubMessageByMessageHeader($line, $message);
            } elseif ($rowIdentifier === self::MESSAGE_TRAILER) {
                if (($entity = $this->processEntity($entityCode, $entityAttributes)) instanceof Entity) {
                    $subMessage->addEntity($entity, $orderNumber);
                }
                $entityAttributes = [];
                $message->addSubmessage($subMessage);
            } elseif ($rowIdentifier === self::ENTITY) {
                if (count($entityAttributes)) {
                    if (($entity = $this->processEntity($entityCode, $entityAttributes)) instanceof Entity) {
                        $subMessage->addEntity($entity, $orderNumber);
                    }
                    $entityAttributes = [];
                }
                list(, $entityCode, $orderNumber) = explode(self::SEPARATOR, $line);
            } elseif ($rowIdentifier === self::ATTRIBUTE) {
                $attributeRow = explode(self::SEPARATOR, $line);
                $code = $attributeRow[1];
                $value = $attributeRow[2] ?? null;
                $entityAttributes[$code] = $value;
            }
        }

        return $message;
    }

    protected function verifyThatStringContainsEDIFact($string)
    {
        $needles = [self::SERVICE_STRING_ADVICE, self::INTERCHANGE_HEADER];

        foreach ($needles as $needle) {
            if ($needle !== '' && substr($string, 0, strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        throw new EDIException(sprintf('Provided string does not start with EDIFACT header: "%s"', substr($string, 0, 150)));
    }

    /**
     * @param $headerLine
     * @param Message $message
     */
    protected function parseHeader($headerLine, Message &$message)
    {
        list(
            $rowIdentifier,
            $characterSet,
            $sender,
            $receiver,
            $rawDateTime,
            $batchMessageId
            ) = explode(self::SEPARATOR, $headerLine);

        $dateTime = Carbon::createFromFormat(self::HEADER_DATE_TIME_FORMAT, $rawDateTime);

        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setDateTime($dateTime);
        $message->setMessageId($batchMessageId);
    }

    /**
     * @param $footerLine
     * @param Message $message
     * @throws EDIException
     */
    protected function parseFooter($footerLine, Message $message)
    {
        list($rowIdentifier, $messageCount, $batchMessageId) = explode(self::SEPARATOR, $footerLine);

        if ($message->getSubMessagesCount() !== (int)$messageCount && $this->strictValidation === true) {
            throw new EDIException(
                sprintf(
                    'Message verification count (%d) and number of parsed messages (%d) do not match!',
                    $messageCount,
                    $message->getSubMessagesCount()
                )
            );
        }
    }

    /**
     * @param $headerLine
     * @param Message $message
     * @return Message
     * @throws EDIException
     */
    protected function getSubMessageByMessageHeader($headerLine, Message $message)
    {
        list($rowIdentifier, $messageId, $messageInfo) = explode(self::SEPARATOR, $headerLine);

        // TODO: implement different parsing based on $ediFormatType and $ediFormatVersion
        $messageInfoParts = explode(self::HEADER_SEPARATOR, $messageInfo);

        $ediFormatType = $messageInfoParts[0] ?? null;
        $ediFormatVersion = $messageInfoParts[1] ?? null;
        $ediFormatVersion2 = $messageInfoParts[2] ?? null;
        $messageDirection = $messageInfoParts[3] ?? 'IN';
        $messageType = $messageInfoParts[4] ?? Messages::PROLONGATION;

        switch ($messageType) {
            case self::MESSAGE_TYPE_CONTRACT:
                $label = Messages::CONTRACT_MESSAGE;
                break;
            case self::MESSAGE_TYPE_PROLONGATION:
                $label = Messages::PROLONGATION;
                break;
            case self::MESSAGE_TYPE_MUTATION:
                $label = Messages::MUTATION;
                break;
            default:
                if ($this->strictValidation === true) {
                    throw new EDIException('Could not determine message type');
                } else {
                    $label = Messages::PROLONGATION;
                }
        }

        $subMessage = $this->messageRepository->getByLabel($label);

        $subMessage->setSender($message->getSender());
        $subMessage->setReceiver($message->getReceiver());
        $subMessage->setDateTime($message->getDateTime());
        $subMessage->setMessageId($messageId);

        if (empty($subMessage->getMessageId())) {
            $subMessage->setMessageId(md5($headerLine));
        }

        return $subMessage;
    }

    /**
     * @param $entityLabel
     * @param array $attributesGroups
     * @return Entity
     */
    protected function processEntity($entityLabel, array $attributes): ?Entity
    {
        $entity = $this->entityRepository->getByLabel($entityLabel);

        foreach ($attributes as $attributeLabel => $value) {
            if (($attribute = $this->processAttribute($entityLabel, $attributeLabel, $value)) instanceof Attribute) {
                $entity->addAttribute($attribute);
            }
        }

        return $entity;
    }

    /**
     * @param $entityLabel
     * @param $attributeLabel
     * @param $value
     * @return Attribute
     */
    protected function processAttribute($entityLabel, $attributeLabel, $value): ?Attribute
    {
        return $this->attributeRepository->getByLabel(sprintf('%s_%s', $entityLabel, $attributeLabel), $value);
    }

}
