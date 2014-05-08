<?php namespace AnasT\Caracal\Permissions;

class Permission extends \Eloquent {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "permissions";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The Eloquent role model.
     *
     * @var string
     */
    protected static $roleModel = 'AnasT\Caracal\Roles\Role';

    /**
     * The accounts roles pivot table name.
     *
     * @var string
     */
    protected static $permissionsRolesPivot = 'permissions_roles';

    /**
     * The Eloquent account model.
     *
     * @var string
     */
    protected static $accountModel = 'AnasT\Caracal\Accounts\Account';

    /**
     * The accounts permissions pivot table name.
     *
     * @var string
     */
    protected static $accountsPermissionsPivot = 'accounts_permissions';

    /**
     * Listen for model events
     *
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function($permission)
        {
            $permission->roles()->detach();
            $permission->accounts()->detach();
        });
    }

    /**
     * Returns the relationship between roles and permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(static::$roleModel, static::$permissionsRolesPivot)->withPivot('value');
    }

    /**
     * Returns the relationship between accounts and permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function accounts()
    {
        return $this->belongsToMany(static::$accountModel, static::$accountsPermissionsPivot)
            ->withPivot('value');
    }

}