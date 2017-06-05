<?php
use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Role;
use CoreBundle\Entity\User;

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
    public function _before(ApiTester $I)
    {
        $this->user = $I->factory()->create(User::class);

    }
    public function _after(ApiTester $I)
    {
    }

    public function tryUpdatePostNotLoggedIn(ApiTester $I)
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

    public function tryUpdatePostAsWrongUser(ApiTester $I)
    {
        /** @var Comment $comment */
        $comment = $I->factory()->create(Comment::class,[
            "user" => $I->factory()->create(User::class)
        ]);
        $I->loginUser($this->user->getEmail(), "password");
        $I->sendPATCH('/api/v3/comment/' . $comment->getToken());
        $I->seeResponseIsJson();
        $I->isRestfulFailedResponse();
        $I->seeResponseContainsJson([
            "message" => "Comment Permission Error"
        ]);


    }

    public function tryUpdatePostAsStaff(ApiTester $I)
    {
        /** @var Comment $comment */
        $comment = $I->factory()->create(Comment::class,[
            "user" => $I->factory()->create(User::class)
        ]);
        /** @var User $staff */
        $staff = $I->factory()->create(User::class);
        $staff->addRole(new Role(Role::ROLE_STAFF));
        $I->persistEntity($staff);
        $I->loginUser($staff->getEmail(), "password");

        $I->sendPATCH('/api/v3/comment/' . $comment->getToken(),["content" => "here is the updated comment"]);
        $I->seeResponseIsJson();
        $I->isRestfulSuccessResponse();

//        $comment = $I->grabEntityFromRepository(Comment::class,array('token'=> $comment->getToken()));
//        $I->assertEquals($comment->getContent(),"here is the updated comment");

    }


    public function tryUpdatePostAsUser(ApiTester $I)
    {
        /** @var Comment $comment */
        $comment = $I->factory()->create(Comment::class,[
            "user" => $this->user
        ]);
        $I->loginUser($this->user->getEmail(), "password");

        $I->sendPATCH('/api/v3/comment/' . $comment->getToken(),["content" => "here is the updated comment"]);
        $I->seeResponseIsJson();
        $I->isRestfulSuccessResponse();

//        $c2 = $I->grabEntityFromRepository(Comment::class,array('token'=> $comment->getToken()));
//        $I->assertEquals("here is the updated comment",$c2->getContent());

    }
}