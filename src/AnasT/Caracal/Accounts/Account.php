<?php namespace AnasT\Caracal\Accounts;

use DateTime;
use Illuminate\Auth\UserInterface;
use AnasT\Caracal\Traits\HasRoleTrait;
use AnasT\Caracal\Traits\HasPermissionTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Account extends \Eloquent implements UserInterface, RemindableInterface {

    /**
     * Use the HasPermission trait.
     *
     */
    use HasPermissionTrait;

    /**
     * Use the HasRole trait.
     *
     */
    use HasRoleTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'accounts';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'activation_code'
    ];

    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Attributes that should be hashed.
     *
     * @var array
     */
    protected $hashableAttributes = [
        'password'
    ];

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
    protected static $accountsRolesPivot = 'assigned_roles';

    /**
     * The Eloquent permission model.
     *
     * @var string
     */
    protected static $permissionModel = 'AnasT\Caracal\Permissions\Permission';

    /**
     * The accounts permissions pivot table name.
     *
     * @var string
     */
    protected static $accountsPermissionsPivot = 'accounts_permissions';

    /**
     * The identity columns.
     *
     * @var string
     */
    protected $identityColumn = 'email';

    /**
     * Allowed permissions values.
     *
     * @var array
     */
    protected $allowedPermissionsValues = [-1, 0, 1];

    /**
     * Merged Permissions.
     *
     * @var array
     */
    protected $mergedPermissions = [];

    /**
     * Listen for model events.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function($account)
        {
            $account->clearRoles();
            $account->clearPermissions();
        });

        static::creating(function($account)
        {
            $account->activation_code = getRandomString();
        });

        static::saving(function($account)
        {
            if(isset($account->password_confirmation))

                unset($account->password_confirmation);
        });

    }

    /**
     * Returns the relationship between roles and accounts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(static::$roleModel, static::$accountsRolesPivot);
    }

    /**
     * Returns the relationship between permissions and accounts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(static::$permissionModel, static::$accountsPermissionsPivot)
            ->withPivot('value');
    }

    /**
     * Get the unique identifier for the account.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the account.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $token
     * @return void
     */
    public function setRememberToken($token)
    {
        $this->remember_token = $token;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the identity columns.
     *
     * @return array
     */
    public function getIdentityColumn()
    {
        return $this->identityColumn;
    }

    /**
     * Get the hashable attributes.
     *
     * @return array
     */
    public function getHashableAttributes()
    {
        return $this->hashableAttributes;
    }

    /**
     * Columns to be converted to Carbon instances.
     *
     * @return array
     */
    public function getDates()
    {
        return ['created_at', 'updated_at', 'last_login', 'activated_at'];
    }

    /**
     * Returns an array of merged permissions for the account.
     *
     * @return array
     */
    public function getMergedPermissions()
    {
        if( ! $this->mergedPermissions)
        {
            $permissions = [];

            foreach($this->getRoles() as $role)
            {
                foreach($rolePermissions = $role->getPermissions() as $rolePermission)
                {
                    $permissions[$rolePermission['name']] = [
                        'display_name' => $rolePermission['display_name'],
                        'value' => $rolePermission['pivot']['value']
                    ];
                }
            }

            foreach ($accountPermissions = $this->getPermissions() as $accountPermission)
            {
                if($accountPermission['pivot']['value'] != 0)

                    $permissions[$accountPermission['name']] = [
                        'display_name' => $accountPermission['display_name'],
                        'value' => $accountPermission['pivot']['value']
                    ];
            }

            foreach ($permissions as $permission => $value)
            {
                array_push($this->mergedPermissions, ['name' => $permission, 'display_name' => $value['display_name'], 'pivot' => ['value' => $value['value']]]);
            }
        }

        return $this->mergedPermissions;
    }

    /**
     * Activate the account.
     *
     * @return boolean
     */
    public function activate()
    {
        if(! $this->activated)
        {
            $this->activation_code = null;
            $this->activated = true;
            $this->activated_at = new DateTime;
            return $this->save();
        }

        return false;
    }

    /**
     * Records a login for the account.
     *
     * @return void
     */
    public function recordLogin()
    {
        $this->last_login = new DateTime;
        $this->save();
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->hashableAttributes) and ! empty($value))
        {
            $value = \Hash::make($value);
        }

        return parent::setAttribute($key, $value);
    }

}
