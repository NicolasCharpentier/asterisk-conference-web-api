<?php

namespace Spitchee\Util\Auth;

use Spitchee\Entity\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * Class SpitcheeAuthManager
 * @package Spitchee\Util\Auth
 */
class SpitcheeAuthManager
{
    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param MessageDigestPasswordEncoder $securityEncoder
     * @return null|\Spitchee\Entity\User
     */
    public static function findAuthUser(Request $request, UserRepository $userRepository, MessageDigestPasswordEncoder $securityEncoder) {
        $identifier = $request->server->get('PHP_AUTH_USER', null);
        $password = $request->server->get('PHP_AUTH_PW', null);

        if (! $identifier or ! $password) {
            //$this->log('[BasicAuth] : Les deux identifiants sont pas présent');
            return null;
        }

        $user = $userRepository->loadOneByIdentifier($identifier);

        if (! $user or ! $user->validatePassword($password, $securityEncoder)) {
            //$this->log("[BasicAuth] '$identifier' : user non trouvé ou password incoherent");
            return null;
        }

        //$this->log("[BasicAuth] '$identifier' : success");
        //$this->user = $user;

        return $user;
    }

    /**
     * @return Response
     */
    public static function getDefaultBasicAuthDenyResponse() {
        $response = new Response();
        $response->headers->set('WWW-Authenticate', sprintf('Basic realm="%s"', 'Basic Login'));
        $response->setStatusCode(401, 'Please sign in.');

        return $response;
    }
}