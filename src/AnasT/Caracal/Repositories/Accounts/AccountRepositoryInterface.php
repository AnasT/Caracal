<?php namespace AnasT\Caracal\Repositories\Accounts;

interface AccountRepositoryInterface {

    /**
     * Finds an account by the email value.
     *
     * @param string $email
     * @return mixed
     */
    public function findByEmail($email);

    /**
     * Finds an account by the given activation code.
     *
     * @param string $code
     * @return mixed
     */
    public function findByActivationCode($code);

    /**
     * Finds an account by the given remember token.
     *
     * @param string $token
     * @return mixed
     */
    public function findByRememberToken($token);

}