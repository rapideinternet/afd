<?php

namespace SIVI\AFD\Repositories\JSON;

use SIVI\AFD\Exceptions\FileNotFoundException;
use SIVI\AFD\Models\CodeList\CodeList;

class CodeListRepository implements \SIVI\AFD\Repositories\Contracts\CodeListRepository
{
    /**
     * @var string
     */
    protected $file;
    /**
     * @var string
     */
    protected $valuePath;

    /**
     * @var array<string, array>|null
     */
    protected $codeListData;

    /**
     * @var array<string, array>
     */
    protected $valuesCache = [];

    /**
     * AttributeRepository constructor.
     *
     * @param null $file
     * @param $valuePath
     */
    public function __construct($file = null, $valuePath = null)
    {
        $this->file      = $file      ?? __DIR__ . '/../../../data/JSON/codeList.json';
        $this->valuePath = $valuePath ?? __DIR__ . '/../../../data/JSON/CodeList';
    }

    /**
     * {@inheritDoc}
     */
    public function instantiateObject($label): CodeList
    {
        return new CodeList($label);
    }

    /**
     * {@inheritDoc}
     */
    public function findByLabel($label): ?CodeList
    {
        $codeList = $this->instantiateObject($label);

        $data = $this->getObjectData($label);
        $this->mapDataToObject($codeList, $data);

        return $codeList;
    }

    /**
     * @throws FileNotFoundException
     */
    protected function getObjectData($key): array
    {
        $data = $this->loadCodeListData();

        if (!isset($data[$key])) {
            return [];
        }

        return $data[$key];
    }

    /**
     * @throws FileNotFoundException
     *
     * @return array<string, array>
     */
    protected function loadCodeListData(): array
    {
        if ($this->codeListData !== null) {
            return $this->codeListData;
        }

        if (!file_exists($this->file)) {
            throw new FileNotFoundException(sprintf('Could not find codeList.json file'));
        }

        $json               = json_decode(file_get_contents($this->file), true);
        $this->codeListData = [];

        if (is_array($json)) {
            foreach ($json as $item) {
                if (isset($item['Label'])) {
                    $this->codeListData[$item['Label']] = $item;
                }
            }
        }

        return $this->codeListData;
    }

    /**
     * @param $data
     *
     * @throws FileNotFoundException
     */
    protected function mapDataToObject(CodeList $codeList, $data): CodeList
    {
        if (isset($data['Omschrijving'])) {
            $codeList->setDescription($data['Omschrijving']);
        }

        $codeList->setValues($this->getValues($codeList->getLabel()));

        return $codeList;
    }

    /**
     * @param $label
     *
     * @throws FileNotFoundException
     *
     * @return array
     */
    protected function getValues($label)
    {
        if (isset($this->valuesCache[$label])) {
            return $this->valuesCache[$label];
        }

        $path = sprintf('%s/%s.json', $this->valuePath, strtoupper($label));

        if (!file_exists($path)) {
            return $this->valuesCache[$label] = [];
        }

        $values = json_decode(file_get_contents($path), true);

        //Format values
        $map = [];

        foreach ($values as $item) {
            $map[$item['Code']] = $item['Omschrijving'];
        }

        return $this->valuesCache[$label] = $map;
    }
}
