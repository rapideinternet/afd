<?php

namespace SIVI\AFD\Repositories\JSON;


use SIVI\AFD\Models\Entity;
use SIVI\AFD\Models\Formats\Format;

class AttributeRepository implements \SIVI\AFD\Repositories\Contracts\AttributeRepository
{
    /**
     * @var null
     */
    private $file;

    /**
     * AttributeRepository constructor.
     * @param null $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    public function getByLabel($label)
    {
        // Get from attributes list
        $json = '  {
    "Label": "BRANCHE",
    "Formaat": "AN..3",
    "Code": "",
    "Codelijst": "ADNBRA",
    "Omschrijving": "ADN branchecode",
    "Toelichting": "Classificering van het soort verzekeringscontract. B.v. te  gebruiken voor dooradressering binnen een verzekeringsmaatschappij."
  },';

        // Get CodeList Item

        // Get Format
        $format = new Format($format);

        // Get


    }

    public function getByEntity(Entity $entity)
    {
        // TODO: Implement getByEntity() method.
    }
}