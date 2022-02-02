<?php


namespace SIVI\AFD\Models\Entities;


use SIVI\AFD\Models\Attribute;
use SIVI\AFD\Models\Entity;

class ByEntity extends Entity
{
    protected static string $type = 'BY';
    
    protected string $label = 'BY';

    /**
     * Entity constructor.
     *
     * @param array<string, array<string|int, Attribute>> $attributes
     * @param array<string, array<string|int, Entity>> $subEntities
     */
    public function __construct(
        array $attributes = [],
        array $subEntities = [],
        ?string $description = null,
        ?string $explanation = null
    ) {
        parent::__construct('BY', $attributes, $subEntities, $description, $explanation);
    }

    /**
     * Logic for attaching an attachment
     *
     * TODO: implement
     */
    public function addFile(): void
    {
        // Add all attributes associated with file
    }
}

