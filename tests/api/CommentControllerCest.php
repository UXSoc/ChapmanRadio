<?php
use CoreBundle\Entity\Comment;
use Helper\Step\UserStep;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/29/17
 * Time: 12:32 PM
 */
class CommentControllerCest
{
    /** @var  \CoreBundle\Entity\User */
    private  $user;
    public function _before(ApiTester $I,UserStep $auth)
    {
        $this->user = $auth->createUser();

    }
    public function _after(ApiTester $I)
    {
    }

    public function tryUpdatePostNotLoggedIn(ApiTester $I,UserStep $auth)
    {
        /** @var Comment $comment */
        $comment = $I->factory()->create(Comment::class,[
            "user" => $auth->createUser()
        ]);
        $I->sendPATCH('/api/v3/comment/' . $comment->getToken());
        $I->seeResponseIsJson();
        $I->isRestfulFailedResponse();
        $I->seeResponseContainsJson([
            "success" => false,
            "message" => "Permission Error"
        ]);


    }

    public function tryUpdatePostAsWrongUser(ApiTester $I,UserStep $auth)
    {
        /** @var Comment $comment */
        $comment = $I->factory()->create(Comment::class,[
            "user" => $auth->createUser()
        ]);
        $auth->loginUser($this->user->getEmail(), "password");
        $I->sendPATCH('/api/v3/comment/' . $comment->getToken());
        $I->seeResponseIsJson();
        $I->isRestfulFailedResponse();
        $I->seeResponseContainsJson([
            "message" => "Comment Permission Error"
        ]);


    }

    public function tryUpdatePostAsStaff(ApiTester $I, UserStep $auth)
    {
        /** @var Comment $comment */
        $comment = $I->factory()->create(Comment::class,[
            "user" => $auth->createUser()
        ]);
        $staff = $auth->createStaff();
        $auth->loginUser($staff->getEmail(), "password");

        $I->sendPATCH('/api/v3/comment/' . $comment->getToken(),["content" => "here is the updated comment"]);
        $I->seeResponseIsJson();
        $I->isRestfulSuccessResponse();

        $comment = $I->grabEntityFromRepository(Comment::class,array('token'=> $comment->getToken()));
        $I->assertEquals($comment->getContent(),"here is the updated comment");

    }


    public function tryUpdatePostAsUser(ApiTester $I,UserStep $auth)
    {
        /** @var Comment $comment */
        $comment = $I->factory()->create(Comment::class,[
            "user" => $this->user
        ]);
        $auth->loginUser($this->user->getEmail(), "password");

        $I->sendPATCH('/api/v3/comment/' . $comment->getToken(),["content" => "here is the updated comment"]);
        $I->seeResponseIsJson();
        $I->isRestfulSuccessResponse();

        $comment = $I->grabEntityFromRepository(Comment::class,array('token'=> $comment->getToken()));
        $I->assertEquals($comment->getContent(),"here is the updated comment");

    }
}