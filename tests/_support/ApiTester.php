<?php
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;


    public function isRestfulSuccessResponse()
    {
        $this->seeResponseContainsJson(array(
            "success" => true
        ));
    }

    public  function isRestfulFailedResponse()
    {
        $this->seeResponseContainsJson(array(
            "success" => false
        ));
    }


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

}
