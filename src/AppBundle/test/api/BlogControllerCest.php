<?php
use AppBundle\ApiTester;
use Codeception\Util\HttpCode;
use CoreBundle\Entity\Post;
use CoreBundle\Entity\User;

class BlogControllerCest
{
    public function _before(ApiTester $I)
    {
        $users = $I->factory()->seed(20,User::class);
        /** @var User $u */
        foreach ($users as $u )
        {
            $post = $I->factory()->instance(Post::class,[
                'author' => $u
            ]);
            $I->persistEntity($post);
        }
    }
    public function _after(ApiTester $I)
    {

    }

    /**
     * @param ApiTester $I
     */
    public function getBlogPosts(ApiTester $I)
    {
        $I->sendGET('/api/v3/post');
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->isRestfulSuccessResponse();
        $I->seeResponseMatchesJsonType([
            'count' => 'integer',
            'perPage' => 'integer',
            'pages' => 'integer',
            "result" => [
                [
                    'token' => 'string',
                    'slug' => 'string',
                    'name' => 'string',
                    'created_at'=> 'array',
                    'updated_at' => 'array',
                    'excerpt' => 'string',
                    'categories' => 'null|array',
                    'tags' => 'null|array',
                    'is_pinned' => 'integer'
                ]
            ]
        ],'$.data');
    }

}