<?php
namespace Helper\Step;

use ApiTester;
use CoreBundle\Entity\Dj;
use CoreBundle\Entity\Staff;
use CoreBundle\Entity\User;

class UserStep extends ApiTester
{

    public function loginUser($username,$password)
    {
        $I = $this;
        $I->sendPOST('/login',[
            "_username" => $username,
            "_password" => $password,
            "_remember_me" => false
        ]);
        $I->seeResponseIsJson();
    }

    /**
     * @return User
     */
    public  function createDJ()
    {
        $I = $this;
        $dj = $I->factory()->create(Dj::class);
        $user = $I->factory()->create(User::class,[
            'dj' => $dj
        ]);
        return $user;

    }

    /**
     * @return User
     */
    public  function createStaff()
    {
        $I = $this;
        $staff = $I->factory()->create(Staff::class);
        $user = $I->factory()->create(User::class,[
            'staff' => $staff
        ]);
        return $user;
    }

    /**
     * @return User
     */
    public function createStaffDj()
    {
        $I = $this;
        $dj = $I->factory()->create(Dj::class);
        $staff = $I->factory()->create(Staff::class);
        $user = $I->factory()->create(User::class,[
            'staff' => $staff,
            'dj' => $dj
        ]);
        return  $user;
    }

    /**
     * @return User
     */
    public function createUser()
    {
        $I = $this;
        return $I->factory()->create(User::class);

    }

}