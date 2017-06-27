<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Entity\User;
use CoreBundle\Form\ResetPasswordType;
use FOS\RestBundle\Controller\FOSRestController;
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
    public function  postChangePasswordAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user =  $this->getUser();
        /** @var UserPasswordEncoder $passwordEncoder */
        $passwordEncoder = $this->get('security.password_encoder');

        $form = $this->createForm(ResetPasswordType::class,null,[
            'password_encoder' => $passwordEncoder,
            'user' => $user]);
        $form->submit($request->request->all());
        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $new_password = $passwordEncoder->encodePassword($user,$data["newPassword"]);
            $user->setPassword($new_password);
            $em->persist($user);
            $em->flush();
        }
        return $this->view($form);
     }
}
