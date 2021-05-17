<?php

namespace ApplicationTest\Controller;

use Laminas\Http\Response;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp(): void
    {
        $this->setApplicationConfig(include __DIR__ . '/../../../../config/application.config.php');
        parent::setUp();
    }

    public function testIndexActionCanBeAccessed(): void
    {
        $this->dispatch('/');
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);

        $this->assertModuleName('auth');
        $this->assertControllerName('auth\controller\user');
        $this->assertControllerClass('UserController');
        $this->assertMatchedRouteName('home');
    }
}
