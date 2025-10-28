<?php

declare(strict_types=1);

namespace SIVI\AFD\Tests\Parsers;

use SIVI\AFD\Models\Attribute;
use SIVI\AFD\Models\Message;
use SIVI\AFD\Parsers\EDIParser;

final class EDIParserTest extends ParserTestCase
{
    public function testParseBuildsBatchMessageWithExpectedEntities(): void
    {
        $parser = $this->createEdiParser();
        $ediContent = $this->loadFixture('test.edifact');

        $message = $parser->parse($ediContent);

        self::assertSame('Batch', $message->getLabel());
        self::assertSame('EMS8888940000349', $message->getSender());
        self::assertSame('9001618', $message->getReceiver());
        self::assertSame('348', $message->getMessageId());
        self::assertSame(md5($ediContent), $message->getMessageContentHash());
        self::assertSame(1, $message->getSubMessagesCount());

        $subMessages = $message->getSubMessages();
        self::assertArrayHasKey('Prolongatie', $subMessages);

        /** @var Message $subMessage */
        $subMessage = current($subMessages['Prolongatie']);
        $expectedSubMessageHash = $this->calculateFirstMessageHash($ediContent);

        self::assertSame($expectedSubMessageHash, $subMessage->getMessageContentHash());
        self::assertSame('348-' . $expectedSubMessageHash, $subMessage->getMessageId());

        $entities = $subMessage->getEntities();
        self::assertArrayHasKey('AL', $entities);
        self::assertArrayHasKey('PK', $entities);

        $alEntity = current($entities['AL']);
        $trrefAttributes = $alEntity->getAttributesByLabel('TRREF');
        self::assertNotEmpty($trrefAttributes);
        /** @var Attribute $trackingReference */
        $trackingReference = current($trrefAttributes);
        self::assertSame('0000AA-000999XYZ', $trackingReference->getValue());

        $pkEntity = current($entities['PK']);
        $aantalPolissenAttributes = $pkEntity->getAttributesByLabel('AANTPOL');
        self::assertNotEmpty($aantalPolissenAttributes);
        $aantalPolissen = current($aantalPolissenAttributes);
        self::assertInstanceOf(Attribute::class, $aantalPolissen);
        self::assertSame(1.0, $aantalPolissen->getValue());
    }

    public function testStreamInvokesCallbackWithSameMessageStructure(): void
    {
        $parser = $this->createEdiParser();
        $ediContent = $this->loadFixture('test.edifact');

        $streamedMessages = [];
        $parser->stream(
            $ediContent,
            static function (Message $message) use (&$streamedMessages): void {
                $streamedMessages[] = $message;
            }
        );

        self::assertCount(1, $streamedMessages);

        $streamedMessage = $streamedMessages[0];
        $parsedMessage = $this->createEdiParser()->parse($ediContent);

        self::assertSame($parsedMessage->getLabel(), $streamedMessage->getLabel());
        self::assertSame($parsedMessage->getMessageContentHash(), $streamedMessage->getMessageContentHash());
        self::assertSame($parsedMessage->getMessageId(), $streamedMessage->getMessageId());
        self::assertSame($parsedMessage->getSubMessagesCount(), $streamedMessage->getSubMessagesCount());

        $parsedSubMessages = $parsedMessage->getSubMessages();
        $streamedSubMessages = $streamedMessage->getSubMessages();

        self::assertArrayHasKey('Prolongatie', $parsedSubMessages);
        self::assertArrayHasKey('Prolongatie', $streamedSubMessages);

        $parsedSubMessage = current($parsedSubMessages['Prolongatie']);
        $streamedSubMessage = current($streamedSubMessages['Prolongatie']);

        self::assertSame($parsedSubMessage->getMessageId(), $streamedSubMessage->getMessageId());
        self::assertSame(
            $parsedSubMessage->getMessageContentHash(),
            $streamedSubMessage->getMessageContentHash()
        );
    }

    public function testStreamMatchesParseForLargePayload(): void
    {
        $parser = $this->createEdiParser();
        $ediContent = $this->createLargeEdiFixture();

        $parsedMessage = $parser->parse($ediContent);

        $streamedMessages = [];
        $parser->stream(
            $ediContent,
            static function (Message $message) use (&$streamedMessages): void {
                $streamedMessages[] = $message;
            }
        );

        self::assertCount(1, $streamedMessages);

        $streamedMessage = $streamedMessages[0];

        self::assertSame($parsedMessage->getMessageContentHash(), $streamedMessage->getMessageContentHash());
        self::assertSame($parsedMessage->getSubMessagesCount(), $streamedMessage->getSubMessagesCount());

        $parsedSubMessages = $parsedMessage->getSubMessages();
        $streamedSubMessages = $streamedMessage->getSubMessages();

        self::assertArrayHasKey('Prolongatie', $parsedSubMessages);
        self::assertArrayHasKey('Prolongatie', $streamedSubMessages);

        $parsedSubMessage = current($parsedSubMessages['Prolongatie']);
        $streamedSubMessage = current($streamedSubMessages['Prolongatie']);

        self::assertSame($parsedSubMessage->getMessageId(), $streamedSubMessage->getMessageId());
        self::assertSame(
            $parsedSubMessage->getMessageContentHash(),
            $streamedSubMessage->getMessageContentHash()
        );
    }

    public function testParseAndStreamHandleMultipleSubMessages(): void
    {
        $parser = $this->createEdiParser();
        $ediContent = $this->createBatchFixtureWithMessages(50);

        $parsedMessage = $parser->parse($ediContent);

        self::assertSame('Batch', $parsedMessage->getLabel());
        self::assertSame(50, $parsedMessage->getSubMessagesCount());
        self::assertSame(md5($ediContent), $parsedMessage->getMessageContentHash());

        $subMessages = $parsedMessage->getSubMessages();
        self::assertArrayHasKey('Prolongatie', $subMessages);
        self::assertCount(50, $subMessages['Prolongatie']);

        $hashes = array_map(
            static function (Message $message): string {
                return $message->getMessageContentHash();
            },
            $subMessages['Prolongatie']
        );

        $ids = array_map(
            static function (Message $message): string {
                return $message->getMessageId();
            },
            $subMessages['Prolongatie']
        );

        $streamedHashes = [];
        $streamedIds = [];
        $expectedBatchHash = $parsedMessage->getMessageContentHash();

        $parser->stream(
            $ediContent,
            static function (Message $message) use (&$streamedHashes, &$streamedIds, $expectedBatchHash): void {
                self::assertSame($expectedBatchHash, $message->getMessageContentHash());

                $subMessages = $message->getSubMessages();
                self::assertArrayHasKey('Prolongatie', $subMessages);
                self::assertCount(1, $subMessages['Prolongatie']);

                /** @var Message $subMessage */
                $subMessage = current($subMessages['Prolongatie']);
                $streamedHashes[] = $subMessage->getMessageContentHash();
                $streamedIds[]    = $subMessage->getMessageId();

                self::assertSame(md5($message->getMessageContentHash()), md5($message->getMessageContentHash()));
            }
        );

        self::assertSame($hashes, $streamedHashes);
        self::assertSame($ids, $streamedIds);
    }

    public function testMessagesWithDuplicateHeaderIdsReceiveUniqueHashes(): void
    {
        $parser = $this->createEdiParser();
        $ediContent = $this->createBatchFixtureWithDuplicateIds();

        $parsedMessage = $parser->parse($ediContent);
        $subMessages   = $parsedMessage->getSubMessages();

        self::assertArrayHasKey('Prolongatie', $subMessages);
        self::assertCount(2, $subMessages['Prolongatie']);

        $messageIds = array_map(
            static function (Message $message): string {
                return $message->getMessageId();
            },
            $subMessages['Prolongatie']
        );

        $contentHashes = array_map(
            static function (Message $message): string {
                return $message->getMessageContentHash();
            },
            $subMessages['Prolongatie']
        );

        self::assertCount(2, array_unique($contentHashes));
        self::assertCount(2, array_unique($messageIds));

        foreach ($messageIds as $id) {
            self::assertStringStartsWith('348-', $id);
        }
    }

    private function createEdiParser(): EDIParser
    {
        return new EDIParser(
            $this->createMessageRepository(),
            $this->createEntityRepository(),
            $this->createAttributeRepository()
        );
    }

    private function calculateFirstMessageHash(string $ediContent): string
    {
        $collecting = false;
        $hash = null;

        $segment = strtok($ediContent, EDIParser::EOL);

        while ($segment !== false) {
            $segment = trim($segment, "\r\n");

            if ($segment === '') {
                $segment = strtok(EDIParser::EOL);
                continue;
            }

            $identifier = substr($segment, 0, 3);

            if ($identifier === EDIParser::MESSAGE_HEADER) {
                $collecting = true;
                $hash = hash_init('md5');
            }

            if ($collecting && $hash !== null) {
                hash_update($hash, $segment . EDIParser::EOL);
            }

            if ($identifier === EDIParser::MESSAGE_TRAILER) {
                break;
            }

            $segment = strtok(EDIParser::EOL);
        }

        return $hash !== null ? hash_final($hash) : md5('');
    }

    private function createLargeEdiFixture(): string
    {
        $baseContent = $this->loadFixture('test.edifact');
        $extraSegments = '';

        for ($i = 0; $i < 2000; $i++) {
            $code = sprintf('EXTRA%04d', $i);
            $value = sprintf('VALUE%04d', $i);
            $extraSegments .= sprintf("LBW+%s+%s'\n", $code, $value);
        }

        return str_replace(
            "UNT+",
            $extraSegments . "UNT+",
            $baseContent
        );
    }

    private function createBatchFixtureWithMessages(int $messageCount): string
    {
        $singleMessageContent = $this->loadFixture('test.edifact');

        $firstMessagePosition = strpos($singleMessageContent, 'UNH+');
        $unzPosition          = strrpos($singleMessageContent, 'UNZ+');

        self::assertNotFalse($firstMessagePosition, 'Could not locate UNH segment in base fixture.');
        self::assertNotFalse($unzPosition, 'Could not locate UNZ segment in base fixture.');

        $header          = substr($singleMessageContent, 0, $firstMessagePosition);
        $messageTemplate = substr($singleMessageContent, $firstMessagePosition, $unzPosition - $firstMessagePosition);
        $footer          = substr($singleMessageContent, $unzPosition);

        preg_match('/UNH\+(\d+)\+/', $messageTemplate, $messageIdMatches);
        self::assertNotEmpty($messageIdMatches, 'Could not extract message id from base fixture.');
        $baseMessageId = (int)$messageIdMatches[1];

        preg_match('/UNT\+(\d+)\+\d+/', $messageTemplate, $segmentCountMatches);
        self::assertNotEmpty($segmentCountMatches, 'Could not extract segment count from base fixture.');
        $segmentCount = (int)$segmentCountMatches[1];

        $messages = [];

        for ($index = 0; $index < $messageCount; $index++) {
            $messageId = (string)($baseMessageId + $index);

            $message = preg_replace('/UNH\+\d+\+/', 'UNH+' . $messageId . '+', $messageTemplate, 1);
            $message = preg_replace('/UNT\+\d+\+\d+/', 'UNT+' . $segmentCount . '+' . $messageId, $message, 1);

            $messages[] = $message;
        }

        $footer = preg_replace('/UNZ\+\d+\+/', 'UNZ+' . $messageCount . '+', $footer, 1);

        return $header . implode('', $messages) . $footer;
    }

    private function createBatchFixtureWithDuplicateIds(): string
    {
        $singleMessageContent = $this->loadFixture('test.edifact');

        $firstMessagePosition = strpos($singleMessageContent, 'UNH+');
        $unzPosition          = strrpos($singleMessageContent, 'UNZ+');

        self::assertNotFalse($firstMessagePosition, 'Could not locate UNH segment in base fixture.');
        self::assertNotFalse($unzPosition, 'Could not locate UNZ segment in base fixture.');

        $header          = substr($singleMessageContent, 0, $firstMessagePosition);
        $messageTemplate = substr($singleMessageContent, $firstMessagePosition, $unzPosition - $firstMessagePosition);
        $footer          = substr($singleMessageContent, $unzPosition);

        preg_match('/UNH\+(\d+)\+/', $messageTemplate, $messageIdMatches);
        self::assertNotEmpty($messageIdMatches, 'Could not extract message id from base fixture.');
        $baseMessageId = (int) $messageIdMatches[1];

        preg_match('/UNT\+(\d+)\+\d+/', $messageTemplate, $segmentCountMatches);
        self::assertNotEmpty($segmentCountMatches, 'Could not extract segment count from base fixture.');
        $segmentCount = (int) $segmentCountMatches[1];

        $messageOne = preg_replace('/UNH\+\d+\+/', 'UNH+' . $baseMessageId . '+', $messageTemplate, 1);
        $messageOne = preg_replace('/UNT\+\d+\+\d+/', 'UNT+' . $segmentCount . '+' . $baseMessageId, $messageOne, 1);

        $messageTwo = $messageTemplate;
        $messageTwo = preg_replace('/UNH\+\d+\+/', 'UNH+' . $baseMessageId . '+', $messageTwo, 1);
        $messageTwo = preg_replace('/UNT\+\d+\+\d+/', 'UNT+' . $segmentCount . '+' . $baseMessageId, $messageTwo, 1);
        $messageTwo = preg_replace("/LBW\+NUMMER\+[^']+'/", "LBW+NUMMER+DIFFERENT123'", $messageTwo, 1);

        $footer = preg_replace('/UNZ\+\d+\+/', 'UNZ+2+', $footer, 1);

        return $header . $messageOne . $messageTwo . $footer;
    }
}
