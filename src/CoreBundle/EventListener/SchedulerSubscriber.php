<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/6/17
 * Time: 1:32 PM
 */

namespace CoreBundle\EventListener;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use CoreBundle\Caches;
use CoreBundle\Entity\Event;
use CoreBundle\Entity\Schedule;
use CoreBundle\Event\ScheduleBetweenEvent;
use CoreBundle\Event\ScheduleChainEvent;
use CoreBundle\Events;
use CoreBundle\Helper\CacheableRule;
use CoreBundle\Helper\ScheduleEntry;
use CoreBundle\Repository\EventRepository;
use CoreBundle\Repository\ScheduleRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\Interval;

class SchedulerSubscriber implements EventSubscriberInterface
{

    private $register;
    private $cacheService;
    private $em;

    function __construct(ManagerRegistry $register,CacheItemPoolInterface  $cacheService,EntityManagerInterface $em)
    {
        $this->register = $register;
        $this->cacheService = $cacheService;
        $this->em = $em;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            ScheduleBetweenEvent::NAME =>   'onScheduleBetween',
            ScheduleChainEvent::NAME => 'onScheduleChainEvent'
        ];
    }

    private function getRuleFromCache(Schedule $schedule)
    {
        $rule = new CacheableRule($schedule->getRule());
        $updatedAt = (Carbon::instance($schedule->getUpdatedAt()))->getTimestamp();
        $ruleCache =  $this->cacheService->getItem(Caches::SCHEDULE_RULE_CACHE . $schedule->getToken() . '.' . $updatedAt);
        if($ruleCache->isHit()) {
            $rule->restoreFromCache($ruleCache->get());
        }
        $ruleCache->set($rule);
        $ruleCache->expiresAfter(new CarbonInterval(0, 0, 1, 0, 0, 0, 0));
        return $ruleCache;
    }

    /**
     * @param ScheduleBetweenEvent $event
     */
    public function onScheduleBetween(ScheduleBetweenEvent $event){
        $schedule = $event->getSchedule();
        $token  = (Carbon::instance($schedule->getUpdatedAt()))->getTimestamp();

        $ruleCache = $this->getRuleFromCache($schedule);
        $rule = $ruleCache->get();

        $start  = (Carbon::instance($event->getStart()))->copy()->format('h-m');
        $end  = (Carbon::instance($event->getEnd()))->copy()->format('h-m');
        $dayCache =  $this->cacheService->getItem(Caches::SCHEDULE_RULE_CACHE_BETWEEN . $schedule->getToken() . '.'. $token .'.'.$start . '.' . $end);
        if(!$dayCache->isHit())
        {
            $dayCache->set($rule->getOccurrencesBetween($event->getStart(),$event->getEnd()));
            $dayCache->expiresAfter(new CarbonInterval(0, 0, 0, 0, 1, 0, 0));
            $this->cacheService->save($dayCache);
        }

        $event->setDateTimes($dayCache->get());

        if($rule->isCacheExausted() ) {
            $ruleCache->set($rule->getCache());
            $this->cacheService->saveDeferred($ruleCache);
        }
    }

    public function onScheduleChainEvent(ScheduleChainEvent $event){
        /** @var ScheduleRepository $scheduleRepository */
        $scheduleRepository = $this->em->getRepository(Schedule::class);
        $current = Carbon::instance( $event->getCurrent());

        $showChain = [];

        $count = $event->getCount();
        do {
            $scheduleItems = $scheduleRepository->getByDatetimeRange($current,$current,false,null);
            $start = $current->copy()->startOfDay();
            $end = $current->copy()->endOfDay();
            /** @var Schedule $schedule */
            foreach ($scheduleItems as $schedule) {
                $betweenEvent = new ScheduleBetweenEvent($schedule,$start,$end);
                $this->onScheduleBetween($betweenEvent);
                foreach ($betweenEvent->getDateTimes() as $o) {
                    $date = Carbon::instance($o);
                    if ($date->lessThan($current) && $event->getIncludeCurrent()) {
                        $endTime = $date->copy()->add(CarbonInterval::second($schedule->getShowLength()));
                        if ($endTime->greaterThan($current)) {
                            $showChain[] = new ScheduleEntry($date, $schedule);
                        }
                    } else {
                        $showChain[] = new ScheduleEntry($date, $schedule);
                    }
                }
            }

            usort($showChain,function (ScheduleEntry $a,ScheduleEntry $b) {
                if ($a->getShowDate() == $b->getShowDate()) {
                    return 0;
                }
                return $a->getShowDate() < $b->getShowDate() ? -1 : 1;
            });

            while ($count > 0 && count($showChain) > 0)
            {
                $event->addEntry(array_shift($showChain));
                $count--;
            }
            $current->addDay();
        } while ($count > 0);
    }


}