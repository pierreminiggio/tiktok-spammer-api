<?php

namespace App\Command;

use App\Entity\Comment;
use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class GetOrCreateCommentCommand
{
    public function __construct(private DatabaseFetcher $fetcher)
    {
    }

    public function execute(
        int $videoId,
        int $authorId,
        string $content
    ): Comment
    {
        $queryArgs = [
            'video_id' => $videoId,
            'author_id' => $authorId,
            'content' => $content
        ];

        $selectQueryArgs = [
            $this->fetcher->createQuery(
                'comment'
            )->select(
                'id',
                'video_id',
                'author_id',
                'content'
            )->where(
                'video_id = :video_id AND author_id = :author_id AND content = :content'
            ),
            $queryArgs
        ];

        $queriedComments = $this->fetcher->query(...$selectQueryArgs);

        if (! $queriedComments) {
            $this->fetcher->exec(
                $this->fetcher->createQuery(
                   'comment'
                )->insertInto(
                    'video_id, author_id, content',
                    ':video_id, :author_id, :content'
                ),
                $queryArgs
            );
        }

        $queriedComments = $this->fetcher->query(...$selectQueryArgs);
        $queriedComment = $queriedComments[0];

        return new Comment(
            (int) $queriedComment['id'],
            $queriedComment['video_id'],
            $queriedComment['author_id'],
            $queriedComment['content']
        );
    }
}