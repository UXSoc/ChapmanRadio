<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace AppBundle\Controller\Api\V3;

use AppBundle\Validation\ChangePasswordType;
use AppBundle\Validation\PasswordType;
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

        $oldPasswordType = new PasswordType($request->get("oldPassword"));
        $newPasswordType = new PasswordType($request->get("newPassword"));

        $e1 = $this->validateEntity($oldPasswordType);
        $e2 = $this->validateEntity($newPasswordType);
        if($e1->count() > 0 | $e2->count() > 0) {
            $e = new ErrorWrapper("Unknown User");
            $e->addErrors($e1);
            $e->addErrors($e2);
            return $this->restful([new WrapperNormalizer()],$e,400);
        }


        $user =  $this->getUser();
        $encoder_service = $this->get('security.password_encoder');

        if(!$encoder_service->isPasswordValid($user,$oldPasswordType->getPassword()))
        {
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Invalid Password"),400);
        }
        else
        {
            $new_password = $encoder_service->encodePassword($user,$newPasswordType->getPassword());
            $user->setPassword($new_password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->restful([new WrapperNormalizer(),new UserNormalizer()],new SuccessWrapper($user,"Password Changed"),200);
        }
    }
}