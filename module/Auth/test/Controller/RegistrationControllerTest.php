<?php
declare(strict_types=1);

namespace AuthTest\Controller;

use Auth\Controller\RegistrationController;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class RegistrationControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp(): void
    {
        $this->setApplicationConfig(include __DIR__ . '/../../../../config/application.config.php');
        parent::setUp();
    }

    /**
     * @throws \Exception
     */
    public function testForgottenPasswordActionCanBeAccessed(): void
    {
        $this->dispatch('/auth/registration/forgotten-password');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('auth');
        $this->assertControllerName(RegistrationController::class);
        $this->assertControllerClass('RegistrationController');
    }

    /**
     * @throws \Exception
     */
    public function testRegistrationActionCanBeAccessed(): void
    {
        $this->dispatch('/auth/registration/forgotten-password');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('auth');
        $this->assertControllerName(RegistrationController::class);
        $this->assertControllerClass('RegistrationController');
    }
}
