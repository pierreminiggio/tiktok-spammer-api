<?php

namespace App\Query;

use App\Enum\LangEnum;
use Exception;
use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class RandomCommentQuery
{
    public function __construct(private DatabaseFetcher $fetcher)
    {
    }

    /**
     * @see LangEnum
     */
    public function execute(string $locale): string
    {
        $query = $this->fetcher->createQuery(
            'random_comment'
        )->select(
            'content'
        )->where(
            'locales like \'%[' . $locale . ']%\''
        )->build();

        $query .= ' ORDER BY RAND() LIMIT 1';

        $fetchedComments = $this->fetcher->rawQuery($query);

        if (! $fetchedComments) {
            throw new Exception(':\'(');
        }

        return $fetchedComments[0]['content'];
    }
}