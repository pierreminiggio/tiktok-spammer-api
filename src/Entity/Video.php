<?php

namespace App\Entity;

class Video
{
    public function __construct(
        public int $id,
        public string $link,
        public int $authorId,
        public string $tikTokId,
        public string $caption,
        public int $likeCount,
        public int $commentCount,
        public int $shareCount,
        public ?string $expectedCommentLocale,
        public ?string $comment
    )
    {
    }
}
