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
use CoreBundle\Entity\Schedule;
use CoreBundle\Event\CommentEvent;
use CoreBundle\Event\ScheduleEvent;
use CoreBundle\Events;
use Psr\Cache\CacheItemPoolInterface;
use Recurr\RecurrenceCollection;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\BeforeConstraint;
use RRule\RRule;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SchedulerSubscriber implements EventSubscriberInterface
{

    private $register;
    private $cacheService;

    function __construct(RegistryInterface $register,CacheItemPoolInterface  $cacheService)
    {
        $this->register = $register;
        $this->cacheService = $cacheService;
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
            Events::ON_SCHEDULE_RULE =>   'onScheduleRule'
        ];
    }

    /**
     * @param ScheduleEvent $event
     * @return \DateTime[] mixed
     */
    public function onScheduleRule(ScheduleEvent $event){
        $schedule = $event->getSchedule();
        $token  = (new Carbon($schedule->getUpdatedAt()))->toCookieString();
        $ruleCache =  $this->cacheService->getItem(Caches::SCHEDULE_RULE_CACHE . $token);
        if(!$ruleCache->isHit()) {
           $rule = new RRule($schedule->getRule());
           $ruleCache->set($rule);
        }
        $ruleCache->expiresAfter(new CarbonInterval(0, 0, 1, 0, 0, 0, 0));

        $start  = (new Carbon($event->getStart()))->toCookieString();
        $end  = (new Carbon($event->getEnd()))->toCookieString();
        $dayCache =  $this->cacheService->getItem(Caches::SCHEDULE_RULE_CACHE_BETWEEN . $token .'.'.$start . '.' . $end);
        if(!$dayCache->isHit())
        {
            /** @var RRule $c */
            $c = $ruleCache->get();
            $dayCache->set($c->getOccurrencesBetween($event->getStart(),$event->getEnd()));
        }
        $dayCache->expiresAfter(new CarbonInterval(0, 0, 1, 0, 0, 0, 0));

        $this->cacheService->save($ruleCache);
        $this->cacheService->save($dayCache);
        return $dayCache->get();

    }



}