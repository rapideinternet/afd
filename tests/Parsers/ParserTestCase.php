<?php

declare(strict_types=1);

namespace SIVI\AFD\Tests\Parsers;

use PHPUnit\Framework\TestCase;
use SIVI\AFD\Models\Entity;
use SIVI\AFD\Repositories\JSON\AttributeRepository;
use SIVI\AFD\Repositories\JSON\CodeListRepository;
use SIVI\AFD\Repositories\JSON\EntityRepository;
use SIVI\AFD\Repositories\Model\CodeRepository;
use SIVI\AFD\Repositories\Model\MessageRepository;
use TypeError;

abstract class ParserTestCase extends TestCase
{
    protected function createCodeRepository(): CodeRepository
    {
        return new CodeRepository();
    }

    protected function createCodeListRepository(): CodeListRepository
    {
        return new CodeListRepository();
    }

    protected function createAttributeRepository(): AttributeRepository
    {
        return new AttributeRepository(
            $this->createCodeListRepository(),
            $this->createCodeRepository()
        );
    }

    protected function createEntityRepository(): EntityRepository
    {
        return new class extends EntityRepository {
            public function instantiateObject($label): Entity
            {
                try {
                    return parent::instantiateObject($label);
                } catch (TypeError $exception) {
                    return new Entity($label);
                }
            }
        };
    }

    protected function createMessageRepository(): MessageRepository
    {
        return new MessageRepository();
    }

    protected function loadFixture(string $filename): string
    {
        $path = __DIR__ . '/../files/' . $filename;
        $contents = file_get_contents($path);

        $this->assertNotFalse($contents, sprintf('Fixture "%s" could not be loaded.', $filename));

        return $contents;
    }
}
