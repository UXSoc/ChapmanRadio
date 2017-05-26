<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/25/17
 * Time: 1:55 PM
 */

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BlogCategory
 *
 * @ORM\Table(name="tag")
 * @ORM\Entity()
 *
 */
class Tag
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
     * @ORM\Column(name="tag",  type="string", length=100, nullable=false, unique=true, nullable=false)
     */
    private $tag;

    public function getId()
    {
        return $this->id;
    }

    public  function getTag()
    {
        return $this->tag;
    }

    public function setTag($tag)
    {
        $this->tag = $tag;
    }
}