<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/18/17
 * Time: 10:16 PM
 */

namespace DashboardBundle\Controller;

use CoreBundle\Controller\BaseController;
use CoreBundle\Entity\User;
use CoreBundle\Helper\RestfulError;
use CoreBundle\Helper\RestfulHelper;
use DashboardBundle\Validation\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class UserProfileController extends BaseController
{
    /**
     * @Route("/dashboard/profile/settings/profile", name="dashboard_user_settings_profile")
     */
    public  function  profileAction(Request $request)
    {
        return $this->render('dashboard/profile/settings/profile.html.twig');
    }

    /**
     * @Route("/dashboard/ajax/profile/new-password", options = { "expose" = true }, name="dashboard_ajax_new_password", )
     * @Method({"POST"})
     */
    public  function  putAccountAction(Request $request)
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