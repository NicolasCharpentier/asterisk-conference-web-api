<?php

namespace Spitchee\Service\Entity;

use Ramsey\Uuid\Uuid;
use Spitchee\Entity\User;
use Spitchee\Entity\UserTemp;
use Spitchee\Service\Generic\BaseEntityService;

/**
 * Class UserService
 * @package Spitchee\Service\Entity
 */
class UserService extends BaseEntityService
{
    /**
     * @param $role
     * @param null $nickname
     * @param bool $withSipAccount
     * @param bool $save
     * @return UserTemp
     */
    public function createTempUser($role, $nickname = null, $withSipAccount = false, $save = true)
    {
        $user = new UserTemp($role, $this->createUuid($role), $nickname);
        //if (in_array($role, self::getSipRoles())) {
        //    $sipAccount = $this->getContainer()->getSipAccountService()->createSipAccount($user);
        //    if ($save) {
        //        $this->persist($sipAccount);
        //    }
        //}
        if ($withSipAccount) {
            $this->getContainer()->getSipAccountService()->createSipAccount($user, false);
        }
        
        if ($save) {
            $this->persist($user);
            $this->persist($user->getSipAccount());
            $this->flush();
        }
        
        return $user; 
    }

    /**
     * @param User $user
     * @return bool
     */
    public function registerWannaTalk(User $user)
    {
        if (true === $user->wannaTalk() or null === $user->getActiveConference()) {
            return false;
        }

        $user->registerWannaTalk();
        $this->persist($user);
        $this->flush();

        $this->getContainer()->getRabbitPublisherService()->publishAsks(
            $user->getActiveConference(),
            $this->getContainer()->getRepositoryService()->getUserRepository()
        );

        return true;
    }

    /**
     * @return array
     */
    static public function getAvailableRoles() {
        return [
            User::ROLE_CONFERENCIER,
            User::ROLE_PUBLIC,
            User::ROLE_HP
        ];
    }

    /**
     * @return array
     */
    static private function getSelfRegistrableRoles() {
        return [
            User::ROLE_CONFERENCIER,
            User::ROLE_PUBLIC
        ];
    }

    /**
     * @return array
     */
    static public function getSipRoles() {
        return [
            User::ROLE_PUBLIC,
            User::ROLE_HP
        ];
    }

    /**
     * @return array
     */
    static private function getShortedRoles() {
        return [
            User::ROLE_HP
        ];
    }

    /**
     * @param $role
     * @return bool
     */
    static public function isValidRole($role) {
        return $role !== null and in_array($role, self::getAvailableRoles());
    }

    /**
     * @param $role
     * @return bool
     */
    static public function isSelfRegistrableRole($role) {
        return self::isValidRole($role) and in_array($role, self::getSelfRegistrableRoles());
    }

    /**
     * @param $role
     * @return bool
     */
    static public function isSipRole($role) {
        return self::isValidRole($role) and in_array($role, self::getSipRoles());
    }

    /**
     * @param $role
     * @return \Ramsey\Uuid\UuidInterface|string
     */
    private function createUuid($role) {
        if (! in_array($role, self::getShortedRoles())) {
            return Uuid::uuid4();
        }

        $id = substr(Uuid::uuid4(), 0, 8);

        while (null !== $this->getContainer()->getRepositoryService()->getUserRepository()->findOneBy([
                'uuid' => $id
            ])) $id = substr(Uuid::uuid4(), 0, 8);

        return $id;
    }
    
    
}