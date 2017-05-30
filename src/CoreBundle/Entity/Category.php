<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/25/17
 * Time: 1:55 PM
 */

namespace CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * BlogCategory
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\CategoryRepository")
 *
 */
class Category
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
     * @ORM\Column(name="category",  type="string", length=100, nullable=false, unique=true)
     */
    private $category;

    public function getId()
    {
        return $this->id;
    }

    public  function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }
}