<?php

namespace Spitchee\Service\Rabbit;


use Container;
use Doctrine\Common\Collections\Collection;
use PhpAmqpLib\Message\AMQPMessage;
use Spitchee\Entity\Conference;
use Spitchee\Entity\Repository\UserRepository;
use Spitchee\Entity\User;
use Spitchee\Util\Rabbit\RabbitClient;
use Spitchee\Util\Type\ArrayUtil;

/**
 * Class RabbitPublisherService
 * @package Spitchee\Service\Rabbit
 */
class RabbitPublisherService
{
    const LECTURER_KEY  = 'lecturer';
    const PUBLIC_KEY    = 'public';

    const TYPE_CONFERENCE_STATE = 'ConferenceState';
    const TYPE_WARNING          = 'Warning';
    const TYPE_CALL_DECLINE     = 'CallDecline';
    const TYPE_USER_QTY_CHANGE  = 'OnCallNbUsers'; // @Deprecated remplacé par OnCallUsers
    const TYPE_ON_CALL_USERS    = 'OnCallUsers';
    const TYPE_ASKS             = 'Asks';

    // Futur : TYPE_TOTAL_NB_USERS --> Le nombre d'users total liés à la conférence (appel ou pas appel)

    const WARNING_SPEAKER_OFFLINE   = 'SPEAKER_OFFLINE';

    /** @var RabbitClient $client */
    private $client;

    public function __construct(Container $container)
    {
        $this->client = new RabbitClient(
            $container['config']['rabbit'],
            $container->getLogger()
        );
    }

    /**
     * @param Conference $conference
     * @param User $user
     */
    public function publishCallDecline(Conference $conference, User $user)
    {
        $this->publish($user->toArray(), $conference, self::TYPE_CALL_DECLINE);
    }

    /**
     * @param Conference $conference
     * @param User $user
     */
    public function publishCallUsersIncrement(Conference $conference, User $user)
    {
        $this->publishCallUsersQuantityChangement($conference, $user, +1);
        $this->publishCallUsersChangement($conference, $user, +1);
    }

    /**
     * @param Conference $conference
     * @param User $user
     */
    public function publishCallUsersDecrement(Conference $conference, User $user)
    {
        $this->publishCallUsersQuantityChangement($conference, $user, -1);
        $this->publishCallUsersChangement($conference, $user, -1);
    }

    /**
     * @param Conference $conference
     */
    public function publishConferenceState(Conference $conference)
    {
        $this->publish([
            'state' => Conference::$strStates[$conference->getState()]
        ], $conference, self::TYPE_CONFERENCE_STATE);
    }

    /**
     * @param Conference $conference
     * @param $warn
     */
    public function publishWarning(Conference $conference, $warn)
    {
        $this->publish([
            'warning' => $warn
        ], $conference, self::TYPE_WARNING);
    }

    /**
     * @param Conference $conference
     * @param UserRepository $userRepository
     * @param User|null $exceptThisOne
     */
    public function publishAsks(Conference $conference, UserRepository $userRepository, User $exceptThisOne = null)
    {
        // v1
        //$this->publish(array_map(function (User $user) {
        //    return $user->toArray();
        //}, $users), $conference, self::TYPE_ASKS, self::PUBLIC_KEY);


        /* v2
        $users = $userRepository->findWannaTalkUsersInConference($conference);

        // En raison d'une grosse flemme, et vu que le last user est pas flush,
        // il va tomber dans la query, on va donc le virer À MAIN NUE
        $toSendUsers = array();

        foreach ($users as $u) {
            if ($exceptThisOne and $u->getUuid() === $exceptThisOne->getUuid()) {
                continue;
            }
            $toSendUsers[] = $u->toArray();
        }
        */

        $toSendUsers = $userRepository->findWannaTalkUsersInConference($conference, [
            'uuidNot' => $exceptThisOne ? $exceptThisOne->getUuid() : null
        ]);

        $this->publish(array_map(function (User $user) {
            return $user->toArray();
        }, $toSendUsers), $conference, self::TYPE_ASKS, self::PUBLIC_KEY);
    }

    /**
     * @deprecated
     * TODO Supprimer en 2.0
     *
     * @param Conference $conference
     * @param User $user
     * @param $way
     */
    private function publishCallUsersQuantityChangement(Conference $conference, User $user, $way)
    {
        $this->publish([
            'user' => $user->toArray(),
            'nbUsers' => $conference->countOnCallAgoraUsers(),
            'way'  => $way,
        ], $conference, self::TYPE_USER_QTY_CHANGE);
    }

    /**
     *
     * @param Conference $conference
     * @param User $user
     * @param $way
     */
    private function publishCallUsersChangement(Conference $conference, User $user, $way)
    {
        $onCallUsers = $conference->getOncallUsers(User::ROLE_PUBLIC)->map(function (User $user) {
            return $user->toArray();
        })->toArray();

        $this->publish([
            'guiltyUser' => $user->toArray(),
            'users' => ArrayUtil::asCleanNumericArray($onCallUsers),
            'lastChangeWay' => $way,
        ], $conference, self::TYPE_ON_CALL_USERS);
    }

    /**
     * @param $arrayData
     * @param Conference $conference
     * @param $type
     * @param string $target
     */
    private function publish($arrayData, Conference $conference, $type, $target = self::LECTURER_KEY)
    {
        $this->client->publish(
            new AMQPMessage(json_encode($arrayData)),
            $conference->getUuid() . ".$target.$type"
        );
    }

    /**
     * @param $bullshit
     */
    public function publishBullshit($bullshit)
    {
        $this->client->publish(new AMQPMessage($bullshit), 'no.more.the.war.KAREN');
    }
}