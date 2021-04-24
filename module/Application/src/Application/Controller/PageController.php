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
    /**
     * @return ViewModel
     */
    public function privacyPolicyAction()
    {
        return new ViewModel();
    }
}