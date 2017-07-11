<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/7/17
 * Time: 2:52 AM
 */

namespace CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use CoreBundle\Validation\Constraints As CoreAssert;
use JMS\Serializer\Annotation As JMS;

/**
 * Media
 *
 * @ORM\Table(name="media")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\MediaRepository")
 *
 * @ORM\HasLifecycleCallbacks
 */
class Media
{
    const MEDIA_PNG = "image/png";
    const MEDIA_JPEG = "image/jpeg";

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Exclude
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="token", type="string",length=20, nullable=false,unique=true)
     * @JMS\Groups({"detail","list"})
     */
    private $token;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $updatedAt;


    /**
     * @var string
     * @ORM\Column(name="title", type="text", length=6000, nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $title;


    /**
     * @var string
     * @ORM\Column(name="caption", type="text", length=6000, nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $caption;



    /**
     * @var string
     * @ORM\Column(name="alt_text", type="text", length=6000, nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $altText;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", length=6000, nullable=true)
     * @JMS\Groups({"detail","list"})
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=200, nullable=false)
     * @JMS\Exclude
     */
    private $source;


    /**
     * @var string
     *
     * @ORM\Column(name="mime", type="string", length=70, nullable=false)
     * @JMS\Groups({"detail","list"})
     */
    private $mime;

    /**
     * @var UploadedFile
     * @Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"image/png", "image/jpeg"},
     *     mimeTypesMessage = "Please upload a valid image file"
     * )
     * @Assert\NotBlank()
     * @JMS\Exclude
     */
    private $file;


    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     * @JMS\Groups({"detail","list"})
     */
    private $author;


    /**
     * @var bool
     *
     * @ORM\Column(name="hidden", type="boolean")
     * @JMS\Groups({"detail","list"})
     */
    private $hidden = false;


    /**
     * @var string
     *
     * @ORM\Column(name="filter", type="object")
     * @JMS\Exclude
     */
    private $filter;


    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Post",mappedBy="media")
     * @JMS\Exclude
     */
    private $post;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Show",mappedBy="media")
     * @JMS\Exclude
     */
    private $show;


    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->updatedAt = new \DateTime('now');

        if ($this->createdAt === null) {
            $this->token = substr(bin2hex(random_bytes(12)),10);
            $this->createdAt = new \DateTime('now');
        }
    }

    public function setAuthor(User $author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getId() {
        return $this->id;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setCaption($caption){
        $this->caption = $caption;
    }

    public function getCaption()
    {
        return $this->caption;
    }

    public function setAltText($altText)
    {
        $this->altText = $altText;
    }

    public function getAltText()
    {
        return $this->altText;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function getSource() {
        return $this->source;
    }

    public function setMime($mime)
    {
        $this->mime = $mime;
    }

    public function getMime()
    {
        return $this->mime;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setHidden($Hidden)
    {
        $this->hidden  = $Hidden;
    }

    public function getHidden(){
        return $this->hidden;
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function getBlog()
    {
        return $this->post->first();
    }

    public function getShow()
    {
        return $this->show->first();
    }

}