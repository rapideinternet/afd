<?php

namespace SIVI\AFD\Repositories\Model;

use SIVI\AFD\Exceptions\NotFoundException;
use SIVI\AFD\Models\Codes\Code;

class CodeRepository implements \SIVI\AFD\Repositories\Contracts\CodeRepository
{
    /**
     * @var string
     */
    protected $delimiter;

    /**
     * CodeRepository constructor.
     *
     * @param string $delimiter
     */
    public function __construct($delimiter = '.')
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @param $code
     *
     * @throws NotFoundException
     */
    public function instantiateObject($code): Code
    {
        $class = Code::codeMap()[strtoupper($code)]
            ?? Code::codeMap()[strtoupper($code[0])];

        if ($class !== null) {
            return $class::$variableLength
                ? new $class($code, $this->delimiter)
                : new $class($code);
        }

        throw new NotFoundException();
    }

    /**
     * @param $code
     *
     * @throws NotFoundException
     *
     * @return Code
     */
    public function findByCode($code): ?Code
    {
        return $this->instantiateObject($code);
    }

    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }
}
