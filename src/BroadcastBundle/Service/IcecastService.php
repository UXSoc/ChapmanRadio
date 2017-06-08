<?php

namespace BroadcastBundle\Service;

use BroadcastBundle\Entity\Stream;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

class IcecastService
{
    private $kernel;

    /**
     * IcecastService constructor.
     *
     * @param Kernel $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function getPublicAddr()
    {
        return $this->getParameter('icecast_public_addr', 'localhost:'.$this->getParameter('icecast_port', 9000));
    }

    /**
     * @param Stream[] $streams
     */
    public function updateConfig($streams)
    {
        $combinedMounts = '';
        /** @var Stream $stream */
        foreach ($streams as $stream) {
            $path = $this->generateRecordingPath($stream->getRecording());

            $route_mount = $stream->getMount();
            $username = $stream->getUsername();
            $password = $stream->getPassword();

            $combinedMounts .= <<<xml
          <mount type="normal">
                <mount-name>/$route_mount</mount-name>
                <username>$username</username>
                <password>$password</password>
                <max-listeners>5000</max-listeners>
                <max-listener-duration>3600</max-listener-duration>
                <dump-file>$path</dump-file>
                <intro>/intro.mp3</intro>
                <fallback-override>1</fallback-override>
                <fallback-when-full>1</fallback-when-full>
                <type>audio/mpeg</type>
                <charset>ISO8859-1</charset>
                <public>1</public>
                <bitrate>128</bitrate>
                <hidden>1</hidden>
                <burst-size>65536</burst-size>
                <mp3-metadata-interval>4096</mp3-metadata-interval>
                <on-connect>/home/icecast/bin/source-start</on-connect>
                <on-disconnect>/home/icecast/bin/source-end</on-disconnect>
            </mount>
xml;
            // <fallback-mount>/example2.mp3</fallback-mount>
            // <stream-url>http://some.place.com</stream-url>
            // <genre>classical</genre>
        }
        $rootDir = $this->kernel->getRootDir().'/../';

        $clients = $this->getParameter('icecast_clients', 5000);
        $sources = $this->getParameter('icecast_sources', 10);
        $workers = $this->getParameter('icecast_workers', 5);
        $queueSize = $this->getParameter('icecast_queue_size', 1048576);
        $headerTimeout = $this->getParameter('icecast_queue_size', 1048576);
        $sourceTimeout = $this->getParameter('icecast_works', 10);
        $burstSize = $this->getParameter('icecast_works', 500000);

        $relayUser = $this->getParameter('icecast_relay_username');
        $relayPassword = $this->getParameter('icecast_relay_password');
        $adminUsername = $this->getParameter('icecast_admin_username');
        $adminPassword = $this->getParameter('icecast_admin_password');

        $port = $this->getParameter('icecast_port', 9000);
        $admin = $this->getParameter('icecast_admin_email');
        $hostName = $this->getParameter('icecast_host');
        $location = $this->getParameter('icecast_location');

        $pidFile = $this->getProcessPidPath();

        $result = <<<xml
<icecast>

    <limits>
        <clients>$clients</clients>
        <sources>$sources</sources>
        <workers>$workers</workers>
        <queue-size>$queueSize</queue-size>
        <header-timeout>$headerTimeout</header-timeout>
        <source-timeout>$sourceTimeout</source-timeout>
        <burst-size>$burstSize</burst-size>
    </limits>
    <authentication>
        <relay-user>$relayUser</relay-user>
        <relay-password>$relayPassword</relay-password>
        <admin-user>$adminUsername</admin-user>
        <admin-password>$adminPassword</admin-password>
    </authentication>
    <listen-socket>
        <port>$port</port>
    </listen-socket>
    <admin>$admin</admin>
    <hostname>$hostName</hostname>
    <location>$location</location>
    <paths>
        <basedir>$rootDir</basedir>
        <logdir>var/logs/icecast</logdir>
        <pidfile>var/</pidfile>
        <adminroot>./admin</adminroot>
         <pidfile>$pidFile</pidfile>
    </paths>

    <logging>
        <accesslog>access.log</accesslog>
        <errorlog>error.log </errorlog>
        <playlistlog>playlist.log</playlistlog>
        <loglevel>4</loglevel>
    </logging>
    $combinedMounts
    
</icecast>
xml;
        file_put_contents($this->getConfigPath(), $result);
    }

    private function generateRecordingDirectory()
    {
        return $this->kernel->getRootDir().'/data/recordings/';
    }

    private function generateRecordingPath($hash)
    {
        return $this->generateRecordingDirectory().$hash.'.mp3';
    }

    public function getProcessPidPath()
    {
        return $this->kernel->getRootDir().'/../var/icecast.pid';
    }

    public function getConfigPath()
    {
        return $this->kernel->getRootDir().'/../var/icecast.xml';
    }

    public function startIcecast($configPage, $background = false)
    {
        if ($background == true) {
            exec('/bin/icecast -b -c '.$configPage);
        } else {
            exec('/bin/icecast -c '.$configPage);
        }
    }

    public function refreshIcecast()
    {
        $fileSystem = new Filesystem();

        $pidFile = $this->getProcessPidPath();
        if ($fileSystem->exists($pidFile)) {
            posix_kill(intval(file_get_contents($pidFile)), SIGHUP);
        }
    }

    public function isIcecastRunning()
    {
        $fileSystem = new Filesystem();
        $pidFile = $this->getProcessPidPath();

        return $fileSystem->exists($pidFile);
    }

    private function getParameter($parameter, $default = null)
    {
        if (!$this->kernel->getContainer()->hasParameter($parameter)) {
            return $default;
        }
        $param = $this->kernel->getContainer()->getParameter($parameter);

        return $param;
    }
}
