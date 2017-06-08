<?php

namespace CoreBundle\Service;

use CoreBundle\Entity\Image;
use CoreBundle\Entity\User;
use Intervention\Image\ImageManagerStatic as Intervention;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/18/17
 * Time: 10:34 PM.
 */
class ImageUploadService
{
    private $targetDir;

    /**
     * ImageUploadService constructor.
     *
     * @param string $targetDir
     */
    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function createImage(UploadedFile $image, User $user)
    {
        $image = new Image();
        $image->setAuthor($user);
        $image->setImage($image);

        return $image;
    }

    /**
     * @param Image $image
     */
    public function saveImageToFilesystem(Image $image)
    {
        /** @var FileSystem $fs */
        $fs = new FileSystem();

        /** @var \Intervention\Image\Image $intervention */
        $intervention = Intervention::make($image->getImage());
        $source = substr(bin2hex(random_bytes(12)), 12);
        $fs->mkdir($this->targetDir.'/'.$this->generateDirectory($source));

        $image->setSource($source);
        $intervention->save($this->targetDir.'/'.$this->generatePath($source, 'png'));
    }

    public function deleteImage(Image $image)
    {
    }

    private function generateDirectory($hash)
    {
        return substr($hash, 0, 2).'/'.substr($hash, 2, 2).'/';
    }

    private function generatePath($hash, $ext)
    {
        return $this->generateDirectory($hash).substr($hash, 4).'.'.$ext;
    }

    public function getTargetDir()
    {
        return $this->targetDir;
    }

    public function getImagePath(Image $image)
    {
        return $this->generatePath($image->getSource(), 'png');
    }
}
