<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Genre;
use CoreBundle\Entity\Show;
use CoreBundle\Repository\UserRepository;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadShowData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();

        $genres = $manager->getRepository(Genre::class)->findAll();

        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get("core.user_repository");
        $users = $userRepository->findAll();

        for ($i = 0; $i < 20; $i++)
        {
            $show = new Show();
            $show->setName($faker->name);
            $show->setDescription($faker->paragraph(10));
            $show->setStrikeCount(0);
            $show->setAttendenceOptional(0);
            $show->setScore($faker->randomNumber(2));
            $show->setSlug($show->getName());

            /** @var Comment[] $comments */
            $comments = array();
            for($j = 0; $j < 20; $j++)
            {
                $c = new Comment();
                $c->setUser($users[array_rand($users,1)]);
                $c->setContent($faker->paragraph(3));
                $show->addComment($c);
                $manager->persist($c);
                $comments[] = $c;
            }
            for($j = 0; $j < 10; $j++)
            {
                $comments[$j]->setParentComment($comments[array_rand($comments,1)]);
                $manager->persist($comments[$j]);
            }


            for($b =0; $b < 3; $b++)
            {
                $show->addGenre($genres[array_rand($genres,1)]);
            }
            $manager->persist($show);

        }
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 3;
    }

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}