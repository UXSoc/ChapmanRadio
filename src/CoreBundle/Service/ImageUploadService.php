<?php
namespace CoreBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

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

    public function __construct($targetDir,$imageRepository)
    {
        $this->targetDir = $targetDir;
        $this->imageRepository = $imageRepository;
    }

    public  function uploadFile(UploadedFile $file)
    {

    }
}