<?php

namespace AppBundle\Helper;
// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Module\Symfony2;
use Codeception\Test\Metadata;
use Codeception\TestInterface;
use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Dj;
use CoreBundle\Entity\Post;
use CoreBundle\Entity\Show;
use CoreBundle\Entity\Staff;
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

        $this->factory->define(Post::class)->setDefinitions([
            'name' => Faker::unique()->name(),
            'slug' => Faker::unique()->slug(),
            'excerpt' => Faker::paragraph(3),
            'content' => Faker::paragraph(10),
        ]);

        $this->factory->define(Show::class)->setDefinitions([
            'name' => Faker::unique()->name(),
            'slug' => Faker::unique()->slug(),
            'description' =>Faker::paragraph(10),
            'score' => Faker::randomNumber(2)
        ]);

        $this->factory->define(Dj::class)->setDefinitions([
            'description' => Faker::paragraph(3)
        ]);

        $this->factory->define(Staff::class)->setDefinitions();

        $this->factory->define(Comment::class)->setDefinitions([
            'content' => Faker::paragraph(1)
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