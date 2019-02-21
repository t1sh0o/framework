<?php

namespace Illuminate\Tests\Integration\Database;

use Illuminate\Database\Eloquent\Model;

/**
 * @group integration
 */
class EloquentJoinTest extends DatabaseTestCase
{
    /** @test */
    public function testJoinOnHasMany()
    {
        $query = User::join('posts');

        $this->assertEquals(
            'select * from "users" inner join "posts" on "users"."id" = "posts"."user_id"',
            $query->toSql()
        );
    }

    /** @test */
    public function testJoinOnHasOne()
    {
        $query = User::join('topPost');

        $this->assertEquals(
            'select * from "users" inner join "posts" on "users"."id" = "posts"."user_id"',
            $query->toSql()
        );
    }

    /** @test */
    public function testJoinOnBelongsTo()
    {
        $query = Post::join('author');

        $this->assertEquals(
            'select * from "posts" inner join "users" on "posts"."user_id" = "users"."id"',
            $query->toSql()
        );
    }

    /** @test */
    public function testJoinWorksAsBeforeWhenMoreArgsPassed()
    {
        $query = User::join('posts', 'users.name', '=', 'posts.username');

        $this->assertEquals(
            'select * from "users" inner join "posts" on "users"."name" = "posts"."username"',
            $query->toSql()
        );
    }

    /** @test */
    public function testJoinThrowsExceptionWhenNoSuchRelationAndOnlyOneArgPassed()
    {
        try {
            User::join('authoredPosts');
            $this->fail('ArgumentCountError should have been thrown');
        } catch (\ArgumentCountError $e) {
            $this->assertContains('join', $e->getMessage());
        }
    }
}

class User extends Model
{
    protected $table = 'users';

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function topPost()
    {
        return $this->hasOne(Post::class);
    }
}

class Post extends Model
{
    protected $table = 'posts';

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
