<?php
namespace WebsocketBundle\Server;

use FOS\RestBundle\Controller\FOSRestController;
use Monolog\Logger;
use Ratchet\MessageComponentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/2/17
 * Time: 1:37 PM
 */
class Chat extends FOSRestController implements MessageComponentInterface
{

    protected $connections = array();

    protected $clients;

    /** @var Logger  */
    private  $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);

        $this->clients = new \SplObjectStorage;
        /** @var Logger logger */
        $this->logger = $this->get('logger');
        $this->logger->info('Server Started');
        echo "Server Started";
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  \Ratchet\ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(\Ratchet\ConnectionInterface $conn)
    {
        /** @var \CoreBundle\Entity\User $user */
        $user = $this->getUser();
        $this->logger->log(Logger::INFO,"User {$conn->resourceId} Connected: {$user->getToken()} - {$user->getName()}");

        $this->clients->attach($conn,$user->getToken());
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

        $this->logger->log(Logger::INFO, "Connection {$conn->resourceId} has disconnected\n");
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
        $this->logger->log(Logger::ERROR, "An error has occurred: {$e->getMessage()}\n");

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
        $numRecv = count($this->clients) - 1;
        $this->logger->log(Logger::INFO, sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n", $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's'));

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }
}