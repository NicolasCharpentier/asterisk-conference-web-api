<?php

namespace Spitchee\Entity\Repository;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Spitchee\Entity\Conference;
use Spitchee\Entity\User;

/**
 * UserRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class UserRepository extends BaseRepository
{
    /**
     * @param array $args
     * @return QueryBuilder
     */
    public function getQueryBuilder($args = array())
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->select('u', 'c')
            ->leftJoin('u.activeConference', 'c')
        ;
        
        $this->buildSearches('u', $args, $qb, ['uuid', 'username', 'sipId', 'type']);
        $this->buildSorts('u', $args, $qb, 'wannaTalkSince');

        if (isset($args['identifier'])) {
            $orX = $qb->expr()->orX();
            $orX->add('u.uuid = :ident');
            $orX->add('u.username = :ident AND u INSTANCE OF :persistant_user');
            $qb ->andWhere($orX)
                ->setParameter('ident', $args['identifier'])
                ->setParameter('persistant_user', 'persistant_user');

            unset($args['identifier']);
        }

        if (isset($args['wannaTalkInConferenceId'])) {
            $qb->andWhere($qb->expr()->like('c.uuid', ':wannaTalkInConferenceId'));
            $qb->andWhere($qb->expr()->isNotNull('u.wannaTalkSince'));
            $qb->setParameter('wannaTalkInConferenceId', $args['wannaTalkInConferenceId']);

            unset($args['wannaTalkInConferenceId']);
        }

        if (isset($args['uuidNot'])) {
            $qb->andWhere($qb->expr()->notLike('u.uuid', ':uuidNot'));
            $qb->setParameter('uuidNot', $args['uuidNot']);

            unset($args['uuidNot']);
        }
        
        $this->integrityCheck($args);
        
        return $qb;
    }

    /**
     * @param array $args
     * @return User[]
     */
    public function findUsers($args = array()) 
    {
        return $this->getQueryBuilder($args)->getQuery()->getResult();
    }

    public function findWannaTalkUsersInConference(Conference $conference, $args = array())
    {
        return $this->findUsers(array_merge([
            'wannaTalkInConferenceId' => $conference->getUuid(),
            'sort_wannaTalkSince' => 'ASC'
        ], $args));
    }

    /**
     * @param $id
     * @return User|null
     */
    public function loadOneByIdentifier($id) {
        $result = null;

        try {
            $result = $this->getQueryBuilder([
                'identifier' => $id
            ])->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            $result = null;
        } catch (NonUniqueResultException $e) {
            $result = null;
        }
        
        return $result;
    }

    /**
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return object
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }
}
