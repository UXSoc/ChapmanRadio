<?php
namespace BroadcastBundle\Command;

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
        /** @var Kernel $kernel */
        $kernel = $this->getContainer()->get('kernel');
        $rootDir = $kernel->getRootDir();
        $var = $rootDir . '/../var';
        $file = $rootDir . '/../var/icecast.xml';

        $logDirectory = $rootDir . '/../var/logs';

        $result = <<<xml
<icecast>
    <limits>
        <clients>5000</clients>
        <sources>10</sources>
        <workers>5</workers>
        <queue-size>1048576</queue-size>
        <burst-size>943718</burst-size>
        <header-timeout>15</header-timeout>
        <source-timeout>10</source-timeout>
        <burst-size>500000</burst-size>
    </limits>
    <authentication>
        <source-password>hackme</source-password>
        <relay-user>relay</relay-user>
        <relay-password>hackme</relay-password>
        <admin-user>admin</admin-user>
        <admin-password>hackme</admin-password>
    </authentication>
    <listen-socket>
        <port>9000</port>
    </listen-socket>
    <admin>mpollind@localhost</admin>
    <hostname>0.0.0.0</hostname>
    <location>ch_radio</location>
    <paths>
        <basedir>$var</basedir>
        <logdir>$logDirectory</logdir>
        <pidfile>$var/icecast.pid</pidfile>
        <webroot>./web</webroot>
        <adminroot>./admin</adminroot>
        <alias source="/foo" dest="/bar"/>
    </paths>

    <logging>
        <accesslog>icecast_access.log</accesslog>
        <errorlog>icecast_error.log </errorlog>
        <playlistlog>icecast_playlist.log</playlistlog>
        <loglevel>4</loglevel>
    </logging>
    <mount type="normal">
        <mount-name>/main.mp3</mount-name>
        <username>othersource</username>
        <password>hackmemore</password>
        <max-listeners>5000</max-listeners>
        <max-listener-duration>3600</max-listener-duration>
        <dump-file>/tmp/main.ogg</dump-file>
        <intro>/intro.mp3</intro>
        <fallback-mount>/example2.mp3</fallback-mount>
        <fallback-override>1</fallback-override>
        <fallback-when-full>1</fallback-when-full>
        <type>audio/mpeg</type>
        <charset>ISO8859-1</charset>
        <public>1</public>
        <stream-url>http://some.place.com</stream-url>
        <genre>classical</genre>
        <bitrate>128</bitrate>
        <hidden>1</hidden>
        <burst-size>65536</burst-size>
        <mp3-metadata-interval>4096</mp3-metadata-interval>
        <on-connect>/home/icecast/bin/source-start</on-connect>
        <on-disconnect>/home/icecast/bin/source-end</on-disconnect>
    </mount>
    
    
</icecast>
xml;

        file_put_contents($file,$result);
        exec('/bin/icecast -c '.$file);

    }
}