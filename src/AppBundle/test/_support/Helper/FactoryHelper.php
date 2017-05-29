<?php

namespace AppBundle\Helper;
// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Module\Symfony2;
use Codeception\Test\Metadata;
use Codeception\TestInterface;
use CoreBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Stores\RepositoryStore;
use Faker\Factory;
use League\FactoryMuffin\Faker\Facade as Faker;

use PHPUnit_Framework_TestResult;


class FactoryHelper extends \Codeception\Module
{
    /**
     * @var  \League\FactoryMuffin\FactoryMuffin
     */
    protected $factory;



    public function _initialize()
    {
        /** @var Symfony2 $symfony */
        $symfony = $this->getModule('Symfony2');
        $this->factory = new FactoryMuffin(new RepositoryStore($symfony->_getEntityManager()));

        $this->factory->define(User::class)->setDefinitions([
            'email' => Faker::unique()->email(),
            'name' => Faker::unique()->name(),
            'studentId' => Faker::numerify("#########"),
            'password' => function ($object, $save) use ($symfony) {
                return $symfony->grabService('security.password_encoder')
                    ->encodePassword($object, 'password');
            },
            'confirmed' => true,
            'username' => Faker::unique()->userName()
        ]);

    }


    /**
     * @return \League\FactoryMuffin\FactoryMuffin
     */
    public function factory()
    {
        return $this->factory;
    }


}