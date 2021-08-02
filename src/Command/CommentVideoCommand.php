<?php

namespace App\Command;

use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class CommentVideoCommand
{
    public function __construct(private DatabaseFetcher $fetcher)
    {
    }

    public function execute(string $link, string $comment): void
    {
        $this->fetcher->exec(
            $this->fetcher->createQuery(
                'video'
            )->update(
                'comment = :comment'
            )->where(
                'link = :link'
            ),
            [
                'comment' => $comment,
                'link' => $link
            ]
        );
    }
}