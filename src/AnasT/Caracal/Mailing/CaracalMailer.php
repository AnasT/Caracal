<?php namespace AnasT\Caracal\Mailing;

use AnasT\Mailing\LaravelMailer;
use Illuminate\Foundation\Application;
use Illuminate\Database\Eloquent\Model;

class CaracalMailer extends LaravelMailer {

    /**
     * Send the activation email.
     *
     * @param Illuminate\Database\Eloquent\Model $receiver
     */
    public function sendActivationEmail(Model $receiver)
    {
        $view = $this->app['config']->get('caracal::activation_email_view');
        $email_subject = $this->app['translator']->get('caracal::caracal.email.activation_email.subject');

        $this->sendEmail($receiver, $email_subject, $view, ['receiver' => $receiver]);
    }

    /**
     * Send the reset password email.
     *
     * @param Illuminate\Database\Eloquent\Model $receiver
     * @param string $token
     */
    public function sendResetPasswordEmail(Model $receiver, $token)
    {
        $view = $this->app['config']->get('caracal::reminder_email_view');
        $email_subject = $this->app['translator']->get('caracal::caracal.email.reminder_email.subject');

        $this->sendEmail($receiver, $email_subject, $view, ['receiver' => $receiver, 'token' => $token]);
    }

}