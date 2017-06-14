<?php

namespace RestfulBundle\Controller\Api\V3\Secure;

use Carbon\Carbon;
use CoreBundle\Entity\Schedule;
use CoreBundle\Entity\Show;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Normalizer\DateTimeNormalizer;
use CoreBundle\Normalizer\DjNormalizer;
use CoreBundle\Normalizer\ShowNormalizer;
use CoreBundle\Repository\ScheduleRepository;
use CoreBundle\Repository\ShowRepository;
use CoreBundle\Service\ScheduleService;
use CoreBundle\Validation\ScheduleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Security("has_role('ROLE_STAFF')")
 * @Route("/api/v3/private")
 */
class ScheduleController extends Controller
{


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
                return RestfulEnvelope::successResponseTemplate('Comment Added',$schedule,[])->response();
            }
            return RestfulEnvelope::errorResponseTemplate("Invalid Schedule")->addFormErrors($form)->response();
        }

        return RestfulEnvelope::errorResponseTemplate("Unknown Show")->setStatus(410)->response();
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
                return RestfulEnvelope::successResponseTemplate('Schedule Updated',$schedule,[])->response();
            }
            return RestfulEnvelope::errorResponseTemplate("Invalid Schedule")->addFormErrors($form)->response();
        }
        return RestfulEnvelope::errorResponseTemplate("Unknown Schedule")->setStatus(410)->response();
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
            return RestfulEnvelope::successResponseTemplate('Schedule',
                $schedules,[new DateTimeNormalizer(),new ShowNormalizer(), new DjNormalizer()])->response();

        }
        return RestfulEnvelope::errorResponseTemplate("Unknown Show")->setStatus(410)->response();
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
        return RestfulEnvelope::successResponseTemplate('Schedule',
            $schedules,[new DateTimeNormalizer(),new ShowNormalizer(), new DjNormalizer()])->response();
    }

}
