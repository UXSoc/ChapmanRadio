<?php
namespace WebsocketBundle\Command;

use Ratchet\App;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebsocketBundle\Server\ChatSocket;

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
        $loop = \React\EventLoop\Factory::create();

        $app = new App('localhost',8080,'0.0.0.0',$loop);
        $app->route('/chat',new ChatSocket($this->getContainer(),$loop),array('*'));
        $app->run();

    }

}