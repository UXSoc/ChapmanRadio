<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace AppBundle\Controller\Api\V3;

use AppBundle\Validation\ChangePasswordType;
use CoreBundle\Controller\BaseController;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\UserNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api/v3")
 */
class AccountController extends BaseController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/account/new-password", options = { "expose" = true }, name="post_account_password")
     * @Method({"POST"})
     */
    public  function  patchChangePasswordAction(Request $request)
    {

        $changePasswordType = new ChangePasswordType();

        $changePasswordType->setOldPassword($request->get("oldPassword"));
        $changePasswordType->setNewPassword($request->get("newPassword"));

        $errors = $this->validateEntity($changePasswordType);
        if($errors->count() > 0) {
            $e = new ErrorWrapper("Unknown User");
            $e->addErrors($errors);
            return $this->restful([new WrapperNormalizer()],$e,400);
        }


        $user =  $this->getUser();
        $encoder_service = $this->get('security.password_encoder');

        if(!$encoder_service->isPasswordValid($user,$changePasswordType->getOldPassword()))
        {
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Invalid Password"),400);
        }
        else
        {
            $new_password = $encoder_service->encodePassword($user,$changePasswordType->getNewPassword());
            $user->setPassword($new_password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->restful([new WrapperNormalizer(),new UserNormalizer()],new SuccessWrapper($user,"Password Changed"),400);
        }
    }
}