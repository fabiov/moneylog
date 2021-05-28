<?php

namespace Auth\Service;

use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Result;

class AuthManager
{
    /**
     * @var AuthenticationService
     */
    private $authService;

    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authService = $authenticationService;
    }

    /**
     * Performs a login attempt. If $rememberMe argument is true, it forces the session
     * to last for one month (otherwise the session expires on one hour).
     *
     * @param string $email
     * @param string $password
     * @param bool $rememberMe
     * @return \Laminas\Authentication\Result
     */
    public function login(string $email, string $password, bool $rememberMe): Result
    {
        /** @var \Auth\Service\AuthAdapter $authAdapter */
        $authAdapter = $this->authService->getAdapter();

        // Authenticate with login/password.
        $authAdapter->setEmail($email);
        $authAdapter->setPassword($password);
        $result = $this->authService->authenticate();

        // If user wants to "remember him", we will make session to expire in one month.
        // By default session expires in 1 hour (as specified in our config/global.php file).
//        if ($result->getCode() == Result::SUCCESS) {
//            $identity = $result->getIdentity();
//
//
//            // if ($rememberMe) {
//            //     // Session cookie will expire in 1 week.
//            // }
//        }

        return $result;
    }

    public function logout(): void
    {
        $this->authService->clearIdentity();
    }
}
