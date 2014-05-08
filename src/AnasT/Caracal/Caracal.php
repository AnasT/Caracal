<?php namespace AnasT\Caracal;

use DateTime;
use AnasT\Mailing\MailingInterface;
use Illuminate\Foundation\Application;
use AnasT\Caracal\Repositories\Roles\RoleRepositoryInterface;
use AnasT\Caracal\Repositories\Accounts\AccountRepositoryInterface;
use AnasT\Caracal\Repositories\Permissions\PermissionRepositoryInterface;

class Caracal {

    /**
     * Laravel application.
     *
     * @var Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Caracal account repository instance.
     *
     * @var AnasT\Caracal\Repositories\Accounts\EloquentAccountRepository
     */
    protected $accountRepo;

    /**
     * Account mailer.
     *
     * @var AnasT\Mailing\MailingInterface
     */
    protected $mailer;

    /**
     * Create a new Caracal instance.
     *
     * @param Illuminate\Foundation\Application $app
     * @param AnasT\Caracal\Repositories\Accounts\AccountRepositoryInterface $accountRepo
     * @param AnasT\Mailing\MailingInterface $mailer
     */
    public function __construct(
        Application $app,
        AccountRepositoryInterface $accountRepo,
        MailingInterface $mailer
    )
    {
        $this->app = $app;
        $this->accountRepo = $accountRepo;
        $this->mailer = $mailer;
    }

    /**
     * Get the currently authenticated account or null.
     *
     * @return mixed
     */
    public function account()
    {
        return $this->app['auth']->user();
    }
    /**
     * Registers an account.
     *
     * @param array $attributes
     * @param bool $activate
     * @return mixed
     */
    public function register(array $attributes, $activate = false)
    {
        if($account = $this->accountRepo->create($attributes))
        {
            ($activate)
            ? $account->activate($account->activation_code)
            : $this->mailer->sendActivationEmail($account);
        }

        return $account;
    }

    /**
     * Login an account using given credentials.
     *
     * @param array $credentials
     * @param boolean $activated_only
     * @return boolean
     */
    public function login(array $credentials, $activated_only = false)
    {
        if(! $this->reachedAttemptsLimit($credentials))
        {
            $account = $this->accountRepo->findByEmail($credentials['email']);

            if(
                $account
                && ($account->activated || ! $activated_only)
                && $this->app['hash']->check($credentials['password'], $account->password)
            )
            {
                $remember = isset($credentials['remember']) ? $credentials['remember'] : false;
                $this->app['auth']->login($account, $remember);

                return true;
            }
        }

        $this->attemptsCount($credentials);

        return false;
    }

    /**
     * Returns the name of the cache key used to store failed login attempts.
     *
     * @param array $credentials.
     * @return string.
     */
    protected function attemptCacheKey(array $credentials)
    {
        return 'caracal_failed_login_attempt_'
            .$this->app['request']->server('REMOTE_ADDR')
            .$credentials['email'];
    }

    /**
     * Checks if the current IP and email has reached the attempts limit.
     *
     * @param array $credentials
     * @return boolean
     */
    protected function reachedAttemptsLimit(array $credentials)
    {
        $attempt_key = $this->attemptCacheKey($credentials);
        $attempts = $this->app['cache']->get($attempt_key, 0);

        return $attempts >= 5;
    }

    /**
     * Increment attempts count.
     *
     * @param array $credentials
     * @return void
     */
    protected function attemptsCount(array $credentials)
    {
        $attempt_key = $this->attemptCacheKey($credentials);
        $attempts = $this->app['cache']->get($attempt_key, 0);
        $attempts += 1;

        $this->app['cache']->put($attempt_key, $attempts, 10);
    }

    /**
     * Send email with information about password reset.
     *
     * @param string $email
     * @return boolean
     */
    public function forgotPassword($email)
    {
        if($account = $this->accountRepo->findByEmail($email))
        {
            $token = getRandomString();

            $values = [
                'email'=> $account->email,
                'token'=> $token,
                'created_at'=> new DateTime
            ];

            $this->app['db']
                ->connection()
                ->table('password_reminders')
                ->insert($values);

            $this->mailer->sendResetPasswordEmail($account, $token);

            return true;
        }

        return false;
    }

    /**
     * Change account password.
     *
     * @param array $attributes
     * @return boolean
     */
    public function resetPassword(array $attributes)
    {
        if($account = $this->accountRepo->findByRememberToken($attributes['token']))
        {
            unset($attributes['token']);

            if($this->accountRepo->update($account->id, $attributes))
            {
                $this->app['db']
                    ->connection()
                    ->table('password_reminders')
                    ->where('email', '=', $account->email)
                    ->delete();

                return true;
            }
        }

        return false;
    }

    /**
     * Logout the account.
     *
     * @return void
     */
    public function logout()
    {
        $this->app['auth']->logout();
    }

    /**
     * Checks if the current account has the passed role.
     *
     * @param string $name
     * @return boolean
     */
    public function hasRole($name)
    {
        if($account = $this->account())
        {
            return $account->hasRole($name);
        }

        return false;
    }

    /**
     * See if a current account has access to the passed permission.
     *
     * @param string $name
     * @return boolean
     */
    public function can($name)
    {
        if($account = $this->account())
        {
            return $account->hasAccess($name);
        }

        return false;
    }

}