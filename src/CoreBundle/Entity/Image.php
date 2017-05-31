<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Keygen\Keygen;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\ImageRepository")
 *
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=200, nullable=false)
     */
    private $source;


    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=10, nullable=false)
     */
    private $token;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;


    /**
     * @var UploadedFile
     * @Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"image/png", "image/jpeg"},
     *     mimeTypesMessage = "Please upload a valid image file"
     * )
     */
    private $image;


    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        if ($this->createdAt == null) {
            $this->token = Keygen::alphanum(10)->generate();
            $this->createdAt = new \DateTime('now');
        }
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return UploadedFile
     */
    public function getImage()
    {
        return $this->image;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getToken()
    {
        return $this->token;
    }


}

