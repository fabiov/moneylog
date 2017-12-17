<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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