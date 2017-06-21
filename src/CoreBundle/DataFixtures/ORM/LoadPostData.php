<?php
use CoreBundle\Entity\Post;
use CoreBundle\Entity\Category;
use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Tag;
use CoreBundle\Entity\User;
use CoreBundle\Repository\UserRepository;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPostData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $users =  $manager->getRepository(User::class)->findAll();

        $categories = $manager->getRepository(Category::class)->findAll();
        $tags = $manager->getRepository(Tag::class)->findAll();

        for ($i = 0; $i < 100; $i++)
        {
            $post = new Post();
            $post->setName($faker->name);
            $post->setContent('{"ops":[{"insert":"' . $faker->paragraph(10) . '"}]}');
            $post->setAuthor($users[array_rand($users,1)]);
            $post->setPinned($faker->boolean());
            $post->setExcerpt($faker->paragraph(1));
            $post->setSlug($post->getName());

            /** @var Comment[] $comments */
            $comments = array();
            for($j = 0; $j < 20; $j++)
            {
                $c = new Comment();
                $c->setUser($users[array_rand($users,1)]);
                $c->setContent($faker->paragraph(3));
                $post->addComment($c);
                $manager->persist($c);
                $comments[] = $c;
            }
            for($j = 0; $j < 10; $j++)
            {
                $comments[$j]->setParentComment($comments[array_rand($comments,1)]);
                $manager->persist($comments[$j]);
            }

            for($b = 0; $b < 10; $b++)
            {
                /** @var Tag $tag */
                $tag = $tags[array_rand($tags,1)];
                /** @var Category $category */
                $category = $categories[array_rand($categories,1)];

                if(!in_array($tag->getTag(),$post->getTags()->getKeys()))
                    $post->addTag($tag);
                if(!in_array($category->getCategory(),$post->getCategories()->getKeys()))
                    $post->addCategory($category);
            }
            $manager->persist($post);
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