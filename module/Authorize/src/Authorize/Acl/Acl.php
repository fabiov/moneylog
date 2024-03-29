<?php

namespace Authorize\Acl;

use Laminas\Permissions\Acl\Acl as LaminasAcl;
use Laminas\Permissions\Acl\Resource\GenericResource as Resource;
use Laminas\Permissions\Acl\Role\GenericRole as Role;

/**
 * Class Acl to handle Access Control List, loading ACL defined in a config
 *
 * @author Marco Neumann <webcoder_at_binware_dot_org>
 * @category User
 * @copyright Copyright (c) 2011, Marco Neumann
 * @license http://binware.org/license/index/type:new-bsd New BSD License
 * @see http://p0l0.binware.org/index.php/2012/02/18/zend-framework-2-authentication-acl-using-eventmanager/
 */
class Acl extends LaminasAcl
{
    /**
     * Default Role
     */
    public const DEFAULT_ROLE = 'guest';

    /**
     * Constructor
     *
     * @param array<array> $config
     * @return void
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        if (!isset($config['acl']['roles']) || !isset($config['acl']['resources'])) {
            throw new \Exception('Invalid ACL Config found');
        }

        $roles = $config['acl']['roles'];
        if (!isset($roles[self::DEFAULT_ROLE])) {
            $roles[self::DEFAULT_ROLE] = '';
        }

        $this->_addRoles($roles)->_addResources($config['acl']['resources']);
    }

    /**
     * Adds Roles to ACL
     *
     * @param array<string, string> $roles
     * @return $this
     */
    protected function _addRoles(array $roles): self
    {
        foreach ($roles as $name => $parent) {
            if (!$this->hasRole($name)) {
                if (empty($parent)) {
                    $parent = [];
                } else {
                    $parent = explode(',', $parent);
                }

                $this->addRole(new Role($name), $parent);
            }
        }

        return $this;
    }

    /**
     * Adds Resources to ACL
     *
     * @param array<string, mixed> $resources
     * @return $this
     * @throws \Exception
     */
    protected function _addResources(array $resources): self
    {
        foreach ($resources as $permission => $controllers) {
            foreach ($controllers as $controller => $actions) {
                if ($controller == 'all') {
                    $controller = null;
                } else {
                    if (!$this->hasResource($controller)) {
                        $this->addResource(new Resource($controller));
                    }
                }

                foreach ($actions as $action => $role) {
                    if ($action == 'all') {
                        $action = null;
                    }

                    if ($permission == 'allow') {
                        $this->allow($role, $controller, $action);
                    } elseif ($permission == 'deny') {
                        $this->deny($role, $controller, $action);
                    } else {
                        throw new \Exception('No valid permission defined: ' . $permission);
                    }
                }
            }
        }

        return $this;
    }
}
