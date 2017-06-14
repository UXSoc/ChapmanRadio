<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Service;


use Carbon\Carbon;
use Carbon\CarbonInterval;
use CoreBundle\Caches;
use CoreBundle\Entity\Event;
use CoreBundle\Entity\Schedule;
use CoreBundle\Entity\Show;
use CoreBundle\Helper\ScheduleEntry;
use CoreBundle\Repository\ScheduleRepository;
use CoreBundle\Repository\ShowRepository;
use DateTime;
use Psr\Cache\CacheItemPoolInterface;
use Recurr\Exception;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\BeforeConstraint;
use Recurr\Transformer\Constraint\BetweenConstraint;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ScheduleService
{


    private  $registry;
    private $cacheService;

    function __construct(RegistryInterface $register,CacheItemPoolInterface  $cacheService)
    {
        $this->registry = $register;
        $this->cacheService= $cacheService;
    }

    public function createSchedule(Rule $rule,DateTime $startDate, DateTime $endDate,DateTime $startTime,DateTime $endTime)
    {
        if($startDate > $endDate)
            throw new \Exception("start date has to less then end date");
        if($startTime > $endTime)
            throw new \Exception("start time has to be less then end time");

        $schedule = new Schedule();
        $schedule->setStartDate($startDate);
        $schedule->setEndDate($endDate);

        $schedule->setStartTime($startTime);
        $schedule->setEndTime($endTime);
        $schedule->setMeta($rule);
        return $schedule;
    }

    public function getEventsForDay(DateTime $day,$archive = null,$profanity = null)
    {
        $start = Carbon::instance($day)->copy()->startOfDay();
        /** @var ScheduleRepository $scheduleRepository */
        $scheduleRepository = $this->registry->getRepository(Schedule::class);
        $schedules = $scheduleRepository->getByDatetime($start,$archive,$profanity);

        return $this->getScheduleResult($day,$schedules);
    }

    private function getScheduleResult(DateTime $day, $schedule_entries)
    {

        $key = Caches::SCHEDULE_EVENTS. substr(hash('sha256',json_encode($schedule_entries)),0,10) .'.'. Carbon::instance($day)->toDateString() ;
        $entries =[];
        $cacheItem =  $this->cacheService->getItem($key);
        if(!$cacheItem->isHit()) {


            $start = Carbon::instance($day)->copy()->startOfDay();

            $end = Carbon::instance($day)->copy()->endOfDay();
            $transformer = new ArrayTransformer();

            $c = new BetweenConstraint($start, $end, true);

            /** @var Schedule $schedule */
            foreach ($schedule_entries as $schedule) {

                $rule = $schedule->getMeta();
                $result = $transformer->transform($rule, $c);
                if ($result->count() > 0) {
                    $entry = new ScheduleEntry();

                    $entry->setShow($schedule->getShow());
                    $entry->setDate($day);
                    $entry->setStartTime($schedule->getStartTime());
                    $entry->setEndTime($schedule->getEndTime());
                    $entries[] = $entry;

                }
            }

            $cacheItem->set($entries);
            $cacheItem->expiresAfter(new CarbonInterval(0, 0, 1, 0, 0, 0, 0));
            $this->cacheService->save( $cacheItem);
        }
        else
        {
            /** @var  $items */
            $items = $cacheItem->get();

            /** @var ShowRepository $showRepository */
            $showRepository = $this->registry->getRepository(Show::class);
            /** @var ScheduleEntry $item */
            foreach ($items as $item)
            {
                //refreshes show items from cache
                $item->setShow($showRepository->find($item->getShow()->getId()));
            }
            return $items ;
        }
        return $entries;
    }


    public function deleteEvent(Event $event,$future= false)
    {

    }
}
