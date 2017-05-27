<?php
use CoreBundle\Entity\Blog;
use CoreBundle\Entity\Category;
use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Tag;
use CoreBundle\Entity\User;
use CoreBundle\Repository\UserRepository;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadBlogData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();

        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get("core.user_repository");
        $users = $userRepository->findAll();

        $categories = $manager->getRepository(Category::class)->findAll();
        $tags = $manager->getRepository(Tag::class)->findAll();

        for ($i = 0; $i < 20; $i++)
        {
            $blog = new Blog();
            $blog->setName($faker->name);
            $blog->setContent($faker->paragraph(10));
            $blog->setAuthor($users[array_rand($users,1)]);
            $blog->setIsPinned($faker->boolean());
            $blog->setExcerpt($faker->paragraph(1));
            $blog->setSlug($blog->getName());

            /** @var Comment[] $comments */
            $comments = array();
            for($j = 0; $j < 20; $j++)
            {
                $c = new Comment();
                $c->setUser($users[array_rand($users,1)]);
                $c->setContent($faker->paragraph(3));
                $blog->addComment($c);
                $manager->persist($c);
                $comments[] = $c;
            }
            for($j = 0; $j < 10; $j++)
            {
                $comments[$j]->setParentComment($comments[array_rand($comments,1)]);
                $manager->persist($comments[$j]);
            }


            for($b = 0; $b < 15; $b++)
            {
                $blog->addTag($tags[array_rand($tags,1)]);
                $blog->addCategory($categories[array_rand($categories,1)]);
            }
            $manager->persist($blog);
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
}