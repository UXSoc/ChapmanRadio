<?php

namespace RestfulBundle\Controller\Api\V3;

use Carbon\Carbon;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Normalizer\DateTimeNormalizer;
use CoreBundle\Normalizer\ScheduleEntryNormalizer;
use CoreBundle\Normalizer\ShowNormalizer;
use CoreBundle\Service\ScheduleService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    public function getTodayScheduleAction()
    {
        /** @var ScheduleService $calendarService */
        $calendarService = $this->get(ScheduleService::class);
        $entires = $calendarService->getEventsForDay(new \DateTime('now'));

        return RestfulEnvelope::successResponseTemplate('Show Schedule', $entires,
            [new ScheduleEntryNormalizer(), new ShowNormalizer()])->response();
    }

    /**
     * @Route("time", options = { "expose" = true }, name="get_schedule_time")
     * @Method({"GET"})
     */
    public function getTimeAction(Request $request)
    {
        return RestfulEnvelope::successResponseTemplate('time', new \DateTime('now'), [new DateTimeNormalizer()])->response();
    }

    /**
     * @Route("schedule/{year}/{month}/{day}", options = { "expose" = true }, name="get_schedule_by_date")
     * @Method({"GET"})
     */
    public function getCurrentDateTimeAction(Request $request, $year, $month, $day)
    {
        $date = Carbon::createFromDate($year, $month, $day);

        /** @var ScheduleService $calendarService */
        $calendarService = $this->get(ScheduleService::class);
        $entires = $calendarService->getEventsForDay($date);

        return RestfulEnvelope::successResponseTemplate('Show Schedule', $entires,
            [new ScheduleEntryNormalizer(), new ShowNormalizer()])->response();
    }
}
