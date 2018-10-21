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
     * @param null $file
     * @param CodeListRepository $codeListRepository
     */
    public function __construct(CodeListRepository $codeListRepository, CodeRepository $codeRepository, $file = null)
    {
        $this->file = $file ?? __DIR__ . '/../../../data/JSON/attributes.json';
        $this->codeListRepository = $codeListRepository;
        $this->codeRepository = $codeRepository;
    }

    /**
     * @param $label
     * @throws \SIVI\AFD\Exceptions\InvalidFormatException
     */
    public function getByLabel($label, $value = null): Attribute
    {
        $type = substr($label, 3);
        $class = Attribute::typeMap()[strtoupper($type)] ?? null;

        if ($class !== null) {
            $attribute = new $class($label);
        } else {
            $attribute = new Attribute($label);
        }

        $data = $this->getObjectData($type);
        $this->mapDataToObject($attribute, $data);

//        // Get CodeList Item
//

        // Get Format

        $attribute->setValue($value);

        return $attribute;

    }

    public function getByEntity(Entity $entity)
    {
        // TODO: Implement getByEntity() method.
    }

    protected function getObjectData($key)
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

    }

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

        if (isset($data['Code']) && !empty($data['Code'])) {
            $attribute->setCode($this->codeRepository->findByCode($data['Code']));
        }

        if (isset($data['Codelijst']) && !empty($data['Codelijst'])) {
            $attribute->setCodeList($this->codeListRepository->findByLabel($data['Codelijst']));
        }

        return $attribute;
    }
}
