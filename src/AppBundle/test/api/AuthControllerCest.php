<?php
namespace AppBundle\test\api;
use AppBundle\ApiTester;
use Codeception\Util\HttpCode;
use CoreBundle\Entity\User;
use CoreBundle\Repository\UserRepository;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Tests\Common\DataFixtures\TestFixtures\UserFixture;
use League\FactoryMuffin\FactoryMuffin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerCest
{
    /** @var  User */
    private $user;

    public function _before(ApiTester $I)
    {
        /** @var User $user */
        $this->user  =  $I->factory()->create(User::class);
    }
    public function _after(ApiTester $I)
    {
    }

    // tests
    public function register(ApiTester $I)
    {

        /** @var User $user */
        $user = $I->factory()->instance(User::class);

        $I->sendPOST('/api/v3/auth/register',[
            "name" => $user->getName(),
            "username" => $user->getUsername(),
            "email" => $user->getEmail(),
            "password" => "password",
            "studentId" => $user->getStudentId()
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContainsJson(array(
            "success" => true,
            "data" => [
            "username" => $user->getUsername(),
            'roles' => ["ROLE_USER"]]
        ));
        $token = $I->grabDataFromResponseByJsonPath('$.data.token');
        /** @var User $user */
        $user = $I->grabEntityFromRepository('CoreBundle:User',array('token'=> $token));

        $I->assertEquals($user->getName(),  $user->getName());
        $I->assertEquals($user->getEmail() , $user->getEmail());
        $I->assertEquals($user->getStudentId(),$user->getStudentId());
        $I->assertEquals($user->getUsername() ,$user->getUsername());

        //TODO: can't test verification
    }

    /**
     * @param ApiTester $I
     */
    public function login(ApiTester $I)
    {
        $I->login($this->user->getUsername(),"password");
        $I->seeResponseContainsJson(array(
            "success" => true,
            "data" => [
                "username" => $this->user->getUsername(),
                'roles' => ["ROLE_USER"]]
        ));
    }

    public function failLogin(ApiTester $I)
    {
        $I->login($this->user->getUsername(),"wrongpassword");
        $I->seeResponseContainsJson(array(
            "success" => false
        ));
    }

    /**
     * @param ApiTester $I
     * @before login
     */
    public function status(ApiTester $I)
    {
        $I->sendGET('/api/v3/auth/status');
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContainsJson(array(
            "success" => true,
            "data" => [
                "username" => $this->user->getUsername(),
                'roles' => ["ROLE_USER"]]
        ));
    }


}
