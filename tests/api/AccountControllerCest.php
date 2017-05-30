<?php
use Codeception\Util\HttpCode;
use CoreBundle\Entity\User;
use Helper\Step\UserStep;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/28/17
 * Time: 11:38 PM
 */
class AccountControllerCest
{
    /** @var  User */
    private $user;

    public function _before(ApiTester $I)
    {
        /** @var User $user */
        $this->user = $I->factory()->create(User::class);
    }
    public function _after(ApiTester $I)
    {
    }

    public function tryChangePassword(ApiTester $I, UserStep $auth)
    {
        $auth->loginUser($this->user->getEmail(),"password");
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->sendPOST('/api/v3/account/new-password',[
            "oldPassword" => "password",
            "newPassword" => "password2"
        ]);
        $I->isRestfulSuccessResponse();
        $I->seeResponseCodeIs(HttpCode::OK);
        $auth->loginUser($this->user->getEmail(),"password2");
        $I->seeResponseCodeIs(HttpCode::OK);

    }


}