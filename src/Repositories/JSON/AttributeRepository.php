<?php

namespace SIVI\AFD\Repositories\JSON;


use SIVI\AFD\Builders\Models\AttributeBuilder;
use SIVI\AFD\Models\Entity;
use SIVI\AFD\Models\Formats\Format;
use SIVI\AFD\Repositories\Contracts\CodeListRepository;
use SIVI\AFD\Repositories\Contracts\CodeRepository;

class AttributeRepository implements \SIVI\AFD\Repositories\Contracts\AttributeRepository
{
    /**
     * @var null
     */
    private $file;
    /**
     * @var CodeListRepository
     */
    private $codeListRepository;
    /**
     * @var CodeRepository
     */
    private $codeRepository;

    /**
     * AttributeRepository constructor.
     * @param null $file
     * @param CodeListRepository $codeListRepository
     */
    public function __construct($file, CodeListRepository $codeListRepository, CodeRepository $codeRepository)
    {
        $this->file = $file;
        $this->codeListRepository = $codeListRepository;
        $this->codeRepository = $codeRepository;
    }

    /**
     * @param $label
     * @throws \SIVI\AFD\Exceptions\InvalidFormatException
     */
    public function getByLabel($label)
    {
        // Get from attributes list
        $json = json_decode(
            '{
                "Label": "BRANCHE",
                "Formaat": "AN..3",
                "Code": "",
                "Codelijst": "ADNBRA",
                "Omschrijving": "ADN branchecode",
                "Toelichting": "Classificering van het soort verzekeringscontract. B.v. te  gebruiken voor dooradressering binnen een verzekeringsmaatschappij."
              }', true);

        // Get CodeList Item
        $codeList = $this->codeListRepository->findByLabel($json['Codelijst']);

        $code = $this->codeListRepository->findByLabel($json['Code']);

        // Get Format
        $format = new Format($json['Formaat']);

        //Build
        $builder = new AttributeBuilder($json['Label']);

    }

    public function getByEntity(Entity $entity)
    {
        // TODO: Implement getByEntity() method.
    }
}