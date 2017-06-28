<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/26/17
 * Time: 8:37 PM
 */

namespace RestfulBundle\Controller\Api\V3\Dashboard;


use CoreBundle\Entity\Schedule;
use CoreBundle\Entity\Show;
use CoreBundle\Form\ScheduleType;
use CoreBundle\Repository\ScheduleRepository;
use CoreBundle\Repository\ShowRepository;
use FOS\RestBundle\Controller\FOSRestController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\BrowserKit\Request;

/**
 * @Route("/api/v3/")
 */
class ScheduleController  extends FOSRestController
{

    /**
     * @Rest\Patch("schedule/{token}",
     *     options = { "expose" = true },
     *     name="patch_show_schedule")
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
            if($form->isSubmitted() && $form->isValid())
            {
                $em->persist($schedule);
                $em->flush();
            }
            return $this->view($form);
        }
        throw  $this->createNotFoundException("Can't find Show");
    }


    /**
     * @@Rest\Post("show/{token}/{slug}/schedule",
     *     options = { "expose" = true },
     *     name="post_show_schedule")
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



}