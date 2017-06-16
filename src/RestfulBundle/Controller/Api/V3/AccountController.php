<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Entity\User;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Validation\Items\ResetPassword;
use CoreBundle\Validation\ResetPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v3")
 */
class AccountController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/account/new-password",
     *     options = { "expose" = true },
     *     name="post_account_password")
     * @Method({"POST"})
     */
    public  function  patchChangePasswordAction(Request $request)
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
        if($form->isValid())
        {
            $data = $form->getData();

            $new_password = $passwordEncoder->encodePassword($user,$data["newPassword"]);
            $user->setPassword($new_password);
            $em->persist($user);
            $em->flush();
            return RestfulEnvelope::successResponseTemplate('Password Changed')->response();
        }
        return RestfulEnvelope::errorResponseTemplate("Invalid Password")->setStatus(410)->addFormErrors($form)->response();

    }
}
