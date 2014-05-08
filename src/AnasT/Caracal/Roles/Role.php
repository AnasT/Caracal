<?php namespace AnasT\Caracal\Roles;

use AnasT\Caracal\Traits\HasPermissionTrait;

class Role extends \Eloquent {

    /**
     * Use the HasPermission trait.
     *
     */
    use HasPermissionTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The Eloquent account model.
     *
     * @var string
     */
    protected static $accountModel = 'AnasT\Caracal\Accounts\Account';

    /**
     * The accounts roles pivot table name.
     *
     * @var string
     */
    protected static $accountsRolesPivot = 'assigned_roles';

    /**
     * The Eloquent permission model.
     *
     * @var string
     */
    protected static $permissionModel = 'AnasT\Caracal\Permissions\Permission';

    /**
     * The permissions roles pivot table name.
     *
     * @var string
     */
    protected static $permissionsRolesPivot = 'permissions_roles';

    /**
     * Allowed permissions values.
     *
     * @var array
     */
    protected $allowedPermissionsValues = [0, 1];

    /**
     * Listen for model events.
     *
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function($role) {
            $role->clearPermissions();
            $role->accounts()->detach();
        });
    }

    /**
     * Returns the relationship between roles and accounts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function accounts()
    {
        return $this->belongsToMany(static::$accountModel, static::$accountsRolesPivot);
    }

    /**
     * Returns the relationship between roles and permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(static::$permissionModel, static::$permissionsRolesPivot)
            ->withPivot('value');
    }

}