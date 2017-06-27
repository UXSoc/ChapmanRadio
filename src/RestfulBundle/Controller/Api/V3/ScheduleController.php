<?php
namespace RestfulBundle\Controller\Api\V3;

use Carbon\Carbon;
use CoreBundle\Entity\Schedule;
use CoreBundle\Entity\Show;
use CoreBundle\Event\ScheduleBetweenEvent;
use CoreBundle\Events;
use CoreBundle\Form\ScheduleType;
use CoreBundle\Helper\ScheduleEntry;
use CoreBundle\Repository\ScheduleRepository;
use CoreBundle\Repository\ShowRepository;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("/api/v3/")
 */
class ScheduleController extends FOSRestController
{

    /**
     * @Route("time",
     *     options = { "expose" = true },
     *     name="get_schedule_time")
     * @Method({"GET"})
     */
    public function getTimeAction(Request $request)
    {
        return $this->view(['time' => new \DateTime('now')]);
    }


    /**
     * @Rest\Get("schedule/{year}/{month}/{day}",
     *     options = { "expose" = true },
     *     name="get_schedule_by_date")
     * @Rest\View(serializerGroups={"list"})
     */
    public function getScheduleByDayYearMonthAction(Request $request,$year,$month,$day)
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
        return $this->view(['scheduleEntries' => $result]);
    }



    /**
     * @Rest\Get("/show/{token}/{slug}/schedule/{year}/{month}",
     *     options = { "expose" = true },
     *     name="get_show_schedule_month")
     */
    public function getScheduleByMonthAction(Request $request, $token, $slug, $year, $month)
    {
        $date = Carbon::create($year,$month);

        /** @var ShowRepository $showRepository */
        $showRepository = $this->get(Show::class);

        /** @var ScheduleRepository $scheduleRepository */
        $scheduleRepository = $this->get(Schedule::class);

        /** @var Show $show */
        if ($show = $showRepository->getShowByTokenAndSlug($token, $slug))
        {
            $schedules = $scheduleRepository->getByShowDatetimeRange($date->copy()->startOfMonth(),$date->copy()->endOfMonth(),$show,false);
            return $this->view(["schedules" => $schedules]);

        }
        throw  $this->createNotFoundException("Show not found");
    }

    /**
     * @Rest\Get("/schedule/{year}/{month}",
     *     options = { "expose" = true },
     *     name="get_schedule_month")
     */
    public function getScheduleMonthAction(Request $request, $year, $month)
    {
        /** @var ScheduleRepository $scheduleRepository */
        $scheduleRepository = $this->get(Schedule::class);

        $date = Carbon::create($year,$month);
        $schedules = $scheduleRepository->getByDatetimeRange($date->copy()->startOfMonth(),$date->copy()->endOfMonth(),false);
        return $this->view(["schedules" => $schedules]);
    }

}
