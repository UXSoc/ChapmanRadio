<?php

use Codeception\Util\HttpCode;
use CoreBundle\Entity\Post;
use CoreBundle\Entity\Show;
use CoreBundle\Entity\User;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/29/17
 * Time: 2:23 PM.
 */
class ShowControllerCest
{
    private $shows = [];

    public function _before(ApiTester $I)
    {
        /* @var Show $show */
        $this->shows = $I->factory()->seed(20, Show::class);
    }

    public function _after(ApiTester $I)
    {
    }

    /**
     * @param ApiTester $I
     */
    public function tryGetShows(ApiTester $I)
    {
        $I->sendGET('/api/v3/show');
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->isRestfulSuccessResponse();
        $I->seeResponseMatchesJsonType([
            'count'   => 'integer',
            'perPage' => 'integer',
            'pages'   => 'integer',
            'result'  => [
                [
                    'token'           => 'string',
                    'slug'            => 'string',
                    'name'            => 'string',
                    'description'     => 'string',
                    'created_at'      => 'array',
                    'updated_at'      => 'array',
                    'enable_comments' => 'boolean',
                ],
            ],
        ], '$.data');
    }

    public function tryGetShow(ApiTester $I)
    {
        /** @var Show $show */
        $show = $this->shows[array_rand($this->shows, 1)];

        $I->sendGET('/api/v3/show/'.$show->getToken().'/'.$show->getSlug());
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->isRestfulSuccessResponse();
        $I->seeResponseMatchesJsonType([
            'data' => [
                'token'           => 'string',
                'slug'            => 'string',
                'name'            => 'string',
                'description'     => 'string',
                'created_at'      => 'array',
                'updated_at'      => 'array',
                'enable_comments' => 'boolean',
            ],
        ]);
    }

    public function tryToGetShowWithInvalidSlug(ApiTester $I)
    {
        /** @var Show $show */
        $show = $this->shows[array_rand($this->shows, 1)];

        $I->sendGET('/api/v3/show/'.$show->getToken().'/'.'wrongslug');
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::GONE);
        $I->isRestfulFailedResponse();
    }

    public function tryToGetShowWithInvalidToken(ApiTester $I)
    {
        /** @var Show $show */
        $post = $this->shows[array_rand($this->shows, 1)];

        $I->sendGET('/api/v3/post/show/'.$post->getSlug());
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::GONE);
        $I->isRestfulFailedResponse();
    }
}
