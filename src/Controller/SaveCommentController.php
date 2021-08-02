<?php

namespace App\Controller;

use App\Command\CommentVideoCommand;
use App\Query\VideoFromLinkQuery;
use Exception;

class SaveCommentController
{

    public function __construct(
        private VideoFromLinkQuery $videoQuery,
        private CommentVideoCommand $commentVideoCommand
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

        $comment = $jsonBody['comment'] ?? null;

        if (! $comment) {
            http_response_code(400);
            exit;
        }

        try {
            $this->videoQuery->execute($link);
        } catch (Exception) {
            http_response_code(404);

            return;
        }

        $this->commentVideoCommand->execute($link, $comment);

        http_response_code(204);
    }
}