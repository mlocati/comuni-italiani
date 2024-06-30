<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Build;

use DateTimeInterface;
use Generator;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;
use RuntimeException;
use stdClass;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @internal
 */
final readonly class Fetcher
{
    private const SERVICEID_GEOGRAPHICALSUBDIVISIONS = 71;
    private const SERVICEID_REGIONS = 68;
    private const SERVICEID_PROVINCES = 64;
    private const SERVICEID_MUNICIPALITIES = 61;

    private const PAGE_SIZE = 100;

    private readonly Validator $validator;

    public function __construct(
        private readonly ?CacheInterface $cache = null,
    ) {
        $this->validator = new Validator();
        $resolver = $this->validator->resolver();
        foreach([
            self::SERVICEID_GEOGRAPHICALSUBDIVISIONS,
            self::SERVICEID_REGIONS,
            self::SERVICEID_PROVINCES,
            self::SERVICEID_MUNICIPALITIES,
        ] as $serviceID) {
            $resolver->registerFile("https://github.com/mlocati/comuni-italiani/{$serviceID}", __DIR__ . "/data/schema-{$serviceID}.json");
        }
    }

    /**
     * @return \Generator<\stdClass>
     */
    public function listGeographicalSubdivisions(DateTimeInterface $date): Generator
    {
        return $this->list(self::SERVICEID_GEOGRAPHICALSUBDIVISIONS, $date);
    }

    /**
     * @return \Generator<\stdClass>
     */
    public function listRegions(DateTimeInterface $date): Generator
    {
        return $this->list(self::SERVICEID_REGIONS, $date);
    }

    /**
     * @return \Generator<\stdClass>
     */
    public function listProvinces(DateTimeInterface $date): Generator
    {
        return $this->list(self::SERVICEID_PROVINCES, $date);
    }

    /**
     * @return \Generator<\stdClass>
     */
    public function listMunicipalities(DateTimeInterface $date): Generator
    {
        return $this->list(self::SERVICEID_MUNICIPALITIES, $date);
    }

    /**
     * @return \Generator<\stdClass>
     */
    private function list(int $serviceID, DateTimeInterface $date): Generator
    {
        $listedRows = null;
        for ($page = 1; ; $page++) {
            $data = $this->getPage($serviceID, $date, $page);
            foreach ($data->body as $item) {
                yield $item;
                $listedRows++;
            }
            if ($listedRows >= $data->totalRows) {
                break;
            }
        }
    }

    private function getPage(int $serviceID, DateTimeInterface $date, int $page): \stdClass
    {
        $url = "https://situas.istat.it/ShibO2Module/api/Report/Spool/{$date->format('Y-m-d')}/{$serviceID}?PageNum={$page}&PageSize=" . self::PAGE_SIZE;
        $json = $this->getPageFromCache($url) ?? $this->downloadPage($url);
        $data = $this->getPageData($serviceID, $json);

        return $data;
    }

    private function getPageFromCache(string $url): ?string
    {
        if ($this->cache === null) {
            return null;
        }
        $cacheItem = $this->cache?->getItem(sha1($url));
        return $cacheItem?->isHit() ? $cacheItem->get() : null;
    }

    private function downloadPage(string $url): string
    {
        $payload = '{"orderFields":[],"orderDirects":[],"pFilterFields":[],"pFilterValues":[]}';
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'ignore_errors' => true,
                'user_agent' => 'mlocati/comuni-italiani Builder',
                'follow_location' => 0,
                'max_redirects' => 0,
                'header' => [
                    'Content-Length: ' . strlen($payload),
                    'Content-Type: application/json',
                    'Accept: application/json, text/plain, */*',
                ],
                'content' => $payload,
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false,
            ],
        ]);
        $http_response_header = [];
        $whyNot = '';
        set_error_handler(
            static function ($errno, $errstr) use (&$whyNot) {
                if ($whyNot === '' && is_string($errstr)) {
                    $whyNot = trim($errstr);
                }
            },
            -1,
        );
        try {
            $json = file_get_contents($url, false, $context);
        } finally {
            restore_error_handler();
        }
        $chunks = $http_response_header === [] ? [] : explode(' ', $http_response_header[0], 3);
        $responseCode = isset($chunks[1]) && is_numeric($chunks[1]) ? (int) $chunks[1] : null;
        if ($responseCode !== 200) {
            throw new RuntimeException($whyNot ?: "Invalid HTTP response code (expected: 200, received: {$responseCode}");
        }
        if ($json === false) {
            throw new RuntimeException($whyNot ?: 'file_get_contents() failed');
        }

        if ($this->cache !== null) {
            $cacheItem = $this->cache->getItem(sha1($url));
            $cacheItem->set($json);
            $this->cache->save($cacheItem);
        }

        return $json;
    }

    private function getPageData(int $serviceID, string $json): stdClass
    {
        $data = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
        $validationResult = $this->validator->validate($data, "https://github.com/mlocati/comuni-italiani/{$serviceID}");
        if ($validationResult->isValid()) {
            return $data;
        }
        $error = $validationResult->error();
        $formatter = new ErrorFormatter();
        $errorFormatted = $formatter->formatNested($error);
        $lines = [];
        $walker = null;
        $walker = static function (array $error, int $depth) use (&$walker, &$lines): void {
            foreach (preg_split('/[\r\n]+/', $error['message'], -1, PREG_SPLIT_NO_EMPTY) as $line) {
                $lines[] = str_repeat('   ', $depth) . $line;
            }
            foreach (($error['errors'] ?? []) as $subError) {
                $walker($subError, $depth + 1);
            }
        };

        $walker($errorFormatted, 0);
        throw new RuntimeException(implode("\n", $lines));
    }
}
