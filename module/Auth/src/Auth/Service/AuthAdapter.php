<?php

namespace Auth\Service;

use Application\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;

/**
 * Adapter used for authenticating user. It takes login and password on input
 * and checks the database if there is a user with such login (email) and password.
 * If such user exists, the service returns his identity (email).
 * The identity is saved to session and can be retrieved later with Identity view helper providedby ZF3.
 */
class AuthAdapter implements AdapterInterface
{
    /**
     * User email.
     * @var string
     */
    private $email;

    /**
     * Password
     * @var string
     */
    private $password;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Sets password.
     *
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password)
    {
        $this->password = (string) $password;
        return $this;
    }

    /**
     * Performs an authentication attempt.
     *
     * @return Result
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function authenticate(): Result
    {
        /* @var \Application\Entity\User $user */
        // Check the database if there is a user with such email.
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $this->email]);

        // If there is no such user, return 'Identity Not Found' status.
        if (!$user) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null, ['Invalid credentials.']);
        }

        // If the user with such email exists, we need to check if it is confirmed.
        // Do not allow not confirmed users to log in.
        if ($user->getStatus() == User::STATUS_NOT_CONFIRMED) {
            return new Result(Result::FAILURE, null, ['User not confirmed.']);
        }

        // Now we need to calculate hash based on user-entered password and compare
        // it with the password hash stored in database.
        if ($user->getPassword() === md5($this->password . $user->getSalt())) {
            $user->setLastLogin(new \DateTime());
            $this->entityManager->flush();

            // Great! The password hash matches. Return user identity (email) to be saved in session for later use.
            return new Result(Result::SUCCESS, (object) [
                'id'      => $user->getId(),
                'name'    => $user->getName(),
                'surname' => $user->getSurname(),
                'email'   => $user->getEmail(),
                'role'    => $user->getRole(),
                'setting' => $user->getSetting(),
            ], ['Authenticated successfully.']);
        }

        // If password check didn't pass return 'Invalid Credential' failure status.
        return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['Invalid credentials.']);
    }
}
