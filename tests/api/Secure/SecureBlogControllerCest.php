<?php
use Codeception\Util\HttpCode;
use CoreBundle\Entity\Dj;
use CoreBundle\Entity\Post;
use CoreBundle\Entity\Role;
use CoreBundle\Entity\User;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/30/17
 * Time: 6:33 PM
 */
class SecureBlogControllerCest
{
    /** @var  User */
    private $user;

    public function _before(ApiTester $I)
    {
        /** @var User $user */
        $this->user = $I->factory()->create(User::class);
    }
    public function _after(ApiTester $I)
    {
    }

    public function tryAddBlogEntryAsNormalUser(ApiTester $I)
    {
        $faker = Faker\Factory::create();
        $slug = $faker->unique()->slug;
        $I->loginUser($this->user->getUsername(),'password');
        $I->sendPUT('/api/v3/private/post', [
            'pinned' => true,
            'content' =>$faker->paragraph(10),
            'slug' => $slug,
            'excerpt' => $faker->paragraph(1),
            'name' => $faker->unique()->name
            ]);
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->isRestfulFailedResponse();
        $I->assertEmpty($I->grabEntitiesFromRepository(Post::class,['slug'=> $slug]));
    }

    public  function tryAddBlogEntryAsDj(ApiTester $I)
    {
        $faker = Faker\Factory::create();

        $slug = $faker->unique()->slug;
        $dj = $I->factory()->create(Dj::class);
        $this->user->setDj($dj);
        $I->persistEntity($this->user);
        $I->loginUser($this->user->getUsername(),'password');
        $I->sendPUT('/api/v3/private/post', [
            'pinned' => true,
            'content' =>$faker->paragraph(10),
            'slug' => $slug,
            'excerpt' => $faker->paragraph(1),
            'name' => $faker->unique()->name
        ]);
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->isRestfulFailedResponse();
        $I->assertEmpty($I->grabEntitiesFromRepository(Post::class,['slug'=> $slug]));

    }

    public  function tryAddBlogEntryAsStaff(ApiTester $I)
    {
        $faker = Faker\Factory::create();

        $temp = [
            'pinned' => true,
            'content' =>$faker->paragraph(10),
            'slug' => $faker->unique()->slug,
            'excerpt' => $faker->paragraph(1),
            'name' => $faker->unique()->name
        ];

        $role = new Role(Role::ROLE_STAFF);
        $this->user->addRole($role);
        $I->persistEntity($this->user);
        $I->loginUser($this->user->getUsername(),'password');
        $I->sendPUT('/api/v3/private/post', $temp);
        $I->isRestfulSuccessResponse();
        /** @var Post $post */
        $post = $I->grabEntityFromRepository(Post::class,['slug'=> $temp['slug']]);

        $I->assertEquals($temp['pinned'],$post->getIsPinned());
        $I->assertEquals($temp['content'],$post->getContent());
        $I->assertEquals($temp['slug'],$post->getSlug());
        $I->assertEquals($temp['excerpt'],$post->getExcerpt());
        $I->assertEquals($temp['name'],$post->getName());
        $I->assertEquals($this->user->getId(),$post->getAuthor()->getId());
    }

    public function tryPatchBlogEntryAsNormalUser(ApiTester $I)
    {
        $faker = Faker\Factory::create();
        /** @var Post $post */
        $post = $I->factory()->create(Post::class,[
            'author' => $this->user]);

        $content = $faker->paragraph(10);
        $I->loginUser($this->user->getUsername(),'password');
        $I->sendPATCH('/api/v3/private/post/' . $post->getToken(). '/' . $post->getSlug(), [
            'content' => $content
        ]);
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->isRestfulFailedResponse();
        $post = $I->grabEntityFromRepository(Post::class,['slug'=> $post->getSlug()]);
        $I->assertNotEquals($content,$post->getContent());
    }

    public  function tryPatchBlogEntryAsOwningDj(ApiTester $I)
    {
        $faker = Faker\Factory::create();
        $post = $I->factory()->create(Post::class,[
            'author' => $this->user]);

        $content = $faker->paragraph(10);

        $dj = $I->factory()->create(Dj::class);
        $this->user->setDj($dj);
        $I->persistEntity($this->user);
        $I->loginUser($this->user->getUsername(),'password');
        $I->sendPATCH('/api/v3/private/post/' . $post->getToken(). '/' . $post->getSlug(), [
            'content' =>$content
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->isRestfulSuccessResponse();
        /** @var Post $post */
        $post = $I->grabEntityFromRepository(Post::class,['slug'=> $post->getSlug()]);
        $I->assertEquals($content,$post->getContent());
    }

    public  function tryPatchBlogEntryAsDj(ApiTester $I)
    {
        $faker = Faker\Factory::create();
        $post = $I->factory()->create(Post::class,[
            'author' =>$I->factory()->create(User::class)]);

        $content = $faker->paragraph(10);

        $dj = $I->factory()->create(Dj::class);
        $this->user->setDj($dj);
        $I->persistEntity($this->user);
        $I->loginUser($this->user->getUsername(),'password');
        $I->sendPATCH('/api/v3/private/post/' . $post->getToken(). '/' . $post->getSlug(), [
            'content' =>$content
        ]);
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->isRestfulFailedResponse();
        /** @var Post $post */
        $post = $I->grabEntityFromRepository(Post::class,['slug'=> $post->getSlug()]);
        $I->assertNotEquals($content,$post->getContent());
    }


    public  function tryPatchBlogEntryAsStaff(ApiTester $I)
    {

        $faker = Faker\Factory::create();
        $post = $I->factory()->create(Post::class,[
            'author' =>$I->factory()->create(User::class)]);

        $content = $faker->paragraph(10);

        $role = new Role(Role::ROLE_STAFF);
        $this->user->addRole($role);
        $I->persistEntity($this->user);
        $I->loginUser($this->user->getUsername(),'password');
        $I->sendPATCH('/api/v3/private/post/' . $post->getToken(). '/' . $post->getSlug(), [
            'content' =>$content
        ]);
        $I->isRestfulSuccessResponse();
        /** @var Post $post */
        $post = $I->grabEntityFromRepository(Post::class,['slug'=> $post->getSlug(),'token' => $post->getToken()]);

        $I->assertEquals($post->getContent(),$content);
    }


    public function tryUploadImage(ApiTester $I)
    {
        $post = $I->factory()->create(Post::class,[
            'author' =>$I->factory()->create(User::class)]);

        $role = new Role(Role::ROLE_STAFF);
        $this->user->addRole($role);
        $I->persistEntity($this->user);
        $I->loginUser($this->user->getUsername(),'password');

        $I->sendPUT('/api/v3/private/post/'. $post->getToken(). '/' . $post->getSlug() . '/image',[],[
            'image' => codecept_data_dir('concert.jpeg'),
        ]);
        $I->isRestfulSuccessResponse();
        $I->seeResponseCodeIs(HttpCode::OK);

        $I->sendPUT('/api/v3/private/post/'. $post->getToken(). '/' . $post->getSlug() . '/image',[],[
            'image' => codecept_data_dir('apple-laptop.jpg'),
        ]);
        $I->isRestfulSuccessResponse();
        $I->seeResponseCodeIs(HttpCode::OK);

        /** @var Post $post */
        $post = $I->grabEntityFromRepository(Post::class,['slug'=> $post->getSlug(),'token' => $post->getToken()]);

        /** @var \CoreBundle\Entity\Image $image */
        foreach ($post->getImages() as $image)
        {
            $I->sendGET('/image/' . $image->getToken());
            $I->seeResponseCodeIs(HttpCode::OK);

        }

    }

    public function tryGetImagesForPost(ApiTester $I)
    {
        $post = $I->factory()->create(Post::class,[
            'author' =>$I->factory()->create(User::class)]);

        $role = new Role(Role::ROLE_STAFF);
        $this->user->addRole($role);
        $I->persistEntity($this->user);
        $I->loginUser($this->user->getUsername(),'password');

        $I->sendPUT('/api/v3/private/post/'. $post->getToken(). '/' . $post->getSlug() . '/image',[],[
            'image' => codecept_data_dir('concert.jpeg'),
        ]);
        $I->isRestfulSuccessResponse();
        $I->seeResponseCodeIs(HttpCode::OK);

        $I->sendPUT('/api/v3/private/post/'. $post->getToken(). '/' . $post->getSlug() . '/image',[],[
            'image' => codecept_data_dir('attention.jpg'),
        ]);
        $I->isRestfulSuccessResponse();
        $I->seeResponseCodeIs(HttpCode::OK);

        $I->sendGET('/api/v3/private/post/'.$post->getToken() . '/' . $post->getSlug() . '/image');
        $I->isRestfulSuccessResponse();
        $I->seeResponseMatchesJsonType(array(
            "data" => [
                [
                    'token' => 'string',
                    'created_at' => 'array'
                ],
                [
                    'token' => 'string',
                    'created_at' => 'array'
                ]
            ]
        ));

    }



}