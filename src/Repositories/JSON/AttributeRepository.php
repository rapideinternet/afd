<?php

namespace SIVI\AFD\Repositories\JSON;

use SIVI\AFD\Exceptions\FileNotFoundException;
use SIVI\AFD\Exceptions\InvalidFormatException;
use SIVI\AFD\Models\Attribute;
use SIVI\AFD\Models\Entity;
use SIVI\AFD\Models\Formats\Format;
use SIVI\AFD\Repositories\Contracts\CodeListRepository;
use SIVI\AFD\Repositories\Contracts\CodeRepository;

class AttributeRepository implements \SIVI\AFD\Repositories\Contracts\AttributeRepository
{
    /**
     * @var null
     */
    protected $file;
    /**
     * @var CodeListRepository
     */
    protected $codeListRepository;
    /**
     * @var CodeRepository
     */
    protected $codeRepository;

    /**
     * AttributeRepository constructor.
     *
     * @param null $file
     */
    public function __construct(CodeListRepository $codeListRepository, CodeRepository $codeRepository, $file = null)
    {
        $this->file               = $file ?? __DIR__ . '/../../../data/JSON/attributes.json';
        $this->codeListRepository = $codeListRepository;
        $this->codeRepository     = $codeRepository;
    }

    /**
     * @param $label
     */
    public function instantiateObject($label): Attribute
    {
        $class = Attribute::typeMap()[strtoupper(Attribute::formatTypeLabel($label))] ?? null;

        if ($class !== null) {
            $attribute = new $class($label);
        } else {
            $attribute = new Attribute($label);
        }

        return $attribute;
    }

    /**
     * @param $label
     * @param null $value
     *
     * @throws FileNotFoundException
     */
    public function getByLabel($label, $value = null): Attribute
    {
        $attribute = $this->instantiateObject($label);

        $data = $this->getObjectData($attribute->getTypeLabel());
        $this->mapDataToObject($attribute, $data);

        //Set the actual value
        $attribute->setValue($value);

        return $attribute;
    }

    public function getByEntity(Entity $entity)
    {
        // TODO: Implement getByEntity() method.
    }

    /**
     * @param $key
     *
     * @throws FileNotFoundException
     */
    protected function getObjectData($key): array
    {
        if (file_exists($this->file)) {
            $json = json_decode(file_get_contents($this->file), true);

            //TODO optimize n-problem
            foreach ($json as $item) {
                if (isset($item['Label']) && $item['Label'] == $key) {
                    return $item;
                }
            }
        } else {
            throw new FileNotFoundException(sprintf('Could not find attributes.json file'));
        }

        return [];
    }

    /**
     * @param $data
     */
    protected function mapDataToObject(Attribute $attribute, $data): Attribute
    {
        if (isset($data['Omschrijving'])) {
            $attribute->setDescription($data['Omschrijving']);
        }

        if (isset($data['Toelichting'])) {
            $attribute->setExplanation($data['Toelichting']);
        }

        if (isset($data['Formaat']) && !empty($data['Formaat'])) {
            try {
                $attribute->setFormat(new Format($data['Formaat']));
            } catch (InvalidFormatException $e) {
                //Report
            }
        }

        if (isset($data['Code']) && !empty($data['Code']) && ($code = $this->codeRepository->findByCode($data['Code'])) !== null) {
            $attribute->setCode($code);
        }

        if (isset($data['Codelijst']) && !empty($data['Codelijst']) && ($codeList = $this->codeListRepository->findByLabel($data['Codelijst'])) !== null) {
            $attribute->setCodeList($codeList);
        }

        return $attribute;
    }
}
