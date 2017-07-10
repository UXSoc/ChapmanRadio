<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/6/17
 * Time: 8:53 PM
 */

namespace CoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Entity()
 * @ORM\Table(name="show_meta")
 *
 */
class ShowMeta extends BaseMeta
{

    /**
     * @var Schedule
     *
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="meta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="show_id", referencedColumnName="id")
     * })
     */
    private $show;


    public function setShow($show)
    {
        $this->show = $show;
    }

    public function getShow()
    {
        return $this->show;
    }
}