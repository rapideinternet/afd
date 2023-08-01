<?php

namespace SIVI\AFD\Repositories\JSON;

use SIVI\AFD\Exceptions\FileNotFoundException;
use SIVI\AFD\Models\CodeList\CodeList;
use SIVI\AFD\Models\Entity;

class EntityRepository implements \SIVI\AFD\Repositories\Contracts\EntityRepository
{
    protected $file;

    /**
     * AttributeRepository constructor.
     *
     * @param null $file
     * @param $valuePath
     */
    public function __construct($file = null)
    {
        $this->file = $file ?? __DIR__ . '/../../../data/JSON/entities.json';
    }

    /**
     * @param $label
     */
    public function instantiateObject($label): Entity
    {
        $class = Entity::typeMap()[strtoupper($label)] ?? null;

        if ($class !== null) {
            $entity = new $class($label);
        } else {
            $entity = new Entity($label);
        }

        return $entity;
    }

    /**
     * @param $label
     *
     * @throws FileNotFoundException
     *
     * @return CodeList
     */
    public function getByLabel($label): Entity
    {
        $entity = $this->instantiateObject($label);

        //Enrich entity with json data
        $data = $this->getObjectData($label);
        $this->mapDataToObject($entity, $data);

        return $entity;
    }

    /**
     * @throws FileNotFoundException
     *
     * @return array
     */
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
        }

        throw new FileNotFoundException(sprintf('Could not find entities.json file'));
    }

    protected function mapDataToObject(Entity $entity, array $data): Entity
    {
        if (isset($data['Omschrijving'])) {
            $entity->setDescription($data['Omschrijving']);
        }

        if (isset($data['Toelichting'])) {
            $entity->setExplanation($data['Toelichting']);
        }

        return $entity;
    }
}
