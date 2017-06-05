<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace RestfulBundle\Controller\Api\V3;

use CoreBundle\Helper\RestfulEnvelope;
use RestfulBundle\Validation\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');

        $oldPasswordType = new PasswordType($request->get("oldPassword"));
        $newPasswordType = new PasswordType($request->get("newPassword"));

        $e1 = $validator->validate($oldPasswordType);
        $e2 = $validator->validate($newPasswordType);
        if($e1->count() > 0 || $e2->count() > 0) {
            return RestfulEnvelope::errorResponseTemplate('invalid passwords')
                ->addErrors($e1)
                ->addErrors($e2)
                ->response();
        }

        $user =  $this->getUser();
        $encoder_service = $this->get('security.password_encoder');

        if($encoder_service->isPasswordValid($user,$oldPasswordType->getPassword()))
        {
            $new_password = $encoder_service->encodePassword($user,$newPasswordType->getPassword());
            $user->setPassword($new_password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return RestfulEnvelope::successResponseTemplate('Password Changed')->response();
        }
        return RestfulEnvelope::errorResponseTemplate('invalid passwords')->response();
    }
}
