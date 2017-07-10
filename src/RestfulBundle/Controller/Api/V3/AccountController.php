<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Entity\Image;
use CoreBundle\Entity\User;
use CoreBundle\Event\ImageDeleteEvent;
use CoreBundle\Event\ImageSaveEvent;
use CoreBundle\Events;
use CoreBundle\Form\ProfileImageType;
use CoreBundle\Form\ResetPasswordType;
use FOS\RestBundle\Controller\FOSRestController;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("/api/v3")
 */
class AccountController extends FOSRestController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @Rest\Post("/account/new-password",
     *     options = { "expose" = true },
     *     name="post_account_password")
     */
    public function postChangePasswordAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();
        /** @var UserPasswordEncoder $passwordEncoder */
        $passwordEncoder = $this->get('security.password_encoder');

        $form = $this->createForm(ResetPasswordType::class, null, [
            'user' => $user]);
        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $new_password = $passwordEncoder->encodePassword($user, $data["newPassword"]);
            $user->setPassword($new_password);
            $em->persist($user);
            $em->flush();
        }
        return $this->view($form);
    }


    /**
     * @Security("has_role('ROLE_USER')")
     * @Rest\Post("/account/profile/image",
     *     options = { "expose" = true },
     *     name="post_account_profile_image")
     */
    public function postProfileImageAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileImageType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->get('event_dispatcher');

            $profile = $user->getProfile();
            if ($profile->getImage()) {
                $dispatcher->dispatch(ImageDeleteEvent::NAME, new ImageDeleteEvent($profile->getImage()));
            }

            $data = $form->getData();
            /** @var Image $image */
            $image = $data['image'];

            $event = new ImageSaveEvent($image, array(), function (ImageInterface $image) use ($data) {
                $image->crop(new Point($data['x'], $data['y']), new Box($data['width'], $data['height']));
                $image->resize(new Box(200, 200));
            });
            $dispatcher->dispatch(ImageSaveEvent::NAME, $event);
            $profile->setImage($event->getImage());
            $em->persist($user);
            $em->flush();
            return $this->view();
        }
        return $this->view($form);
    }

}
