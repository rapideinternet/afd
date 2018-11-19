<?php


namespace SIVI\AFD\Models\Entities;


use SIVI\AFD\Models\Entity;

class ByEntity extends Entity
{
    protected $label = 'BY';

    public function __construct(array $attributes = [], array $subEntities = [], $description = null, $explanation = null)
    {
        parent::__construct('BY', $attributes, $subEntities, $description, $explanation);
    }

    //Logic for attaching an attachment

    public function addFile(){


        //


        //Add all attributes associated with file
    }
}

