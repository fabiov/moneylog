<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class PageController extends AbstractActionController
{
    /**
     * @return ViewModel<mixed>
     */
    public function offlineAction(): ViewModel
    {
        return new ViewModel();
    }

    /**
     * @return ViewModel<mixed>
     */
    public function privacyPolicyAction(): ViewModel
    {
        return new ViewModel();
    }
}
