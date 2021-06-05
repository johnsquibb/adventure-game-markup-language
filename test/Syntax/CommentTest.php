<?php

namespace AdventureGameMarkupLanguage\Syntax;

use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testToString()
    {
        $tokens = ['This', ' ', 'is', ' ', 'a', ' ', 'comment'];
        $comment = new Comment($tokens);

        $this->assertEquals('#This is a comment', $comment->toString());
    }
}
