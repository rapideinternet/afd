<?php

declare(strict_types=1);

namespace SIVI\AFD\Tests\Parsers;

use SIVI\AFD\Models\Attribute;
use SIVI\AFD\Models\Message;
use SIVI\AFD\Parsers\XMLParser;

/**
 * @internal
 * @coversNothing
 */
final class XMLParserTest extends ParserTestCase
{
    public function testParseTransformsXmlIntoMessageHierarchy(): void
    {
        $parser     = $this->createXmlParser();
        $xmlContent = $this->loadFixture('test.xml');

        $message = $parser->parse($xmlContent);

        self::assertSame('Pakket', $message->getLabel());
        self::assertSame(md5($xmlContent), $message->getMessageId());
        self::assertSame(md5($xmlContent), $message->getMessageContentHash());
        self::assertCount(0, $message->getEntities());

        $subMessages = $message->getSubMessages();
        self::assertArrayHasKey('Mantel', $subMessages);

        /** @var Message $mantel */
        $mantel = current($subMessages['Mantel']);

        $expectedHash = md5($this->extractMantelXml($xmlContent));
        self::assertSame($expectedHash, $mantel->getMessageId());
        self::assertSame($expectedHash, $mantel->getMessageContentHash());

        $entities = $mantel->getEntities();
        self::assertArrayHasKey('AL', $entities);
        self::assertArrayHasKey('PK', $entities);

        $alEntity          = current($entities['AL']);
        $vrwrkcdAttributes = $alEntity->getAttributesByLabel('VRWRKCD');
        self::assertNotEmpty($vrwrkcdAttributes);
        $vrwrkcd = current($vrwrkcdAttributes);
        self::assertInstanceOf(Attribute::class, $vrwrkcd);
        self::assertSame(0, $vrwrkcd->getValue());

        $pkEntity         = current($entities['PK']);
        $nummerAttributes = $pkEntity->getAttributesByLabel('NUMMER');
        self::assertNotEmpty($nummerAttributes);
        $nummer = current($nummerAttributes);
        self::assertInstanceOf(Attribute::class, $nummer);
        self::assertSame('240372745385', $nummer->getValue());

        $myaandAttributes = $pkEntity->getAttributesByLabel('MYAAND');
        self::assertNotEmpty($myaandAttributes);
        $myaand = current($myaandAttributes);
        self::assertInstanceOf(Attribute::class, $myaand);
        self::assertSame('V755', $myaand->getValue());
    }

    public function testParseExtractsAttachmentDataFromBySubEntity(): void
    {
        $parser     = $this->createXmlParser();
        $xmlContent = $this->loadFixture('test.xml');

        $message = $parser->parse($xmlContent);

        $subMessages = $message->getSubMessages();
        self::assertArrayHasKey('Mantel', $subMessages);

        /** @var Message $mantel */
        $mantel = current($subMessages['Mantel']);

        $entities = $mantel->getEntities();
        self::assertArrayHasKey('AL', $entities);

        /** @var \SIVI\AFD\Models\Entity $alEntity */
        $alEntity    = current($entities['AL']);
        $subEntities = $alEntity->getSubEntities();

        self::assertArrayHasKey('BY', $subEntities);
        self::assertNotEmpty($subEntities['BY']);

        /** @var \SIVI\AFD\Models\Entity $byEntity */
        $byEntity = current($subEntities['BY']);

        $expectedFilename = $this->extractByNodeValue($xmlContent, 'BY_FILNM');
        $expectedData     = $this->extractByNodeValue($xmlContent, 'BY_DATA');

        $filenameAttributes = $byEntity->getAttributesByLabel('FILNM');
        self::assertNotEmpty($filenameAttributes);
        $filenameAttribute = current($filenameAttributes);
        self::assertInstanceOf(Attribute::class, $filenameAttribute);
        self::assertSame($expectedFilename, $filenameAttribute->getValue());

        $dataAttributes = $byEntity->getAttributesByLabel('DATA');
        self::assertNotEmpty($dataAttributes);
        $dataAttribute = current($dataAttributes);
        self::assertInstanceOf(Attribute::class, $dataAttribute);
        self::assertSame($expectedData, $dataAttribute->getValue());
        self::assertSame($expectedData, $dataAttribute->getRawValue());

        $decoded = base64_decode($expectedData, true);
        self::assertNotFalse($decoded);
        self::assertStringStartsWith('%PDF', $decoded);
    }

    private function createXmlParser(): XMLParser
    {
        return new XMLParser(
            $this->createMessageRepository(),
            $this->createEntityRepository(),
            $this->createAttributeRepository()
        );
    }

    private function extractMantelXml(string $xmlContent): string
    {
        $xml = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
        self::assertNotFalse($xml, 'XML fixture failed to load.');

        $mantel = $xml->Mantel;
        self::assertInstanceOf(\SimpleXMLElement::class, $mantel, 'Mantel node missing from XML.');

        $serialised = $mantel->asXML();
        self::assertNotFalse($serialised, 'Mantel node could not be serialised.');

        return $serialised;
    }

    private function extractByNodeValue(string $xmlContent, string $nodeName): string
    {
        $xml = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
        self::assertNotFalse($xml, 'XML fixture failed to load.');

        $byNode = $xml->Mantel->AL->BY;
        self::assertInstanceOf(\SimpleXMLElement::class, $byNode, 'BY node missing from XML.');
        self::assertTrue(isset($byNode->{$nodeName}), sprintf('Node %s missing from BY entity.', $nodeName));

        return (string)$byNode->{$nodeName};
    }
}
