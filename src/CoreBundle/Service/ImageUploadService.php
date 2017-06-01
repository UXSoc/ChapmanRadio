<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Service;

use CoreBundle\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Intervention\Image\ImageManagerStatic as Intervention;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\FileValidator;


/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/18/17
 * Time: 10:34 PM
 */
class ImageUploadService
{
    private $targetDir;
    private $imageRepository;

    public function __construct($targetDir, $imageRepository)
    {
        $this->targetDir = $targetDir;
        $this->imageRepository = $imageRepository;
    }

    /**
     * @param Image $image
     */
    public function saveImage(Image $image)
    {
        /** @var FileSystem $fs */
        $fs = new FileSystem();

        /** @var \Intervention\Image\Image $intervention */
        $intervention = Intervention::make($image->getImage());
        $source = substr(bin2hex(random_bytes(12)), 12);
        $fs->mkdir($this->targetDir . '/' . $this->generateDirectory($source));

        $image->setSource($source);
        $intervention->save($this->targetDir . '/' . $this->generatePath($source, 'png'));

    }

    public function deleteImage(Image $image)
    {

    }

    private function generateDirectory($hash)
    {
        return substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/';
    }

    private function generatePath($hash, $ext)
    {
        return $this->generateDirectory($hash) . substr($hash, 4) . '.' . $ext;

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