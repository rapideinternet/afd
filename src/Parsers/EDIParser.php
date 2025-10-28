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
    public const SEPARATOR             = '+';
    public const EOL                   = '\'';
    public const ENTITY                = 'ENT';
    public const ATTRIBUTE             = 'LBW';
    public const SERVICE_STRING_ADVICE = 'UNA';
    public const INTERCHANGE_HEADER    = 'UNB';
    public const MESSAGE_HEADER        = 'UNH';
    public const MESSAGE_TRAILER       = 'UNT';
    public const INTERCHANGE_TRAILER   = 'UNZ';
    public const INSURANCE_DETAILS     = 'PP';

    public const HEADER_DATE_TIME_FORMAT = 'ymd:Hi';
    public const HEADER_SEPARATOR        = ':';

    public const MESSAGE_TYPE_CONTRACT     = 'PBI';
    public const MESSAGE_TYPE_PROLONGATION = 'PPR';
    public const MESSAGE_TYPE_MUTATION     = 'PMB';

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
     * @param callback(Message):void $callback
     * @throws EDIException
     */
    public function stream(string $ediContent, callable $callback): void
    {
        $this->verifyThatStringContainsEDIFact($ediContent);

        $message = $this->messageRepository->getByLabel(Messages::BATCH);
        $messageContentHash = md5($ediContent);
        $message->setMessageContentHash($messageContentHash);

        $this->processSegments(
            $this->iterateSegments($ediContent),
            $message,
            $messageContentHash,
            function (Message $batchMessage, Message $subMessage) use ($callback): void {
                $clonedMessage = clone $batchMessage;
                $clonedMessage->addSubmessage($subMessage);
                $callback($clonedMessage);
            }
        );
    }

    /**
     * @throws EDIException
     */
    public function parse(string $ediContent): Message
    {
        $this->verifyThatStringContainsEDIFact($ediContent);

        $message = $this->messageRepository->getByLabel(Messages::BATCH);
        $messageContentHash = md5($ediContent);
        $message->setMessageContentHash($messageContentHash);

        $this->processSegments(
            $this->iterateSegments($ediContent),
            $message,
            $messageContentHash,
            static function (Message $batchMessage, Message $subMessage): void {
                $batchMessage->addSubmessage($subMessage);
            }
        );

        return $message;
    }

    protected function verifyThatStringContainsEDIFact($string)
    {
        $needles = [self::SERVICE_STRING_ADVICE, self::INTERCHANGE_HEADER];

        foreach ($needles as $needle) {
            if ($needle !== '' && substr($string, 0, strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        $formattedString = utf8_encode(substr($string, 0, 150));

        throw new EDIException(sprintf('Provided string does not start with EDIFACT header: "%s"', $formattedString));
    }

    /**
     * @param $headerLine
     */
    protected function parseHeader($headerLine, Message &$message)
    {
        [
            $rowIdentifier,
            $characterSet,
            $sender,
            $receiver,
            $rawDateTime,
            $batchMessageId
            ] = explode(self::SEPARATOR, $headerLine);

        $dateTime = Carbon::createFromFormat(self::HEADER_DATE_TIME_FORMAT, $rawDateTime);

        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setDateTime($dateTime);
        $message->setMessageId($batchMessageId);
    }

    /**
     * @param $footerLine
     *
     * @throws EDIException
     */
    protected function parseFooter($footerLine, Message $message)
    {
        [$rowIdentifier, $messageCount, $batchMessageId] = explode(self::SEPARATOR, $footerLine);

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

    protected function appendHashToMessageId(?string $messageId, string $hash): string
    {
        $messageId = $messageId ?? '';

        if ($messageId === '') {
            return $hash;
        }

        return sprintf('%s-%s', $messageId, $hash);
    }

    /**
     * @param $headerLine
     *
     * @throws EDIException
     *
     * @return Message
     */
    protected function getSubMessageByMessageHeader($headerLine, Message $message)
    {
        [$rowIdentifier, $messageId, $messageInfo] = explode(self::SEPARATOR, $headerLine);

        // TODO: implement different parsing based on $ediFormatType and $ediFormatVersion
        $messageInfoParts = explode(self::HEADER_SEPARATOR, $messageInfo);

        $ediFormatType     = $messageInfoParts[0] ?? null;
        $ediFormatVersion  = $messageInfoParts[1] ?? null;
        $ediFormatVersion2 = $messageInfoParts[2] ?? null;
        $messageDirection  = $messageInfoParts[3] ?? 'IN';
        $messageType       = $messageInfoParts[4] ?? Messages::PROLONGATION;

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
                }
                    $label = Messages::PROLONGATION;
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
     *
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
     *
     * @return Attribute
     */
    protected function processAttribute($entityLabel, $attributeLabel, $value): ?Attribute
    {
        return $this->attributeRepository->getByLabel(sprintf('%s_%s', $entityLabel, $attributeLabel), $value);
    }

    /**
     * @param iterable<string> $segments
     * @param callable(Message, Message):void $finalizeSubMessage
     */
    private function processSegments(iterable $segments, Message $message, string $messageContentHash, callable $finalizeSubMessage): void
    {
        // TODO: incorporate this in parsing of data
        $allowedSpecialCharacters = '';
        $entityCode               = null;
        /** @var Message|null $subMessage */
        $subMessage       = null;
        $orderNumber      = 0;
        $entityAttributes = [];
        $collectingMessage = false;
        $subMessageHash = null;

        foreach ($segments as $segment) {
            $rowIdentifier = substr($segment, 0, 3);

            if ($rowIdentifier === self::SERVICE_STRING_ADVICE) {
                $allowedSpecialCharacters = substr($segment, 3);
            } elseif ($rowIdentifier === self::INTERCHANGE_HEADER) {
                $this->parseHeader($segment, $message);

                if (empty($message->getMessageId())) {
                    $message->setMessageId($messageContentHash);
                }
            } elseif ($rowIdentifier === self::INTERCHANGE_TRAILER) {
                $this->parseFooter($segment, $message);
            }

            if ($rowIdentifier === self::MESSAGE_HEADER) {
                $collectingMessage = true;
                $entityAttributes  = [];
                $subMessage        = $this->getSubMessageByMessageHeader($segment, $message);
                $subMessageHash    = hash_init('md5');
                hash_update($subMessageHash, $segment . self::EOL);

                continue;
            }

            if ($collectingMessage && $subMessageHash !== null) {
                hash_update($subMessageHash, $segment . self::EOL);
            }

            if ($rowIdentifier === self::MESSAGE_TRAILER) {
                if ($subMessage instanceof Message) {
                    if ($entityCode !== null && count($entityAttributes)) {
                        if (($entity = $this->processEntity($entityCode, $entityAttributes)) instanceof Entity) {
                            $subMessage->addEntity($entity, $orderNumber);
                        }
                    }

                    $entityAttributes    = [];
                    $collectingMessage   = false;

                    if ($subMessageHash !== null) {
                        $contentHash = hash_final($subMessageHash);
                        $subMessage->setMessageContentHash($contentHash);
                        $subMessage->setMessageId(
                            $this->appendHashToMessageId($subMessage->getMessageId(), $contentHash)
                        );
                        $subMessageHash = null;
                    }

                    $finalizeSubMessage($message, $subMessage);
                }

                $subMessage = null;
                $entityCode = null;

                continue;
            }

            if ($rowIdentifier === self::ENTITY) {
                if ($subMessage instanceof Message && count($entityAttributes) && $entityCode !== null) {
                    if (($entity = $this->processEntity($entityCode, $entityAttributes)) instanceof Entity) {
                        $subMessage->addEntity($entity, $orderNumber);
                    }
                    $entityAttributes = [];
                }

                $parts = explode(self::SEPARATOR, $segment);
                $entityCode  = $parts[1] ?? null;
                $orderNumber = $parts[2] ?? null;

                continue;
            }

            if ($rowIdentifier === self::ATTRIBUTE) {
                $attributeRow = explode(self::SEPARATOR, $segment);
                $code         = $attributeRow[1] ?? null;

                if ($code !== null) {
                    $value = $attributeRow[2] ?? null;
                    $entityAttributes[$code] = $value;
                }

                continue;
            }
        }
    }

    /**
     * @return \Generator<string>
     */
    private function iterateSegments(string $ediContent): \Generator
    {
        if ($ediContent === '') {
            return;
        }

        $segment = strtok($ediContent, self::EOL);

        while ($segment !== false) {
            $line = trim($segment, "\r\n");

            if ($line !== '') {
                yield $line;
            }

            $segment = strtok(self::EOL);
        }
    }
}
