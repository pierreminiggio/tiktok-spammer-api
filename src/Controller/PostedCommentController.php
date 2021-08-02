<?php

namespace App\Controller;

use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class PostedCommentController
{
    public function __construct(private DatabaseFetcher $fetcher)
    {
    }

    public function __invoke(): void
    {
        $fetchedCommentedVideos = $this->fetcher->query(
            $this->fetcher->createQuery(
                'video'
            )->select(
                'link',
                'comment',
                'created_at'
            )->where(
                'comment IS NOT NULL'
            )
        );

        $commentedVideos = '';

        foreach ($fetchedCommentedVideos as $fetchedCommentedVideo) {
            $link = $fetchedCommentedVideo['link'];
            $comment = $fetchedCommentedVideo['comment'];

            $commentedVideos .= <<<HTML
                <li><a href="$link" target="_blank">$link</a> : $comment</li>
            HTML;

        }

        echo <<<HTML
            <h1>Le bon spamming</h1>
            <ul>
                $commentedVideos
            </ul>
        HTML;
    }
}