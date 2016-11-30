<?php

namespace Spitchee\Service\Generic;

/**
 * Class BaseEntityService
 * @package Spitchee\Service\Generic
 */
class BaseEntityService extends ContainerAwareService
{
    /**
     * @param $entity
     */
    protected function persist($entity) {
        if (null !== $entity)
            $this->getContainer()->getEntityManager()->persist($entity);
    }

    /**
     * @param null $entity
     */
    protected function flush($entity = null) {
        $this->getContainer()->getEntityManager()->flush($entity);
    }

    /**
     * @param $entity
     */
    protected function remove($entity) {
        if (null !== $entity)
            $this->getContainer()->getEntityManager()->remove($entity);
    }
}