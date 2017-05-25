<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace AppBundle\Controller\Api\V3;

use AppBundle\Validation\ChangePasswordType;
use CoreBundle\Controller\BaseController;
use CoreBundle\Helper\RestfulError;
use CoreBundle\Helper\RestfulHelper;
use CoreBundle\Helper\RestfulJsonResponse;
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
     * @Route("/account/new-password", options = { "expose" = true }, name="patch_account_password")
     * @Method({"PATCH"})
     */
    public  function  patchChangePasswordAction(Request $request)
    {
        $restfulJson = new RestfulJsonResponse();

        $changePasswordType = new ChangePasswordType();

        $changePasswordType->setOldPassword($request->get("oldPassword"));
        $changePasswordType->setNewPassword($request->get("newPassword"));

        $restfulJson->addErrors($this->validateEntity($changePasswordType));

        if(!$restfulJson->hasErrors())
        {
            $user =  $this->getUser();
            $encoder_service = $this->get('security.password_encoder');

            if(!$encoder_service->isPasswordValid($user,$changePasswordType->getOldPassword()))
            {
                $restfulJson->addKeyError("oldPassword","Invalid Password");
            }
            else
            {
                $new_password = $encoder_service->encodePassword($user,$changePasswordType->getNewPassword());
                $user->setPassword($new_password);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $restfulJson->setMessage("Password Changed");
                return $restfulJson;
            }
        }

        $restfulJson->setMessage("Couldn't change password");
        $restfulJson->setStatusCode(400);
        return $restfulJson;
    }
}