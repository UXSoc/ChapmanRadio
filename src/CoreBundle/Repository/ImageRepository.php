<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Repository;


use Doctrine\ORM\EntityRepository;

class ImageRepository extends EntityRepository
{
    public function getImagesForShow($showId)
    {

    }

    public function getImagesForDj($showId)
    {

    }

    public function getImagesForBlog($showId)
    {

    }

    public function getImageByToken($token)
    {
        return $this->findOneBy(["token" => $token]);
    }
}