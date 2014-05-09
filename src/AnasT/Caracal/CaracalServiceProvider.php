<?php namespace AnasT\Caracal;

use Illuminate\Support\ServiceProvider;

use AnasT\Caracal\Accounts\Account;
use AnasT\Caracal\Mailing\CaracalMailer;
use AnasT\Caracal\Repositories\Accounts\EloquentAccountRepository;

class CaracalServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('anas-t/caracal');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerAccountRepository();
		$this->registerMailer();
		$this->registerCaracal();
	}

	/**
	 * Register Caracal.
	 *
	 * @return void
	 */
	public function registerCaracal()
	{
		$this->app->singleton('caracal', function($app)
		{
			return new Caracal(
				$app,
				$app['caracal.repositories.account'],
				$app['caracal.mailer']
			);
		});
	}

    /**
     * Register account repository.
     *
     * @return void
     */
    public function registerAccountRepository()
    {
        $this->app->bind('caracal.repositories.account', function($app)
        {
            $model = $app['config']->get('auth.model');
            return new EloquentAccountRepository($app, new $model);
        });
    }

    /**
     * Register the application mailer.
     *
     * @return void
     */
    public function registerMailer()
    {
        $this->app->singleton('caracal.mailer', function($app)
        {
            return new CaracalMailer;
        });
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['caracal'];
	}

}
