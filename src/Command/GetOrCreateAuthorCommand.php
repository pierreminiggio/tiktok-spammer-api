<?php

namespace App\Command;

use App\Entity\Author;
use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class GetOrCreateAuthorCommand
{
    public function __construct(private DatabaseFetcher $fetcher)
    {
    }

    public function execute(string $name): Author
    {
        $queryArgs = ['name' => $name];

        $selectQueryArgs = [
            $this->fetcher->createQuery(
                'author'
            )->select(
                'id',
                'name'
            )->where(
                'name = :name'
            ),
            $queryArgs
        ];

        $queriedAuthors = $this->fetcher->query(...$selectQueryArgs);

        if (! $queriedAuthors) {
            $this->fetcher->exec(
                $this->fetcher->createQuery(
                   'author'
                )->insertInto(
                    'name',
                    ':name'
                ),
                $queryArgs
            );
        }

        $queriedAuthors = $this->fetcher->query(...$selectQueryArgs);
        $queriedAuthor = $queriedAuthors[0];

        return new Author(
            (int) $queriedAuthor['id'],
            $queriedAuthor['name']
        );
    }
}