<?php
namespace RestfulBundle\Controller\Api\V3;

use Carbon\Carbon;
use CoreBundle\Entity\Schedule;
use CoreBundle\Event\ScheduleBetweenEvent;
use CoreBundle\Events;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Helper\ScheduleEntry;
use CoreBundle\Normalizer\DateTimeNormalizer;
use CoreBundle\Normalizer\ScheduleEntryNormalizer;
use CoreBundle\Normalizer\ShowNormalizer;
use CoreBundle\Repository\ScheduleRepository;
use CoreBundle\Service\ScheduleService;
use RRule\RRule;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v3/")
 */
class ScheduleController extends Controller
{
    /**
     * @Route("schedule/today", options = { "expose" = true }, name="get_schedule_today")
     * @Method({"GET"})
     */
    public function getTodayScheduleAction(Request $request)
    {
        $c = new Carbon('now');
        return $this->getCurrentDateTimeAction($request,$c->year,$c->month,$c->day);
    }


    /**
     * @Route("time", options = { "expose" = true }, name="get_schedule_time")
     * @Method({"GET"})
     */
    public function getTimeAction(Request $request)
    {
        return RestfulEnvelope::successResponseTemplate('time',new \DateTime('now'),[new DateTimeNormalizer()])->response();
    }


    /**
     * @Route("schedule/{year}/{month}/{day}", options = { "expose" = true }, name="get_schedule_by_date")
     * @Method({"GET"})
     */
    public function getCurrentDateTimeAction(Request $request,$year,$month,$day)
    {
        $c = Carbon::create($year,$month,$day);

        /** @var ScheduleRepository $scheduleRepository */
        $scheduleRepository = $this->getDoctrine()->getRepository(Schedule::class);

        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->get('event_dispatcher');

        $start = $c->copy()->startOfDay();
        $end = $c->copy()->endOfDay();
        $schedules = $scheduleRepository->getByDatetime($c);

        $result = array();
        /** @var Schedule $schdule */
        foreach ($schedules as $schdule)
        {
            $dates = new ScheduleBetweenEvent($schdule,$start->copy(),$end->copy());
            $dispatcher->dispatch(Events::ON_SCHEDULE_RULE,$dates);
            if($dates->hasDates())
            {
                foreach ($dates->getDateTimes() as $date) {
                    $entry = new ScheduleEntry();
                    $entry->setDate($date);
                    $entry->setShow($schdule->getShow());
                    $entry->setLength($schdule->getShowLength());
                    $result[] = $entry;
                }
            }
        }

        return RestfulEnvelope::successResponseTemplate('Show Schedule', $result,
            [new ScheduleEntryNormalizer(),new ShowNormalizer()])->response();
    }

}
