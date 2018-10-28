<?php
namespace Authorize;

use Authorize\Acl\Acl;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__],
            ],
        ];
    }

    // FOR Authorization
    public function onBootstrap(\Zend\EventManager\EventInterface $e) // use it to attach event listeners
    {
        $application = $e->getApplication();
        $em = $application->getEventManager();
        $em->attach('route', [$this, 'onRoute'], -100);
    }

    /**
     * WORKING the main engine for ACL
     * @param \Zend\EventManager\EventInterface $e
     * @throws \Exception
     */
    public function onRoute(\Zend\EventManager\EventInterface $e) // Event manager of the app
    {
        $application = $e->getApplication();
        $routeMatch = $e->getRouteMatch();
        $sm = $application->getServiceManager();

        /* @var $auth \Zend\Authentication\AuthenticationService */
        $auth = $sm->get(\Zend\Authentication\AuthenticationService::class);

        $config = $sm->get('Config');
        $acl = new Acl($config);

        // The default role is guest $acl everyone is guest untill it gets logged in
        $role = $auth->hasIdentity() ? $auth->getIdentity()->role : Acl::DEFAULT_ROLE;

        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');

        if (!$acl->hasResource($controller)) {
            throw new \Exception("Resource $controller not defined");
        }

        if (!$acl->isAllowed($role, $controller, $action)) {
            $url = $e->getRouter()->assemble(array(), array('name' => 'home'));
            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);

            /**
             * The HTTP response status code 302 Found is a common way of performing a redirection.
             * @link http://en.wikipedia.org/wiki/HTTP_302
             */
            $response->setStatusCode(302);
            $response->sendHeaders();
            exit;
        }
    }
}