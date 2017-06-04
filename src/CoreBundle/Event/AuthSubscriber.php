<?php
namespace CoreBundle\Event;

use CoreBundle\Service\UserTokenService;
use Monolog\Logger;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig_Environment;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/4/17
 * Time: 9:24 AM
 */
class AuthSubscriber implements EventSubscriberInterface
{


    /**
     * @var Twig_Environment
     */
    private $twig;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Swift_Mailer
     */
    private $mailer;
    /**
     * @var UserTokenService
     */
    private $tokenService;

    function __construct(Swift_Mailer $mailer, Twig_Environment $twig, Logger $logger,UserTokenService $tokenService)
    {
        $this->twig = $twig;
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->tokenService = $tokenService;
    }


    public static function getSubscribedEvents()
    {
        return array(
            'user.confirmation' => 'userConfirmation',
            'user.password_reset' => 'userReset'
        );
    }

    public function userConfirmation(UserEvent $event){
        $user = $event->getUser();

        $this->logger->info(sprintf('user conformation sent: %s',$user->getUsername()));

        $token = substr(bin2hex(random_bytes(20)),20);
        $this->tokenService->bindConfirmationToken($token,$user);

        $message = new Swift_Message();
        $message->setSubject('Welcome')
            ->setFrom($user->getEmail())
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'email/confirm.html.twig',
                    array('user' => $user,'token' => $token)
                ),
                'text/html'
            );
        $this->mailer->send($message);

    }

    public function userReset(UserEvent $event)
    {
        $user = $event->getUser();

        $this->logger->info(sprintf('user password sent: %s',$user->getUsername()));

        $token = substr(bin2hex(random_bytes(20)),20);
        $this->tokenService->bindPasswordResetToken($token,$user);

        $message = new Swift_Message();
        $message->setSubject('Welcome')
            ->setFrom($user->getEmail())
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'email/reset.html.twig',
                    array('user' => $user,'token' => $token)
                ),
                'text/html'
            );
        $this->mailer->send($message);


    }
}