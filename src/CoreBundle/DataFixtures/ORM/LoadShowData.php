<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
use Carbon\Carbon;
use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Genre;
use CoreBundle\Entity\Show;
use CoreBundle\Entity\Tag;
use CoreBundle\Entity\User;
use CoreBundle\Repository\UserRepository;
use CoreBundle\Service\ScheduleService;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Recurr\Frequency;
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

        /** @var ScheduleService $calendar */
        $calendar = $this->container->get(ScheduleService::class);

        $genres = $manager->getRepository(Genre::class)->findAll();

        /** @var User[] $users */
        $users = $manager->getRepository(User::class)->findAll();
        $tags = $manager->getRepository(Tag::class)->findAll();

        for ($i = 0; $i < 20; $i++)
        {
            $show = new Show();
            $show->setName($faker->name);
            $show->setDescription($faker->paragraph(20));
            $show->setStrikeCount(0);
            $show->setAttendenceOptional(0);
            $show->setScore($faker->randomNumber(2));
            $show->setSlug($show->getName());
            $show->setExcerpt($faker->paragraph(1));

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
                /** @var Genre $genre */
                $genre = $genres[array_rand($genres,1)];
                if(!in_array($genre->getGenre(),$show->getGenres()->getKeys()))
                    $show->addGenre($genre);

            }

            for ($c = 0; $c < 10; $c++)
            {
                /** @var Tag $tag */
                $tag = $tags[array_rand($tags,1)];
                if(!in_array($tag->getTag(),$show->getTags()->getKeys()))
                    $show->addTag($tag);
            }

            $time = random_int(1,5);
            for ($k = 0; $k < $time; $k++) {

                $schedule = null;
                $rule = null;
                switch (random_int(0, 5)) {
                    case 0:
                        $rule = (new \Recurr\Rule())->setByWeekNumber([0, 1])->setFreq('WEEKLY');
                        break;
                    case 1:
                        $rule = (new \Recurr\Rule())->setFreq('DAILY');
                        break;
                    case 2:
                        $rule = (new \Recurr\Rule())->setByMonth([1, 2])->setByDay(['MO', 'TU']);
                        break;
                    case 3:
                        $rule = (new \Recurr\Rule())->setFreq('WEEKLY')->setByDay(['MO']);
                        break;
                    case 4:
                        $rule = (new \Recurr\Rule())->setFreq('MONTHLY')->setByWeekNumber([1])->setByDay(['MO']);
                        break;
                    case 5:
                        $rule = (new \Recurr\Rule())->setFreq('WEEKLY')->setByDay(['TU', 'TH']);
                        break;
                }

                $st = new Carbon($faker->time('H:i:s', 'now'));
                $end = $faker->dateTimeBetween($st->copy(), $st->copy()->endOfDay());


                $sch = $calendar->createSchedule($rule,
                    $faker->dateTimeBetween('-1 months', 'now'),
                    $faker->dateTimeBetween('now', '1 months'),
                    $st,
                    $end);

                $manager->persist($sch);
                $show->addSchedule($sch);
            }
            for ($k = 0; $k < 20; $k++) {
                $st = new Carbon($faker->time('H:i:s', 'now'));
                $end = $faker->dateTimeBetween($st->copy(), $st->copy()->endOfDay());
                $temp  =$faker->dateTimeBetween('-1 months', '1 months');
                $sch = $calendar->createSchedule(new \Recurr\Rule(),
                    $temp,
                    $temp,
                    $st,
                    $end);

                $manager->persist($sch);
                $show->addSchedule($sch);
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