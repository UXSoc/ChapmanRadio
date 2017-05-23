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
use DashboardBundle\Form\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Validator\Validation;

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
     * @Route("/dashboard/profile/settings/account", name="dashboard_user_settings_account")
     */
    public  function  accountAction(Request $request)
    {
        /** @var $form Form*/
        $changePasswordForm = $this->createForm(ChangePasswordType::class);



        $changePasswordForm->handleRequest($request);
        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {
            $data = $changePasswordForm->getData();
            /** @var User $user */
            $user =  $this->getUser();

            /** @var UserPasswordEncoder  $encoder_service */
            $encoder_service = $this->get('security.password_encoder');
            if(!$encoder_service->isPasswordValid($user,$data["oldPassword"]))
            {
                $changePasswordForm->get("oldPassword")->addError(new FormError("Invalid Password"));
            }
            else
            {
                $this->addFlash('success','Your password has changed');

                $new_password = $encoder_service->encodePassword($user,$data["newPassword"]);
                $user->setPassword($new_password);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
        }
        return new JsonResponse($changePasswordForm->getData());

    }

}