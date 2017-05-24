<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace AppBundle\Validation;

use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordType
{
    /**
     * @Assert\NotBlank()
     */
    private $oldPassword;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $newPassword;


    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    public function getNewPassword()
    {
        return $this->newPassword;
    }

    public function setOldPassword($value)
    {
        $this->oldPassword = $value;
    }

    public function setNewPassword($value)
    {
        $this->newPassword = $value;
    }


}