<?php
namespace Auth\Service;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Session\SessionManager;

class AuthManager
{
    /**
     * @var AuthenticationService
     */
    private $authService;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * AuthManager constructor.
     *
     * @param AuthenticationService $authenticationService
     * @param SessionManager $sessionManager
     */
    public function __construct(AuthenticationService $authenticationService, SessionManager $sessionManager)
    {
        $this->authService    = $authenticationService;
        $this->sessionManager = $sessionManager;
    }

    /**
     * Performs a login attempt. If $rememberMe argument is true, it forces the session
     * to last for one month (otherwise the session expires on one hour).
     *
     * @param $email
     * @param $password
     * @param $rememberMe
     * @return mixed
     * @throws \Exception
     */
    public function login($email, $password, $rememberMe)
    {
        // Check if user has already logged in. If so, do not allow to log in twice.
//        if ($this->authService->getIdentity() != null) {
//            throw new \Exception('Already logged in');
//        }

        // Authenticate with login/password.
        $this->authService->getAdapter()->setEmail($email)->setPassword($password);
        $result = $this->authService->authenticate();


        // If user wants to "remember him", we will make session to expire in one month.
        // By default session expires in 1 hour (as specified in our config/global.php file).
        if ($result->getCode() == Result::SUCCESS && $rememberMe) {
            // Session cookie will expire in 1 week.
            $this->sessionManager->rememberMe(3600 * 24 * 7);
        }

        return $result;
    }

    /**
     * Performs user logout.
     */
    public function logout()
    {
        // Allow to log out only when user is logged in.
        if ($this->authService->getIdentity() == null) {
            throw new \Exception('The user is not logged in');
        }

        // Remove identity from session.
        $this->authService->clearIdentity();
    }
}