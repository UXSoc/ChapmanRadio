<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/25/17
 * Time: 2:43 PM
 */

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Genre
 *
 * @ORM\Table(name="genre")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\GenreRepository")
 *
 */
class Genre
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
     * @ORM\Column(name="genre",  type="string", length=100, nullable=false, unique=true, nullable=false)
     */
    private $genre;

    public function getId()
    {
        return $this->id;
    }

    public  function getGenre()
    {
        return $this->genre;
    }

    public function setGenre($genre)
    {
        $this->genre = $genre;
    }
}