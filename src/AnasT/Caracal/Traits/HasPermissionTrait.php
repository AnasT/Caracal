<?php namespace AnasT\Caracal\Traits;

trait HasPermissionTrait {

    /**
     * Returns permissions for the model.
     *
     * @param array $columns
     * @return array
     */
    public function getPermissions(array $columns = array('*'))
    {
        return $this->permissions()->get($columns)->toArray();
    }

    /**
     * See if a model has access to the passed permission(s).
     *
     * @param string|array $permissions
     * @param boolean $all
     * @return boolean
     */
    public function hasAccess($permissions, $all = true)
    {
        if ($this->isRoot())
        {
            return true;
        }

        return $this->hasPermission($permissions, $all);
    }

    /**
     * See if a model has access to the passed permission(s).
     *
     * @param string|array $permissions
     * @param boolean $all
     * @return boolean
     */
    public function hasPermission($permissions, $all = true)
    {
        $modelPermissions = (! method_exists($this, 'getMergedPermissions'))
                            ? $this->getPermissions(['name'])
                            : $this->getMergedPermissions();

        if(! is_array($permissions))
        {
            $permissions = (array) $permissions;
        }

        foreach($permissions as $permission)
        {
            $matched = true;

            if((strlen($permission) > 1) && ends_with($permission, '*'))
            {
                $matched = false;

                foreach($modelPermissions as $modelPermission)
                {
                    $checkPermission = substr($permission, 0, -1);

                    if($checkPermission != $modelPermission['name'] && starts_with($modelPermission['name'], $checkPermission) && $modelPermission['pivot']['value'] == 1)
                    {
                        $matched = true;
                        break;
                    }
                }
            }
            elseif((strlen($permission) > 1) && starts_with($permission, '*'))
            {
                $matched = false;

                foreach($modelPermissions as $modelPermission)
                {
                    $checkPermission = substr($permission, 1);

                    if($checkPermission != $modelPermission['name'] && ends_with($modelPermission['name'], $checkPermission) && $modelPermission['pivot']['value'] == 1)
                    {

                        $matched = true;
                        break;

                    }
                }

            }
            else
            {
                $matched = false;

                foreach($modelPermissions as $modelPermission)
                {
                    if((strlen($modelPermission['name']) > 1) && ends_with($modelPermission['name'], '*'))
                    {
                        $matched = false;

                        $checkModelPermission = substr($modelPermission['name'], 0, -1);

                        if($checkModelPermission != $permission && starts_with($permission, $checkModelPermission) && $modelPermission['pivot']['value'] == 1)
                        {
                            $matched = true;
                            break;
                        }
                    }
                    elseif($permission == $modelPermission['name'] && $modelPermission['pivot']['value'] == 1)
                    {
                        $matched = true;
                        break;
                    }
                }
            }

            if($all === true && $matched === false)
            {
                return false;
            }
            elseif($all === false && $matched === true)
            {
                return true;
            }
        }

        return $all;
    }

    /**
     * Returns if the model has access to any of the given permissions.
     *
     * @param string|array $permissions
     * @return boolean
     */
    public function hasAnyAccess($permissions)
    {
        return $this->hasAccess($permissions, false);
    }

    /**
     * Save inputted permissions.
     *
     * @param array $inputPermissions
     * @return void
     * @throws InvalidArgumentException
     */
    public function savePermissions(array $inputPermissions = array())
    {
        if(! empty($inputPermissions))
        {
            foreach($inputPermissions as $inputPermission => $value)
            {
                if(! in_array($value['value'], $this->allowedPermissionsValues))
                {
                    throw new \InvalidArgumentException("Invalid value [{$value['value']}] for permission [$inputPermission] given.");
                }
            }

            $this->permissions()->sync($inputPermissions);
        }
        else
        {
            $this->permissions()->detach();
        }
    }

    /**
     * Clear model's permissions.
     *
     * @return void
     */
    public function clearPermissions()
    {
        $this->savePermissions();
    }

    /**
     * Attach permission to current model.
     *
     * @param mixed $permission
     * @param int $value
     * @return void
     * @throws InvalidArgumentException
     */
    public function attachPermission($permission, $value)
    {
        if(! in_array($value, $this->allowedPermissionsValues))
        {
            throw new \InvalidArgumentException("Invalid value [$value] for permission [$permission] given.");
        }

        if( is_object($permission))

            $permission = $permission->getKey();

        if( is_array($permission))

            $permission = $permission['id'];

        $this->permissions()->attach($permission, ['value' => $value]);
    }

    /**
     * Detach permission form current model.
     *
     * @param mixed $permission
     * @return void
     */
    public function detachPermission($permission)
    {
        if( is_object($permission))

            $permission = $permission->getKey();

        if( is_array($permission))

            $permission = $permission['id'];

        $this->permissions()->detach($permission);
    }

    /**
     * Detach multiple permissions from current model.
     *
     * @param mixed $permissions
     * @return void
     */
    public function detachPermissions($permissions)
    {
        foreach($permissions as $permission)
        {
            $this->detachPermission($permission);
        }
    }

    /**
     * Checks if the model has the permission of root.
     *
     * @access public
     * @return boolean
     */
    public function isRoot()
    {
        return $this->hasPermission('root');
    }

}