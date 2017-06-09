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
use Recurr\Rule;
use RestfulBundle\Validation\RuleWrapper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');

        $em = $this->getDoctrine()->getManager();

        /** @var ScheduleRepository $scheduleRepository */
        $scheduleRepository = $this->get(Schedule::class);

        /** @var Schedule $schedule */
        if($schedule = $scheduleRepository->getByToken($token)) {
            $rule = new Rule($schedule->getMeta());

            $ruleType = new RuleWrapper();

            $ruleType->setByYearDay($request->get('byYearDay',$rule->getByYearDay()));
            $ruleType->setByDay($request->get('days', $rule->getByDay()));
            $ruleType->setByMonthDay($request->get('byMonthDay', $rule->getByMonthDay()));
            $ruleType->setByWeekNumber($request->get('byWeekNumber', $rule->getByWeekNumber()));
            $ruleType->setExceptionDate($request->get('exceptionDates', $rule->getExDates()));

            $ruleType->setStartTime($request->get('startTime', $ruleType->getStartTime()));
            $ruleType->setEndTime($request->get('endTime', $ruleType->getEndTime()));
            $ruleType->setStartDate($request->get('startDate', $ruleType->getStartDate()));
            $ruleType->setEndDate($request->get('endDate', $ruleType->getEndDate()));

            $errors = $validator->validate($ruleType);
            if ($errors->count() > 0)
                return RestfulEnvelope::errorResponseTemplate("Invalid Schedule")->addErrors($errors)->response();

            $em->persist($schedule);
            return RestfulEnvelope::successResponseTemplate('Schedule Entry',
                $schedule, [new DateTimeNormalizer(), new ShowNormalizer(), new DjNormalizer()])->response();
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
        /** @var ScheduleService $scheduleService */
        $scheduleService = $this->get(ScheduleService::class);

        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');

        $em = $this->getDoctrine()->getManager();

        /** @var ShowRepository $showRepository */
        $showRepository = $this->get(Show::class);

        /** @var Show $show */
        if ($show = $showRepository->getShowByTokenAndSlug($token, $slug))
        {
            $ruleType  = new RuleWrapper();

            $ruleType->setByYearDay($request->get('byYearDay',[]));
            $ruleType->setByMonthDay($request->get('byMonthDay',[]));
            $ruleType->setByWeekNumber($request->get('byWeekNumber',[]));
            $ruleType->setByDay($request->get('byDays',[]));
            $ruleType->setExceptionDate($request->get('exceptionDates',[]));

            $ruleType->setStartTime($request->get('startTime',null));
            $ruleType->setEndTime($request->get('endTime',null));
            $ruleType->setStartDate($request->get('startDate',null));
            $ruleType->setEndDate($request->get('endDate',null));

            $errors = $validator->validate($ruleType);
            if($errors->count() > 0)
                return RestfulEnvelope::errorResponseTemplate("Invalid Schedule")->addErrors($errors)->response();

            $schedule = $scheduleService->createSchedule( $ruleType->getRule(),
                Carbon::createFromFormat("YYYY-MM-DD",$ruleType->getStartDate()),
                Carbon::createFromFormat("YYYY-MM-DD",$ruleType->getEndDate()),
                Carbon::createFromFormat("HH:MM:SS",$ruleType->getStartTime()),
                Carbon::createFromFormat("HH:MM:SS",$ruleType->getEndTime()));
            $em->persist($schedule);
            $show->addSchedule($schedule);
            $em->persist($show);
            return RestfulEnvelope::successResponseTemplate('Schedule Entry',
                $schedule,[new DateTimeNormalizer(),new ShowNormalizer(), new DjNormalizer()])->response();
        }
        return RestfulEnvelope::errorResponseTemplate("Unknown Show")->setStatus(410)->response();
    }




    /**
     * @Route("/show/{token}/{slug}/schedule/{year}/{month}",
     *     options = { "expose" = true },
     *     name="get_show_schedule_month")
     * @Method({"GET"})
     */
    public function getScheduleByMonthAction(Request $request, $token, $slug, $year, $month)
    {
        /** @var ScheduleService $scheduleService */
        $scheduleService = $this->get(ScheduleService::class);

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
