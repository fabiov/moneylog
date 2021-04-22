<?php

namespace ApplicationTest\Controller;

use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class RegistrationControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(include __DIR__ . '/../../../../config/application.config.php');
        parent::setUp();
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/auth/registration/forgotten-password');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('auth');
        $this->assertControllerName('auth\controller\registration');
        $this->assertControllerClass('RegistrationController');
        $this->assertMatchedRouteName('auth_registration');
    }
}
