<?php

declare(strict_types=1);

namespace Auth\Service;

use Application\Entity\User;
use Auth\Model\LoggedUser;
use Auth\Model\LoggedUserSettings;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;

/**
 * Adapter used for authenticating user. It takes login and password on input and checks the database
 * if there is a user with such login (email) and password. If such user exists, the service returns his identity.
 */
class AuthAdapter implements AdapterInterface
{
    private ?string $email;
    private ?string $password;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function authenticate(): Result
    {
        // Check the database if there is a user with such email.
        /** @var ?User $user */
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
            $settings = $user->getSetting();
            return new Result(Result::SUCCESS, new LoggedUser(
                $user->getId(),
                $user->getName(),
                $user->getSurname(),
                $user->getEmail(),
                $user->getRole(),
                new LoggedUserSettings($settings->getPayday(), $settings->getMonths(), $settings->hasProvisioning())
            ), ['Authenticated successfully.']);
        }

        // If password check didn't pass return 'Invalid Credential' failure status.
        return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['Invalid credentials.']);
    }
}
