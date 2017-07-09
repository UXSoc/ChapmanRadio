<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/27/17
 * Time: 7:39 AM
 */

namespace CoreBundle\EventListener;


use CoreBundle\Entity\Image;
use CoreBundle\Event\ImageDeleteEvent;
use CoreBundle\Event\ImageEvent;
use CoreBundle\Event\ImageRetrieveEvent;
use CoreBundle\Event\ImageSaveEvent;
use CoreBundle\Event\SaveImageEvent;
use CoreBundle\Events;
use Doctrine\ORM\EntityManagerInterface;
use Imagine\Gd\Imagine;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;

use Intervention\Image\ImageManagerStatic as Intervention;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageSubscriber implements EventSubscriberInterface
{
    private $targetDir;
    private $em;


    /**
     * ImageUploadService constructor.
     * @param string $targetDir
     */
    public function __construct($targetDir, EntityManagerInterface $em)
    {
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
            ImageDeleteEvent::NAME =>   'onImageDelete',
            ImageSaveEvent::NAME =>   'onImageSave',
            ImageRetrieveEvent::NAME => 'onImageRetrieve'
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

    public function getImagePath(Image $image)
    {
        return $this->getFullPath($image->getSource(), 'png');
    }

    public function onImageSave(ImageSaveEvent $event){
        $image = $event->getImage();
        $image->setSource(substr(bin2hex(random_bytes(12)), 12));

        /** @var FileSystem $fs */
        $fs = new FileSystem();
        $fs->mkdir($this->targetDir . '/' . $this->getDirectoryPath($image->getSource()));
        $i = (new Imagine())
            ->open($image->getImage());

        if(is_callable($event->getCallback())){
            $callback = $event->getCallback();
            $callback($i);
        }

        $i->save($this->targetDir . '/' . $this->getFullPath($image->getSource(), 'png'),$event->getOptions());
        $image->setImage(null);
    }

    public function onImageDelete(ImageDeleteEvent $event){
        $image = $event->getImage();

        /** @var FileSystem $fs */
        $fs = new FileSystem();
        $fs->remove($this->targetDir . '/' . $this->getFullPath($image->getSource(),'png'));
        $image->setSource(null);
        $this->em->remove($image);
        $this->em->flush();
    }

    public function onImageRetrieve(ImageRetrieveEvent $event){
        $image = $event->getImage();
        $partial = $this->getFullPath($image->getSource(),'png');
        $event->setPath($this->targetDir . '/' . $partial);
    }
}