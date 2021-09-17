<?php

namespace ApplicationTest\Controller;

use Application\Controller\PageController;
use Laminas\Http\Response;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class PageControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp(): void
    {
        $this->setApplicationConfig(include __DIR__ . '/../../../../config/application.config.php');
        parent::setUp();
    }

    public function testPrivacyPolicyActionActionCanBeAccessed(): void
    {
        $this->dispatch('/page/privacy-policy');
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);

        $this->assertModuleName('application');
        $this->assertControllerName(PageController::class);
        $this->assertControllerClass('PageController');
        $this->assertMatchedRouteName('page');
    }

    public function testOfflineActionActionActionCanBeAccessed(): void
    {
        $this->dispatch('/page/offline');
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);

        $this->assertModuleName('application');
        $this->assertControllerName(PageController::class);
        $this->assertControllerClass('PageController');
        $this->assertMatchedRouteName('page');
    }
}
