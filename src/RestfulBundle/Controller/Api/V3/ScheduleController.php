<?php
namespace RestfulBundle\Controller\Api\V3;

use Carbon\Carbon;
use CoreBundle\Entity\Schedule;
use CoreBundle\Entity\Show;
use CoreBundle\Event\ScheduleBetweenEvent;
use CoreBundle\Events;
use CoreBundle\Form\ScheduleType;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Helper\ScheduleEntry;
use CoreBundle\Normalizer\DateTimeNormalizer;
use CoreBundle\Normalizer\ScheduleEntryNormalizer;
use CoreBundle\Normalizer\ShowNormalizer;
use CoreBundle\Repository\ScheduleRepository;
use CoreBundle\Repository\ShowRepository;
use CoreBundle\Service\ScheduleService;
use FOS\RestBundle\Controller\FOSRestController;
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
class ScheduleController extends FOSRestController
{
    /**
     * @Route("schedule/today",
     *     options = { "expose" = true },
     *      name="get_schedule_today")
     * @Method({"GET"})
     */
    public function getTodayScheduleAction(Request $request)
    {
        $c = new Carbon('now');
        return $this->getCurrentDateTimeAction($request,$c->year,$c->month,$c->day);
    }


    /**
     * @Route("time",
     *     options = { "expose" = true },
     *     name="get_schedule_time")
     * @Method({"GET"})
     */
    public function getTimeAction(Request $request)
    {
        return $this->view(new \DateTime('now'));
    }


    /**
     * @Route("schedule/{year}/{month}/{day}",
     *     options = { "expose" = true },
     *     name="get_schedule_by_date")
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
        return $this->view($result);
    }


    /**
     * @Route("/schedule/{token}",
     *     options = { "expose" = true },
     *     name="patch_show_schedule")
     * @Method({"PATCH"})
     */
    public function patchScheduleAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ScheduleRepository $scheduleRepository */
        $scheduleRepository = $this->get(Schedule::class);

        /** @var Schedule $schedule */
        if($schedule = $scheduleRepository->getByToken($token)) {

            $form = $this->createForm(ScheduleType::class,$schedule);
            $form->submit($request->request->all());
            if($form->isValid())
            {
                $em->persist($schedule);
                $em->flush();
            }
            return $this->view($form);
        }
        throw  $this->createNotFoundException("Can't find Show");
    }




    /**
     * @Route("/show/{token}/{slug}/schedule",
     *     options = { "expose" = true },
     *     name="post_show_schedule")
     * @Method({"POST"})
     */
    public function postScheduleAction(Request $request, $token, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $this->get(Show::class);

        /** @var Show $show */
        if ($show = $showRepository->getShowByTokenAndSlug($token, $slug))
        {
            $schedule = new Schedule();

            $form = $this->createForm(ScheduleType::class,$schedule);
            $form->submit($request->request->all());
            if($form->isValid())
            {
                $em->persist($schedule);
                $show->addSchedule($schedule);
                $em->persist($show);
                $em->flush();
            }
            return $this->view($form);
        }
        throw  $this->createNotFoundException("Unknown Schedule");
    }




    /**
     * @Route("/show/{token}/{slug}/schedule/{year}/{month}",
     *     options = { "expose" = true },
     *     name="get_show_schedule_month")
     * @Method({"GET"})
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
     * @Route("/schedule/{year}/{month}",
     *     options = { "expose" = true },
     *     name="get_schedule_month")
     * @Method({"GET"})
     */
    public function getSchedule(Request $request, $year, $month)
    {
        /** @var ScheduleRepository $scheduleRepository */
        $scheduleRepository = $this->get(Schedule::class);

        $date = Carbon::create($year,$month);
        $schedules = $scheduleRepository->getByDatetimeRange($date->copy()->startOfMonth(),$date->copy()->endOfMonth(),false);
        return $this->view(["schedules" => $schedules]);
    }

}
