<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/8/17
 * Time: 10:02 AM
 */

namespace CoreBundle\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Symfony\Component\Filesystem\Filesystem;

class ImageCache
{
    private $targetDir;


    function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    private function getDirectoryPath($hash)
    {
        return substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/';
    }

    private function getFullPath($hash, $ext)
    {
        return $this->getDirectoryPath($hash) . substr($hash, 4) . '.' . $ext;
    }


    public function resolve($path,$filter)
    {
        $fs = new Filesystem();

        $hash = substr(hash('sha256', \json_encode($filter) . $path),12);
        $subPath = $this->getFullPath($hash,'.png');
        $fullPath = $this->targetDir . '/' . $subPath;


        if(!$fs->exists($fullPath)) {
            $fs->mkdir($this->targetDir . '/' . $this->getDirectoryPath($hash));
            $imagine = (new Imagine())->load($path);
            $this->applyFilter($imagine, $filter);
            $imagine->save($fullPath);
        }
        $fs->touch($fullPath);

        return $fullPath;
    }

    private function applyFilter(ImageInterface $imagine,$filter)
    {
        foreach ($filter->operations as $op)
        {
            switch ($op->type)
            {
                case 'crop':
                    $imagine->crop(new Point($op->x,$op->y),new Box($op->width,$op->height));
                    break;
            }
        }
    }



}