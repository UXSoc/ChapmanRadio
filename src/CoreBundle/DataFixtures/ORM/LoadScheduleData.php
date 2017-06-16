<?php
use CoreBundle\Entity\Schedule;
use CoreBundle\Entity\Show;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadScheduleData  extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $shows = $manager->getRepository(Show::class)->findAll();

        for ($i = 0; $i < 50; $i++) {
            /** @var Show $show */
            $show = $shows[array_rand($shows, 1)];

            $time = random_int(1,10);
            for ($k = 0; $k < $time; $k++) {
                $schedule = new Schedule();


                switch (random_int(0, 3)) {
                    case 0:

                        $schedule->setStartDate( $faker->dateTimeBetween('-1 months', 'now'));
                        $schedule->setEndDate($faker->dateTimeBetween('now', '1 months'));
                        $schedule->setFrequency('WEEKLY');
                        $schedule->setByDay(['MO','TU']);
                        break;
                    case 1:
                        $schedule->setStartDate( $faker->dateTimeBetween('-1 months', '1 months'));
                        $schedule->setEndDate($schedule->getStartDate());
                        break;
                    case 2:
                        $schedule->setStartDate( $faker->dateTimeBetween('-1 months', 'now'));
                        $schedule->setEndDate($faker->dateTimeBetween('now', '1 months'));
                        $schedule->setFrequency('DAILY');
                        $schedule->setInterval(random_int(1, 5));
                        break;
                    case 3:
                        $schedule->setStartDate( $faker->dateTimeBetween('-1 months', 'now'));
                        $schedule->setEndDate($faker->dateTimeBetween('now', '1 months'));
                        $schedule->setByMonth([1,3,4]);
                        $schedule->setInterval(random_int(1, 5));
                        break;
                }

                $schedule->setByHour(random_int(0,23));
                $schedule->setByMinute(random_int(0,59));
                $schedule->setShowLenght(random_int(5000,100000));

                $manager->persist($schedule);
                $show->addSchedule($schedule);
                $manager->persist($show);
            }
            $manager->flush();
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 4;
    }
}