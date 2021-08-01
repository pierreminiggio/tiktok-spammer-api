<?php

namespace App;

use App\Command\Editor\UpdateCommand;
use App\Command\EndProcessCommand;
use App\Command\Social\TikTok\PostCommand;
use App\Command\Video\CreateCommand;
use App\Command\Video\FinishCommand;
use App\Controller\DetailController;
use App\Controller\DownloaderController;
use App\Controller\Editor\Text\Preset\ListController;
use App\Controller\Editor\UpdateController;
use App\Controller\Render\DisplayController;
use App\Controller\Social\TikTok\PostController;
use App\Controller\Social\TikTok\VideoFileController;
use App\Controller\Video\CreateController;
use App\Controller\EndProcessController;
use App\Controller\ThumbnailController;
use App\Controller\ToProcessDetailController;
use App\Controller\ToProcessListController;
use App\Controller\Video\FinishController;
use App\Http\Request\JsonBodyParser;
use App\Normalizer\NormalizerFactory;
use App\Query\Account\PostedOnAccountsQuery;
use App\Query\Account\SocialMediaAccountsByContentQuery;
use App\Query\Account\TikTok\CanVideoBePostedOnThisTikTokAccountQuery;
use App\Query\Account\TikTok\PredictedNextPostTimeQuery;
use App\Query\Account\TikTok\VideoFileQuery;
use App\Query\Editor\Preset\ListQuery;
use App\Query\Render\CurrentRenderStatusForVideoQuery;
use App\Query\ToProcessDetailQuery;
use App\Query\ToProcessListQuery;
use App\Query\Video\TikTok\CurrentUploadStatusForTiKTokQuery;
use App\Query\Video\VideoDetailQuery;
use App\Query\VideoLinkQuery;
use App\Serializer\Serializer;
use App\Serializer\SerializerInterface;
use PierreMiniggio\DatabaseConnection\DatabaseConnection;
use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;
use PierreMiniggio\MP4YoutubeVideoDownloader\Downloader;
use RuntimeException;

class App
{
    public function run(
        string $path,
        ?string $queryParameters,
        ?string $authHeader,
        ?string $origin,
        ?string $accessControlRequestHeaders
    ): void
    {

        if ($origin) {
            header('Access-Control-Allow-Origin: ' . $origin);
        }

        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

        if ($accessControlRequestHeaders) {
            header('Access-Control-Allow-Headers: ' . $accessControlRequestHeaders);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        header('Content-Type: application/json');

        $config = require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.php';

        $dbConfig = $config['db'];
        $fetcher = new DatabaseFetcher(new DatabaseConnection(
            $dbConfig['host'],
            $dbConfig['database'],
            $dbConfig['username'],
            $dbConfig['password'],
            DatabaseConnection::UTF8_MB4
        ));

        if ($path === '/') {
            $this->protectUsingToken($authHeader, $config);
            var_dump('test');
            exit;
        }

        http_response_code(404);
        exit;
    }

    protected function protectUsingToken(?string $authHeader, array $config): void
    {
        if (! isset($config['token'])) {
            throw new RuntimeException('bad config, no token');
        }

        $token = $config['token'];

        if (! $authHeader || $authHeader !== 'Bearer ' . $token) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
    }
}
