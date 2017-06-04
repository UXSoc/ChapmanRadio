<?php
namespace BroadcastBundle\Command;

use BroadcastBundle\Entity\Stream;
use BroadcastBundle\Service\IcecastService;
use DOMDocument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/1/17
 * Time: 10:20 PM
 */
class StartIceServerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("icecast:start")
            ->setDescription("Start Icecast server");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var IcecastService $service */
        $service =  $this->getContainer()->get('brodcast.icecast');
        $service->updateConfig([new Stream()]);
        $service->startIcecast($service->getConfigPath(),true);

    }
}