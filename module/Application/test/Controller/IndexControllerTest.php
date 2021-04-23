<?php

namespace ApplicationTest\Controller;

use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp(): void
    {
        $this->setApplicationConfig(include __DIR__ . '/../../../../config/application.config.php');
        parent::setUp();
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('auth');
        $this->assertControllerName('auth\controller\user');
        $this->assertControllerClass('UserController');
        $this->assertMatchedRouteName('home');
    }
}
