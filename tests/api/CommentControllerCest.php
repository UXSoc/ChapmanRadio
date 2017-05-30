<?php
use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Role;
use CoreBundle\Entity\User;
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
        $this->user = $I->factory()->create(User::class);

    }
    public function _after(ApiTester $I)
    {
    }

    public function tryUpdatePostNotLoggedIn(ApiTester $I,UserStep $auth)
    {
        /** @var Comment $comment */
        $comment = $I->factory()->create(Comment::class,[
            "user" => $I->factory()->create(User::class)
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
            "user" => $I->factory()->create(User::class)
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
            "user" => $I->factory()->create(User::class)
        ]);
        /** @var User $staff */
        $staff = $I->factory()->create(User::class);
        $staff->addRole(new Role(Role::ROLE_STAFF));
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