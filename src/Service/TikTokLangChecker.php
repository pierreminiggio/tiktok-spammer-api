<?php

namespace App\Service;

use App\Entity\Video;
use App\Enum\LangEnum;
use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class TikTokLangChecker
{

    public function __construct(private DatabaseFetcher $fetcher)
    {
    }

    /**
     * @see LangEnum
     */
    public function check(Video $video): string
    {

        if ($this->isFrench($video->caption)) {
            return LangEnum::FR;
        }

        $fetchedComments = $this->fetcher->query(
            $this->fetcher->createQuery(
                'comment'
            )->select(
                'content',
            )->where(
                'video_id = :video_id'
            ),
            ['video_id' => $video->id]
        );

        $commentsLang = $this->getLangFromComments($fetchedComments);
        if ($commentsLang !== null) {
            return $commentsLang;
        }

        return LangEnum::EN;
    }

    protected function isFrench(string $input): bool
    {
        $frenchStuffs = [
            'mdr',
            'un pote',
            'pourtoi',
            'c\'est',
            'c’est',
            'c est',
            ' le ',
            ' la ',
            ' les ',
            ' mon ',
            ' ma ',
            ' mes ',
            'ptn',
            'quand',
            'oui',
            'mais',
            'déjà',
            'argent',
            ' un ',
            ' une ',
            ' et ',
            ' sa ',
            ' à ',
            'désolé',
            'Bah',
            ' que ',
            'qu\'on',
            'qu’on',
            ' son ',
            ' sa ',
            ' ses ',
            'jvais',
            'dcp',
            'sinon',
            'médocs',
            't\'inquiete',
            't’inquiete',
            'tinquiete',
            'tkt',
            'tekate',
            'j\'ai',
            'j’ai',
            ' dirait ',
            'trop',
            'ça',
            ' ho ',
            'bg',
            'j\'y',
            'j’y',
            'je ',
            'nous ',
            'vous',
            ' dans ',
            'cigarette',
            'french'
        ];

        foreach ($frenchStuffs as $frenchStuff) {
            if (str_contains(strtolower($input), $frenchStuff)) {
                return true;
            }
        }

        return false;
    }

    protected function getLangFromComments(array $comments): ?string
    {
        $threshold = count($comments) / 2;
        $frenchCommentCount = 0;

        foreach ($comments as $comment) {
            $content = $comment['content'];
            $isCommentFrench = $this->isFrench($content);

            if ($isCommentFrench) {
                $frenchCommentCount++;
            }

            if ($frenchCommentCount >= $threshold) {
                return LangEnum::FR;
            }
        }
        return null;
    }
}
