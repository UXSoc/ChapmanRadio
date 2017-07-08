<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/7/17
 * Time: 2:04 PM
 */

namespace CoreBundle\EventListener;


use CoreBundle\Entity\Media;
use CoreBundle\Event\MediaDeleteEvent;
use CoreBundle\Event\MediaFilterEvent;
use CoreBundle\Event\MediaRetrieveEvent;
use CoreBundle\Event\MediaSaveEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MediaSubscriber  implements EventSubscriberInterface
{

    private $targetDir;
    private $mediaUri;
    private $em;

    /**
     * ImageUploadService constructor.
     * @param string $targetDir
     */
    public function __construct($targetDir,$mediaUri, EntityManagerInterface $em)
    {
        $this->mediaUri = $mediaUri;
        $this->targetDir = $targetDir;
        $this->em = $em;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            MediaDeleteEvent::NAME =>   'onMediaDelete',
            MediaSaveEvent::NAME =>  'onMediaSave',
            MediaFilterEvent::NAME => 'onMediaFilter',
            MediaRetrieveEvent::NAME => 'onMediaRetrieve'
        ];
    }

    private function getDirectoryPath($hash)
    {
        return substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/';
    }

    private function getFullPath($hash, $ext)
    {
        return $this->getDirectoryPath($hash) . substr($hash, 4) . '.' . $ext;
    }

    public function onMediaDelete(MediaDeleteEvent $mediaDeleteEvent)
    {

    }

    public function onMediaSave(MediaSaveEvent $mediaSaveEvent)
    {
        $media = $mediaSaveEvent->getMedia();
        $file = $media->getFile();
        $media->setSource(substr(bin2hex(random_bytes(12)), 12));

        switch ($file->getMimeType()){
            case 'image/png':
            case 'image/jpeg':
                $media->setMime('image/png');
                break;
        }
    }

    public function onMediaFilter(MediaFilterEvent $mediaFilterEvent)
    {

    }

    public function onMediaRetrieve(MediaRetrieveEvent $mediaRetrieveEvent)
    {

    }
}