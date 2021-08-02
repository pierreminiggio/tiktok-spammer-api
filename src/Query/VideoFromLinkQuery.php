<?php

namespace App\Query;

use App\Entity\Video;
use Exception;
use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class VideoFromLinkQuery
{
    public function __construct(private DatabaseFetcher $fetcher)
    {
    }

    public function execute(string $link): Video
    {

        $selectQueryArgs = [
            $this->fetcher->createQuery(
                'video'
            )->select(
                'id',
                'link',
                'author_id',
                'tiktok_id',
                'caption',
                'like_count',
                'comment_count',
                'share_count',
                'expected_comment_locale',
                'comment'
            )->where(
                'link = :link'
            ),
            [
                'link' => $link
            ]
        ];

        $queriedVideos = $this->fetcher->query(...$selectQueryArgs);

        if (! $queriedVideos) {
            throw new Exception('Not found');
        }

        $queriedVideo = $queriedVideos[0];

        return new Video(
            (int) $queriedVideo['id'],
            $queriedVideo['link'],
            (int) $queriedVideo['author_id'],
            $queriedVideo['tiktok_id'],
            $queriedVideo['caption'],
            (int) $queriedVideo['like_count'],
            (int) $queriedVideo['comment_count'],
            (int) $queriedVideo['share_count'],
            $queriedVideo['expected_comment_locale'] ?? null,
            $queriedVideo['comment'] ?? null
        );
    }
}