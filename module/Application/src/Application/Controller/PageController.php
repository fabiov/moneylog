<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

/**
 * Class PageController
 * @package Application\Controller
 */
class PageController extends AbstractActionController
{
    public function offlineAction(): ViewModel
    {
        return new ViewModel();
    }

    public function privacyPolicyAction(): ViewModel
    {
        return new ViewModel();
    }
}
