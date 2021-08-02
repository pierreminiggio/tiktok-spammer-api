<?php

namespace Test\Service;

use App\Service\TikTokLangChecker;
use PHPUnit\Framework\TestCase;
use PierreMiniggio\ConfigProvider\ConfigProvider;
use PierreMiniggio\DatabaseConnection\DatabaseConnection;
use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class TikTokLangCheckerTest extends TestCase
{

    public function testLangs(): void
    {
        $configProvider = new ConfigProvider(
            __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
        );
        $config = $configProvider->get();
        $dbConfig = $config['db'];
        $fetcher = new DatabaseFetcher(new DatabaseConnection(
            $dbConfig['host'],
            $dbConfig['database'],
            $dbConfig['username'],
            $dbConfig['password'],
            DatabaseConnection::UTF8_MB4
        ));

        $fetchedVideos = $fetcher->query(
            $fetcher->createQuery(
                'video'
            )->select(
                'id',
                'caption',
                'link',
                'expected_comment_locale'
            )->where(
                'expected_comment_locale IS NOT NULL'
            )
        );

        $checker = new TikTokLangChecker($fetcher);

        foreach ($fetchedVideos as $fetchedVideo) {
            $expectedLocale = $fetchedVideo['expected_comment_locale'];
            self::assertSame(
                $expectedLocale,
                $checker->check($fetchedVideo['link']),
                'Video '
                    . $fetchedVideo['id']
                    . ' should have been commented in '
                    . $expectedLocale
                    . PHP_EOL
                    . $fetchedVideo['caption']
            );
        }


    }
}