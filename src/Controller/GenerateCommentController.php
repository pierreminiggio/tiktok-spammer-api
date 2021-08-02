<?php

namespace App\Controller;

use App\Query\RandomCommentQuery;
use App\Query\VideoFromLinkQuery;
use App\Service\TikTokLangChecker;
use Exception;

class GenerateCommentController
{

    public function __construct(
        private VideoFromLinkQuery $videoQuery,
        private TikTokLangChecker $langChecker,
        private RandomCommentQuery $randomCommentQuery
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

        try {
            $video = $this->videoQuery->execute($link);
        } catch (Exception) {
            http_response_code(404);

            return;
        }

        if ($video->commentCount === 0) {
            $firstComments = ['First !', 'Preum\'s !', 'First gg izi'];
            echo $firstComments[array_rand($firstComments)];

            return;
        }

        $locale = $this->langChecker->check($video);

        try {
            $randomComment = $this->randomCommentQuery->execute($locale);
        } catch (Exception $e) {
            var_dump($e);
            http_response_code(500);

            return;
        }

        echo $randomComment;
        http_response_code(200);
    }
}