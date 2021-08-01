<?php

namespace App\Command;

use App\Entity\Video;
use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class GetOrCreateVideoCommand
{
    public function __construct(private DatabaseFetcher $fetcher)
    {
    }

    public function execute(
        string $link,
        string $tikTokId,
        int $authorId,
        string $caption,
        int $likeCount,
        int $commentCount,
        int $shareCount
    ): Video
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
                'tiktok_id = :tiktok_id AND author_id = :author_id'
            ),
            [
                'tiktok_id' => $tikTokId,
                'author_id' => $authorId
            ]
        ];

        $queriedVideos = $this->fetcher->query(...$selectQueryArgs);

        if (! $queriedVideos) {
            $this->fetcher->exec(
                $this->fetcher->createQuery(
                   'video'
                )->insertInto(
                    'link, author_id, tiktok_id, caption, like_count, comment_count, share_count',
                    ':link, :author_id, :tiktok_id, :caption, :like_count, :comment_count, :share_count'
                ),
                [
                    'link' => $link,
                    'author_id' => $authorId,
                    'tiktok_id' => $tikTokId,
                    'caption' => $caption,
                    'like_count' => $likeCount,
                    'comment_count' => $commentCount,
                    'share_count' => $shareCount
                ]
            );
        }

        $queriedVideos = $this->fetcher->query(...$selectQueryArgs);
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