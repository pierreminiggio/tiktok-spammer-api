<?php

namespace App\Controller;

use DateTimeImmutable;
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

        /** @var Array<string, bool> $commentedDays */
        $commentedDays = [];

        foreach ($fetchedCommentedVideos as $fetchedCommentedVideo) {
            $commentedAt = new DateTimeImmutable($fetchedCommentedVideo['created_at']);
            $link = $fetchedCommentedVideo['link'];
            $comment = $fetchedCommentedVideo['comment'];

            $commentedVideos .= <<<HTML
                <li>Commenté le {$commentedAt->format('d/m/Y à H:i')} <a href="$link" target="_blank">$link</a> : $comment</li>
            HTML;

            $dateKey = $commentedAt->format('Ymd');
            if (! isset($commentedDays[$dateKey])) {
                $commentedDays[$dateKey] = true;
            }
        }

        $numberOfComments = count($fetchedCommentedVideos);
        $averageCommentingPerDay = $numberOfComments / count($commentedDays);

        echo <<<HTML
            <h1>Le bon spamming</h1>
            <p>Le bot a déjà posté $numberOfComments commentaires, avec une moyenne de $averageCommentingPerDay par jour.</p>
            <ul>
                $commentedVideos
            </ul>
        HTML;
    }
}