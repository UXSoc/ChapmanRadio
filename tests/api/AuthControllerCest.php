<?php

use Codeception\Util\HttpCode;
use CoreBundle\Entity\User;

class AuthControllerCest
{
    /** @var User */
    private $user;

    public function _before(ApiTester $I)
    {
        /* @var User $user */
        $this->user = $I->factory()->create(User::class);
    }

    public function _after(ApiTester $I)
    {
    }

    // tests
    public function tryRegister(ApiTester $I)
    {

        /** @var User $user */
        $user = $I->factory()->instance(User::class);

        $I->sendPOST('/api/v3/auth/register', [
            'name'      => $user->getName(),
            'username'  => $user->getUsername(),
            'email'     => $user->getEmail(),
            'password'  => 'password',
            'studentId' => $user->getStudentId(),
        ]);
        $I->seeResponseIsJson();
        $I->isRestfulSuccessResponse();
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContainsJson([
            'data' => [
            'username' => $user->getUsername(),
            'roles'    => ['ROLE_USER'], ],
        ]);
        $token = $I->grabDataFromResponseByJsonPath('$.data.token');
        /** @var User $user */
        $user = $I->grabEntityFromRepository('CoreBundle:User', ['token'=> $token]);

        $I->assertEquals($user->getName(), $user->getName());
        $I->assertEquals($user->getEmail(), $user->getEmail());
        $I->assertEquals($user->getStudentId(), $user->getStudentId());
        $I->assertEquals($user->getUsername(), $user->getUsername());

        //TODO: can't test verification
    }

    /**
     * @param ApiTester $I
     */
    public function tryLoginWithUsername(ApiTester $I)
    {
        $I->loginUser($this->user->getUsername(), 'password');
        $I->seeResponseContainsJson([
            'data' => [
                'username' => $this->user->getUsername(),
                'roles'    => ['ROLE_USER'], ],
        ]);
        $I->isRestfulSuccessResponse();
    }

    public function tryLoginWithEmail(ApiTester $I)
    {
        $I->loginUser($this->user->getEmail(), 'password');
        $I->seeResponseContainsJson([
            'data' => [
                'username' => $this->user->getUsername(),
                'roles'    => ['ROLE_USER'], ],
        ]);
        $I->isRestfulSuccessResponse();
    }

    public function failLogin(ApiTester $I)
    {
        $I->loginUser($this->user->getUsername(), 'wrongpassword');
        $I->isRestfulFailedResponse();
    }

    /**
     * @param ApiTester $I
     */
    public function TryGetStatus(ApiTester $I)
    {
        $I->loginUser($this->user->getUsername(), 'password');

        $I->sendGET('/api/v3/user/me');
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->isRestfulSuccessResponse();
        $I->seeResponseContainsJson([
            'success' => true,
            'data'    => [
                'username' => $this->user->getUsername(),
                'roles'    => ['ROLE_USER'], ],
        ]);
    }
}
