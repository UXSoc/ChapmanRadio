<?php
namespace WebsocketBundle\Server;

use CoreBundle\Entity\User;
use CoreBundle\Repository\UserRepository;
use Firebase\JWT\JWT;
use FOS\RestBundle\Controller\FOSRestController;
use Monolog\Logger;
use Psr\Cache\CacheItemPoolInterface;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use React\EventLoop\LoopInterface;
use RestfulBundle\Controller\Api\V3\ChatController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WebsocketBundle\Server\Args\Args;
use WebsocketBundle\Server\Exception\AccessException;
use WebsocketBundle\Server\Exception\AuthenticationException;
use WebsocketBundle\Server\Exception\AuthException;
use WebsocketBundle\Server\Exception\SocketException;
use WebsocketBundle\Server\Packets\Message;
use WebsocketBundle\Server\Packets\Packet;
use WebsocketBundle\Server\Packets\UserNotice;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/2/17
 * Time: 1:37 PM
 */
class ChatSocket extends BaseSocket implements MessageComponentInterface
{
    protected $connections = array();

    protected $clients;

    public function __construct(ContainerInterface $container, LoopInterface $loop)
    {
        $this->setContainer($container);

        $this->clients = new \SplObjectStorage;
        $this->get('logger')->info('Server Started');
    }


    /**
     * When a new connection is opened it will be passed to this method
     * @param  \Ratchet\ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(\Ratchet\ConnectionInterface $conn)
    {
        /** @var \CoreBundle\Entity\User $user */
        $this->get('logger')->log(Logger::INFO, "User {$conn->resourceId} Connected: ");
        $this->clients->attach($conn);
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  \Ratchet\ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(\Ratchet\ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        $this->get('logger')->log(Logger::INFO, "Connection {$conn->resourceId} has disconnected\n");
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  \Ratchet\ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    function onError(\Ratchet\ConnectionInterface $conn, \Exception $e)
    {
        $this->get('logger')->log(Logger::ERROR, "An error has occurred: {$e->getMessage()}\n");
        $conn->close();
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    function onMessage(\Ratchet\ConnectionInterface $from, $msg)
    {
        $payload = \json_decode($msg);
        try {
            if (isset($payload->type)) {
                switch ($payload->type) {
                    case Packet::MESSAGE:
                        $this->processMessage($payload, $from);
                        break;
                    case Packet::AUTH:
                        $this->processAuthenticate($payload, $from);
                        break;
                    default:
                        throw new SocketException('Unknown type ' . $payload->type);
                }
            }
        } catch (SocketException $e) {
            $from->send($this->serializePacket($e));
        }
    }


    /**
     * @param $payload
     * @param ConnectionInterface $from
     * @return void
     */
    private function processAuthenticate($payload, $from)
    {
        if (isset($payload->token)) {
            $decode = JWT::decode($payload->token, $this->container->getParameter('env(SYMFONY_SECRET)'), array('HS256'));
            /** @var CacheItemPoolInterface $cache */
            $cache = $this->container->get('cache.app');

            if ($token = $cache->getItem(ChatController::CHAT_USER . $decode->token)) {
                if ($token->isHit()) {
                    if ($token->get() === $decode->jti) {
                        /** @var UserRepository $userRepository */
                        $userRepository = $this->getDoctrine()->getManager()->getRepository(User::class);

                        /** @var User $user */
                        if ($user = $userRepository->getByToken($decode->token)) {
                            $from->user = $user;
                            $this->get('logger')->log(Logger::INFO, sprintf('Connection %d authenticated as %s' . "\n", $from->resourceId, $user->getName()));
                            $cache->deleteItem($token->getKey());
                            $from->send($this->serializePacket(new UserNotice(UserNotice::VERIFIED,$from)));
                            return;
                        }
                    }
                    $cache->deleteItem($token->getKey());
                }
            }
        }
        throw new AuthenticationException('Authentication failed');
    }

    private function processMessage($payload, $from)
    {
        if (isset($from->user)) {

            if (isset($payload->message)) {
                $this->get('logger')->log(Logger::INFO, sprintf('Connection %d to clients %d as %s sending message: "%s"' . "\n", $from->resourceId, $this->clients->count() -1 , $from->user->getUsername(), $payload->message));
                foreach ($this->clients as $client) {
                    $client->send($this->serializePacket(new Message($payload->message,$from,$client)));
                }
            }
        } else throw  new AccessException('You need to be Authenticated First');
    }
}