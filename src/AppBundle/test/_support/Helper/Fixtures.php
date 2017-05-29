<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/28/17
 * Time: 3:30 PM
 */

namespace AppBundle\Helper;


use Codeception\Module;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class Fixtures extends Module
{
    /**
     * @param AbstractFixture[] $fixture
     */
    public function loadFixture($fixture)
    {
        $loader = new Loader();
        foreach ($fixture as $item) {
            $loader->addFixture($item);
        }
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->getModule("Doctrine2")->_getEntityManager(), $purger);
        $executor->execute($loader->getFixtures());
    }
}