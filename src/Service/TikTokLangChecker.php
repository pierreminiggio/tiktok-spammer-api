<?php

namespace App\Service;

use App\Enum\LangEnum;
use Exception;
use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class TikTokLangChecker
{

    public function __construct(private DatabaseFetcher $fetcher)
    {
    }

    /**
     * @throws Exception
     */
    public function check(string $link): string
    {
        $fetchedVideos = $this->fetcher->query(
            $this->fetcher->createQuery(
                'video'
            )->select(
                'id',
                'caption'
            )->where(
                'link = :link'
            ),
            ['link' => $link]
        );

        if (! $fetchedVideos) {
            throw new Exception('Not found');
        }

        $fetchedVideo = $fetchedVideos[0];
        $caption = $fetchedVideo['caption'];

        if ($this->isFrench($caption)) {
            return LangEnum::FR;
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
            ' le ',
            ' la '
        ];

        foreach ($frenchStuffs as $frenchStuff) {
            if (str_contains($input, $frenchStuff)) {
                return true;
            }
        }

        return false;
    }
}
