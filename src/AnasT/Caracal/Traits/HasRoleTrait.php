<?php namespace AnasT\Caracal\Traits;

trait HasRoleTrait {

    /**
     * Returns roles for the model.
     *
     * @param array $columns
     * @return array
     */
    public function getRoles(array $columns = array('*'))
    {
        return $this->roles()->get($columns);
    }

    /**
     * Checks if the model has a role.
     *
     * @param string $roleName.
     * @return boolean
     */
    public function hasRole($roleName)
    {
        foreach($this->getRoles() as $role)
        {
            if($role->name == $roleName) return true;
        }

        return false;
    }

    /**
     * Save roles inputted.
     *
     * @param array $inputRoles
     * @return void
     */
    public function saveRoles(array $inputRoles = array())
    {
        if(! empty($inputRoles))
        {
            $this->roles()->sync($inputRoles);
        }
        else
        {
            $this->roles()->detach();
        }
    }

    /**
     * Clear model's roles.
     *
     * @return void
     */
    public function clearRoles()
    {
        $this->saveRoles();
    }

    /**
     * Attach role to current model.
     *
     * @param mixed $role
     * @return void
     */
    public function attachRole($role)
    {
        if( is_object($role))

            $role = $role->getKey();

        if( is_array($role))

            $role = $role['id'];

        $this->roles()->attach($role);
    }

    /**
     * Detach role form current model.
     *
     * @param mixed $role
     * @return void
     */
    public function detachRole($role)
    {
        if( is_object($role))

            $role = $role->getKey();

        if( is_array($role))

            $role = $role['id'];

        $this->roles()->detach($role);
    }

    /**
     * Attach multiple roles to current model.
     *
     * @param mixed $roles
     * @return void
     */
    public function attachRoles($roles)
    {
        foreach($roles as $role)
        {
            $this->attachRole($role);
        }
    }

    /**
     * Detach multiple roles from current model.
     *
     * @param mixed $roles
     * @return void
     */
    public function detachRoles($roles)
    {
        foreach($roles as $role)
        {
            $this->detachRole($role);
        }
    }

}