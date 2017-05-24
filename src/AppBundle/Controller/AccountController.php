<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace AppBundle\Controller;

use AppBundle\Validation\ChangePasswordType;
use CoreBundle\Controller\BaseController;
use CoreBundle\Helper\RestfulError;
use CoreBundle\Helper\RestfulHelper;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class AccountController extends BaseController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/ajax/user/new-password", options = { "expose" = true }, name="ajax_new_password", )
     * @Method({"POST"})
     */
    public  function  postChangePasswordAction(Request $request)
    {

        $mapping = $this->getJsonPayloadAsMapping();
        /** @var ChangePasswordType $changePasswordType */
        $changePasswordType = $this->denromalizeMapping($mapping,ChangePasswordType::class);
        $errors = $this->validateEntity($changePasswordType);
        $additionalErrors = [];

        if(count($errors) == 0)
        {
            $user =  $this->getUser();
            $encoder_service = $this->get('security.password_encoder');

            if(!$encoder_service->isPasswordValid($user,$changePasswordType->getOldPassword()))
            {
                $additionalErrors[] = new RestfulError("oldPassword","Invalid Password");
            }
            else
            {
                $new_password = $encoder_service->encodePassword($user,$changePasswordType->getNewPassword());
                $user->setPassword($new_password);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                return RestfulHelper::success("Password Changed");
            }
        }
        return RestfulHelper::error(400,"Couldn't change password",$errors,$additionalErrors);
    }
}