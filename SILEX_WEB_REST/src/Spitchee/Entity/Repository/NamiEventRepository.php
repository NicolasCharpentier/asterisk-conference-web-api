<?php

namespace Spitchee\Entity\Repository;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Spitchee\Entity\NamiEvent;

/**
 * NamiEventRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class NamiEventRepository extends BaseRepository
{
    /**
     * @param array $args
     * @return QueryBuilder
     */
    public function getQueryBuilder($args = array())
    {
        $qb = $this
            ->createQueryBuilder('ne')
            ->select('ne', 'sa', 'c', 'u')
            ->leftJoin('ne.relatedSipAccount', 'sa')
            ->leftJoin('sa.user', 'u')
            ->leftJoin('ne.relatedConference', 'c')
        ;
        
        $this->buildSearches('ne', $args, $qb, ['id', 'type', 'created']);
        $this->buildSorts('ne', $args, $qb, 'created');

        if (isset($args['userId'])) {
            $qb->andWhere($qb->expr()->like('u.uuid', ':userId'));
            $qb->setParameter('userId', $args['userId']);
            unset($args['userId']);
        }

        if (isset($args['sipId'])) {
            $qb->andWhere($qb->expr()->like('sa.id', ':sipId'));
            $qb->setParameter('sipId', $args['sipId']);
            unset($args['sipId']);
        }

        if (isset($args['conferenceId'])) {
            $qb->andWhere($qb->expr()->like('c.uuid', ':confId'));
            $qb->setParameter('confId', $args['conferenceId']);
            unset($args['confId']);
        }

        if (isset($args['limit'])) {
            $qb->setMaxResults($args['limit']);
            unset($args['limit']);
        }

        $this->integrityCheck($args);
        
        return $qb;
    }

    /**
     * @param array $args
     * @return NamiEvent[]|Collection
     */
    public function findNamiEvents($args = array())
    {
        return $this->getQueryBuilder($args)->getQuery()->getResult();
    }

    public function findNamiEventsAsArray($args = array())
    {
        $events = $this->findNamiEvents($args);

        return array_map(function (NamiEvent $event) {
            return $event->toArray();
        }, $events);
    }
}