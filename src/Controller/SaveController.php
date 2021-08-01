<?php

namespace App\Controller;

use App\Command\GetOrCreateAuthorCommand;
use App\Command\GetOrCreateCommentCommand;
use App\Command\GetOrCreateVideoCommand;

class SaveController
{

    public function __construct(
        private GetOrCreateAuthorCommand $authorCommand,
        private GetOrCreateVideoCommand $videoCommand,
        private GetOrCreateCommentCommand $commentCommand
    )
    {
    }

    public function __invoke(?string $body): void
    {
        if (! $body) {
            http_response_code(400);
            exit;
        }

        $jsonBody = json_decode($body, true);

        if (! $jsonBody) {
            http_response_code(400);
            exit;
        }

        $link = $jsonBody['link'] ?? null;

        if (! $link) {
            http_response_code(400);
            exit;
        }

        $authorName = $jsonBody['author'] ?? null;

        if (! $authorName) {
            http_response_code(400);
            exit;
        }

        set_time_limit(0);

        $author = $this->authorCommand->execute($authorName);

        $tikTokId = substr($link, strlen('https://www.tiktok.com/@' . $authorName . '/video/'));

        $caption = $jsonBody['caption'] ?? null;

        if (! $caption) {
            http_response_code(400);
            exit;
        }

        $infos = $jsonBody['infos'] ?? null;

        $likeCount = $infos && ! empty($infos['likeCount']) ? (int) $infos['likeCount'] : 0;
        $commentCount = $infos && ! empty($infos['commentCount']) ? (int) $infos['commentCount'] : 0;
        $shareCount = $infos && ! empty($infos['shareCount']) ? (int) $infos['shareCount'] : 0;

        $video = $this->videoCommand->execute(
            $link,
            $tikTokId,
            $author->id,
            $caption,
            $likeCount,
            $commentCount,
            $shareCount
        );

        $comments = $jsonBody['comments'] ?? [];

        foreach ($comments as $comment) {
            $commentAuthorName = $comment['author'] ?? null;

            if ($commentAuthorName === null) {
                continue;
            }

            $commentContent = $comment['content'] ?? null;

            if ($commentContent === null) {
                continue;
            }

            $commentAuthor = $this->authorCommand->execute($commentAuthorName);
            $this->commentCommand->execute($video->id, $commentAuthor->id, $commentContent);
        }

        http_response_code(204);
    }
}