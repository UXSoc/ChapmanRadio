<?php

namespace RestfulBundle\Validation;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordType
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
