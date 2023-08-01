<?php

namespace SIVI\AFD\Processors\Entity;

use SIVI\AFD\Models\Entity;
use SIVI\AFD\Processors\Entity\Contracts\EntityProcessor as EntityProcessorContract;
use SIVI\AFD\Resolvers\Contracts\EntityImplementationResolver;

class EntityProcessor implements EntityProcessorContract
{
    /**
     * @var EntityImplementationResolver
     */
    protected $entityImplementationResolver;

    /**
     * EntityProcessor constructor.
     */
    public function __construct(EntityImplementationResolver $entityImplementationResolver)
    {
        $this->entityImplementationResolver = $entityImplementationResolver;
    }

    public function process(Entity $entity): Entity
    {
        /** @var Entity $implementation */
        foreach ($this->entityImplementationResolver->getGetEntityImplementations() as $implementation) {
            if ($implementation::matchEntity($entity)) {
                return $this->transferEntity($entity, $implementation);
            }
        }

        return $entity;
    }

    protected function transferEntity(Entity $from, $to)
    {
        /** @var Entity $entity */
        $entity = new $to($from->getLabel(),
            $from->getAttributes(), [],
            $from->getDescription(), $from->getExplanation());

        foreach ($from->getSubEntities() as $subEntity) {
            $entity->addSubEntity($this->process($subEntity));
        }

        return $entity;
    }
}
