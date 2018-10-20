<?php

namespace SIVI\AFD\Repositories\JSON;

use SIVI\AFD\Builders\Models\CodeListBuilder;
use SIVI\AFD\Exceptions\FileNotFoundException;
use SIVI\AFD\Models\CodesList\CodeList;

class CodeListRepository implements \SIVI\AFD\Repositories\Contracts\CodeListRepository
{
    /**
     * @var null
     */
    private $file;
    private $valuePath;

    /**
     * AttributeRepository constructor.
     * @param null $file
     * @param $valuePath
     */
    public function __construct($file, $valuePath)
    {
        $this->file = $file;
        $this->valuePath = $valuePath;
    }

    /**
     * @param $label
     * @return CodeList
     * @throws FileNotFoundException
     */
    public function findByLabel($label): CodeList
    {
        //Get the data form the codelist json
        $label = 'ADNBRA';
        $description = 'ADN branchecode';
        $external = false;

        //Get the values from the values file
        $values = $this->getValues($label);

        //Build
        $builder = new CodeListBuilder($label, $values, $description, $external);

        //Return the model
        return $builder->build();
    }

    /**
     * @param $label
     * @throws FileNotFoundException
     */
    protected function getValues($label)
    {
        $path = sprintf('%s/%s.json', $this->valuePath, $label);

        if (!file_exists($path)) {
            throw new FileNotFoundException(sprintf('Could not find json file for label %s', $label));
        }

        $values = json_decode(file_get_contents($path), true);

        //Format values
        $map = [];
        foreach ($values as $item) {
            $map[$item['Lijst']] = $item['Omschrijving'];
        }

        return $map;
    }
}