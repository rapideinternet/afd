<?php

namespace SIVI\AFD\Services;

use Psr\Container\ContainerInterface;
use SIVI\AFD\Exceptions\NotImplementedException;
use SIVI\AFD\Parsers\Contracts\Parser;
use SIVI\AFD\Services\Contracts\ParserService as ParserServiceContract;

class ParserService implements ParserServiceContract
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /*bol
     * @var string
     */
    protected $bindingPrefix;

    /**
     * ParserService constructor.
     *
     * @param string $bindingPrefix
     */
    public function __construct(ContainerInterface $container, $bindingPrefix = 'afd.parsers')
    {
        $this->container     = $container;
        $this->bindingPrefix = $bindingPrefix;
    }

    /**
     * @param $extension
     *
     * @throws NotImplementedException
     */
    public function getParserByExtension($extension): Parser
    {
        $parser = $this->container->get($this->getNamedBinding($extension));

        if (!($parser instanceof Parser)) {
            throw new NotImplementedException(sprintf('No parser found for extension %s', $extension));
        }

        return $parser;
    }

    /**
     * @param $extension
     *
     * @return string
     */
    protected function getNamedBinding($extension)
    {
        return sprintf('%s.%s', $this->bindingPrefix, strtolower($extension));
    }
}
