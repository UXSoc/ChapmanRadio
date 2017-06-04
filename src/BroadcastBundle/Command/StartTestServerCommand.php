<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/2/17
 * Time: 8:03 PM
 */

namespace BroadcastBundle\Command;


use BroadcastBundle\Entity\Stream;
use BroadcastBundle\Service\IcecastService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartTestServerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("icecast:test:start")
            ->setDescription("Start Icecast server");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var IcecastService $service */
        $service =  $this->getContainer()->get('brodcast.icecast');

        $stream = new Stream();
        $stream->setMount('main.mp3');
        $stream->setPassword('password');
        $stream->setUsername('username');
        $stream->updatedTimestamps();
        $service->updateConfig([$stream]);
        if($service->isIcecastRunning())
            $service->refreshIcecast();
        else
            $service->startIcecast($service->getConfigPath(),true);
    }
}