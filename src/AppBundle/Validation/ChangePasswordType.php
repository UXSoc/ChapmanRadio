<?php
namespace AppBundle\Validation;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/22/17
 * Time: 8:26 PM
 */
class ChangePasswordType
{
    /**
     * @Assert\NotBlank()
     */
    private $oldPassword;

    /**
     * @Assert\NotBlank()
     */
    private $newPassword;

    /**
     * @Assert\NotBlank()
     */
    private $repeatPassword;

    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    public function getNewPassword()
    {
        return $this->newPassword;
    }

    public function getRepeatPassword()
    {
        return $this->repeatPassword;
    }


    public function setOldPassword($value)
    {
        $this->oldPassword = $value;
    }

    public function setNewPassword($value)
    {
        $this->newPassword = $value;
    }

    public function setRepeatPassword($value)
    {
        $this->repeatPassword = $value;
    }




}