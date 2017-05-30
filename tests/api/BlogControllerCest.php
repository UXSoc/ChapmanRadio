<?php
use Codeception\Util\HttpCode;
use CoreBundle\Entity\Post;
use CoreBundle\Entity\User;

class BlogControllerCest
{
    private $posts = array();

    public function _before(ApiTester $I)
    {
        $users = $I->factory()->seed(10,User::class);

        $this->posts = $I->factory()->seed(20,Post::class,[
            'author' => function($object,$save) use ($users){
                return $users[array_rand($users,1)];
            }
        ]);

    }
    public function _after(ApiTester $I)
    {

    }

    /**
     * @param ApiTester $I
     */
    public function tryGetPosts(ApiTester $I)
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
                    'is_pinned' => 'boolean'
                ]
            ]
        ],'$.data');
    }

    public function tryGetPost(ApiTester $I)
    {
        /** @var Post $post */
        $post = $this->posts[array_rand($this->posts,1)];

        $I->sendGET('/api/v3/post/'.$post->getToken().'/'.$post->getSlug());
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->isRestfulSuccessResponse();
        $I->seeResponseMatchesJsonType([
            "data" => [
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
        ]);
    }

    public function tryToGetPostWithInvalidSlug(ApiTester $I)
    {
        /** @var Post $post */
        $post = $this->posts[array_rand($this->posts,1)];

        $I->sendGET('/api/v3/post/'.$post->getToken().'/'."wrongslug");
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::GONE);
        $I->isRestfulFailedResponse();

    }

    public function tryToGetPostWithInvalidToken(ApiTester $I)
    {
        /** @var Post $post */
        $post = $this->posts[array_rand($this->posts,1)];

        $I->sendGET('/api/v3/post/apples/'.$post->getSlug());
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::GONE);
        $I->isRestfulFailedResponse();

    }

}