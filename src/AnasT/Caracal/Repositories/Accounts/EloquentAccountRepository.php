<?php namespace AnasT\Caracal\Repositories\Accounts;

use AnasT\Caracal\Repositories\Crudable;
use AnasT\Caracal\Repositories\Paginable;
use AnasT\Caracal\Repositories\Repository;
use AnasT\Caracal\Repositories\AbstractRepository;

class EloquentAccountRepository extends AbstractRepository implements Crudable, Paginable, Repository, AccountRepositoryInterface {

    /**
     * Create a new account.
     *
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update an existing account.
     *
     * @param int $id
     * @param array $attributes
     * @return boolean
     */
    public function update($id, array $attributes)
    {
        $account = $this->find($id);
        $account->update($attributes);
        return $account->save();
    }

    /**
     * Delete an existing account.
     *
     * @param int $id
     * @return boolean
     */
    public function delete($id)
    {
        $account = $this->find($id);
        return $account->delete();
    }

    /**
     * Finds an account by the email value.
     *
     * @param string $email
     * @return mixed
     */
    public function findByEmail($email)
    {
        if( ! $account = $this->model->where('email', '=', $email)->first())
        {
            return false;
        }

        return $account;
    }

    /**
     * Finds an account by the given activation code.
     *
     * @param string $code
     * @return mixed
     */
    public function findByActivationCode($code)
    {
        if(! $account = $this->model->where('activation_code', '=', $code)->first())
        {
            return false;
        }

        return $account;
    }

    /**
     * Finds an account by the given remember token.
     *
     * @param string $token
     * @return mixed
     */
    public function findByRememberToken($token)
    {
        if(
            $email = $this->app['db']
                    ->connection()
                    ->table('password_reminders')
                    ->select('email')
                    ->where('token', '=', $token)
                    ->first()
        )
        {
            return $this->findByEmail($email->email);
        }

        return false;
    }

}