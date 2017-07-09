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
use CoreBundle\Service\ImageCache;
use Doctrine\ORM\EntityManagerInterface;
use Imagine\Imagick\Imagine;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;

class MediaSubscriber  implements EventSubscriberInterface
{

    private $targetDir;
    private $em;
    private $imageCache;

    /**
     * ImageUploadService constructor.
     * @param string $targetDir
     */
    public function __construct($targetDir, EntityManagerInterface $em, ImageCache $imageCache)
    {
        $this->targetDir = $targetDir;
        $this->em = $em;
        $this->imageCache = $imageCache;
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

    public function onMediaDelete(MediaDeleteEvent $event)
    {
        $media = $event->getMedia();

        /** @var FileSystem $fs */
        $fs = new FileSystem();

        $ext = (new  MimeTypeExtensionGuesser())->guess($media->getMime());
        $fs->remove($this->targetDir . '/' . $this->getFullPath($media->getSource(),$ext));
        $media->setSource(null);
        $this->em->remove($media);
        $this->em->flush();
    }

    public function onMediaSave(MediaSaveEvent $mediaSaveEvent)
    {
        $fs = new FileSystem();

        $media = $mediaSaveEvent->getMedia();
        $file = $media->getFile();
        $media->setSource(substr(bin2hex(random_bytes(12)), 12));

        $fs->mkdir($this->targetDir . '/' . $this->getDirectoryPath($media->getSource()));

        switch ($file->getMimeType()){
            case Media::MEDIA_PNG:
            case Media::MEDIA_JPEG:
                $media->setMime('image/png');
                $i = (new Imagine())->open($media->getFile());
                $i->save($this->targetDir . '/' . $this->getFullPath($media->getSource(), 'png'),$mediaSaveEvent->getOptions());
                break;
        }
        $media->setFile(null);

    }

    public function onMediaRetrieve(MediaRetrieveEvent $event)
    {
        $media = $event->getMedia();
        $partial = '';
        switch ($media->getMime())
        {
            case Media::MEDIA_PNG:
                $partial = $this->getFullPath($media->getSource(),'png');
                break;
        }
        $path = $this->targetDir . '/' . $partial;
        if($media->getFilter() !== null)
            $path = $this->imageCache->resolve($path,$media->getFilter());
        $event->setPath($path);

    }

    public function onMediaFilter(MediaFilterEvent $mediaFilterEvent)
    {
        $media = $mediaFilterEvent->getMedia();

        $newMedia = new Media();
        $newMedia->setSource($media->getSource());
        $newMedia->setAltText($media->getAltText());
        $newMedia->setCaption($media->getAltText());
        $newMedia->setDescription($media->getAltText());
        $newMedia->setTitle($media->getTitle());
        $newMedia->setisHidden($mediaFilterEvent->getIsHidden());
        $newMedia->setFilter($mediaFilterEvent->getFilter());

        $mediaFilterEvent->setMedia($newMedia);

    }

}