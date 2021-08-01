<?php

namespace App\Entity;

class Comment
{
    public function __construct(
        public int $id,
        public int $videoId,
        public int $authorId,
        public string $content
    )
    {
    }
}
