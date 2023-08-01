<?php

namespace SIVI\AFD\Builders\Models;

use SIVI\AFD\Builders\Builds;
use SIVI\AFD\Models\Attribute;

class AttributeBuilder implements Builds
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $label;

    /**
     * AttributeBuilder constructor.
     *
     * @param $label
     * @param $value
     */
    public function __construct($label, $value = null)
    {
        $this->label = $label;
        $this->value = $value;
    }

    public function build()
    {
        return new Attribute($this->value);
    }
}
