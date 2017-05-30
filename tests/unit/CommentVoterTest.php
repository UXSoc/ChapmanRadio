<?php


use CoreBundle\Entity\Comment;
use CoreBundle\Entity\User;
use CoreBundle\Security\CommentVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoterTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testWithOwningUser()
    {

        $user = \Mockery::mock(User::class)->makePartial();
        $comment = \Mockery::mock(Comment::class)->makePartial();
        $comment->shouldReceive("getUser")->andReturn($user);


        $token = \Mockery::mock(TokenInterface::class);
        $token->shouldReceive("getUser")->andReturn($user);


        $accessDecisionManager = \Mockery::mock(AccessDecisionManagerInterface::class)->makePartial();
        $accessDecisionManager->shouldReceive('decide')->withArgs([$token,array('ROLE_STAFF')]);

        $commentVoter = new CommentVoter($accessDecisionManager);

        $this->tester->assertEquals( Voter::ACCESS_GRANTED,$commentVoter->vote($token,$comment,['edit']));
    }

    // tests
    public function testWithDiffrentUser()
    {

        $user = \Mockery::mock(User::class)->makePartial();
        $user2 = \Mockery::mock(User::class)->makePartial();

        $comment = \Mockery::mock(Comment::class)->makePartial();
        $comment->shouldReceive("getUser")->andReturn($user);


        $token = \Mockery::mock(TokenInterface::class);
        $token->shouldReceive("getUser")->andReturn($user2);


        $accessDecisionManager = \Mockery::mock(AccessDecisionManagerInterface::class)->makePartial();
        $accessDecisionManager->shouldReceive('decide')->withArgs([$token,array('ROLE_STAFF')]);

        $commentVoter = new CommentVoter($accessDecisionManager);

        $this->tester->assertEquals( Voter::ACCESS_GRANTED,$commentVoter->vote($token,$comment,['edit']));
    }

    // tests
    public function testWithStaffUser()
    {

        $user = \Mockery::mock(User::class)->makePartial();
        $user2 = \Mockery::mock(User::class)->makePartial();

        $comment = \Mockery::mock(Comment::class)->makePartial();
        $comment->shouldReceive("getUser")->andReturn($user);


        $token = \Mockery::mock(TokenInterface::class);
        $token->shouldReceive("getUser")->andReturn($user2);


        $accessDecisionManager = \Mockery::mock(AccessDecisionManagerInterface::class)->makePartial();
        $accessDecisionManager->shouldReceive('decide')->withArgs([$token,array('ROLE_STAFF')])->andReturn(true);

        $commentVoter = new CommentVoter($accessDecisionManager);

        $this->tester->assertEquals( Voter::ACCESS_GRANTED,$commentVoter->vote($token,$comment,['edit']));
    }
}