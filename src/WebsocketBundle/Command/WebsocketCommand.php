<?php
namespace WebsocketBundle\Command;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebsocketBundle\Server\Chat;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/27/17
 * Time: 7:38 AM
 */
class WebsocketCommand  extends ContainerAwareCommand
{
    /**
     * Configure a new Command Line
     */
    protected function configure()
    {
        $this
            ->setName('chapman-radio:websocket')
            ->setDescription('Start the notification server.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $server = IoServer::factory(new HttpServer(
            new WsServer(
                new Chat($this->getContainer())
            )
        ), 8080);

        $server->run();

    }

}