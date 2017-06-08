<?php

// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/18/17
 * Time: 2:46 PM.
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Sets the container.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();

        //generate DJs
        for ($i = 0; $i < 50; $i++) {
            $user = $this->generateUser($faker);
            $dj = $this->generateDj($faker);
            $manager->persist($user);
            $dj->setUser($user);
            $manager->persist($dj);
        }
        $manager->flush();

        //generate users
        for ($i = 0; $i < 500; $i++) {
            $user = $this->generateUser($faker);
            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @param \Faker\Generator $faker
     *
     * @return \CoreBundle\Entity\User
     */
    private function generateUser($faker)
    {
        $user = new \CoreBundle\Entity\User();
        $user->setName($faker->unique()->name);
        $user->setEmail($faker->unique()->email);
        $user->setStudentId($faker->numerify('#########'));
        $user->updateLastLogin();
        $user->setConfirmed(true);
        $user->setUsername($faker->userName);

        $password = $this->container->get('security.password_encoder')
            ->encodePassword($user, 'password');
        $user->setPassword($password);
        $user->setPhone($faker->phoneNumber);

        return $user;
    }

    /**
     * @param \Faker\Generator $faker
     *
     * @return \CoreBundle\Entity\Dj
     */
    private function generateDj($faker)
    {
        $dj = new \CoreBundle\Entity\Dj();
        $dj->setStrikeCount($faker->randomElement(0, 1, 2, 3));
        $dj->setDescription($faker->paragraphs($nb = 3, $asText = true));
        $dj->setAttendWorkshop($faker->boolean());

        return $dj;
    }

    /**
     * Get the order of this fixture.
     *
     * @return int
     */
    public function getOrder()
    {
        return 0;
    }
}
