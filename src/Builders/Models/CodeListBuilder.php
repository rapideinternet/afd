<?php

namespace SIVI\AFD\Builders\Models;

use SIVI\AFD\Builders\Builds;
use SIVI\AFD\Models\CodeList\CodeList;

class CodeListBuilder implements Builds
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var array
     */
    private $values;
    /**
     * @var null
     */
    private $description;
    /**
     * @var bool
     */
    private $external;

    /**
     * AttributeBuilder constructor.
     * @param $label
     * @param array $values
     * @param null $description
     * @param bool $external
     */
    public function __construct($label, array $values, $description = null, $external = false)
    {
        $this->label = $label;
        $this->values = $values;
        $this->description = $description;
        $this->external = $external;
    }

    /**
     * @return CodeList
     */
    public function build()
    {
        $codeList = new CodeList;
        $codeList->setLabel($this->label);
        $codeList->setDescription($this->description);
        $codeList->setValues($this->values);
        $codeList->setExternal($this->external);

        return $codeList;
    }
}