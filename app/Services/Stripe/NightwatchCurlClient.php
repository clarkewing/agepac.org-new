<?php

namespace App\Services\Stripe;

use Closure;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;
use GuzzleHttp\Psr7\Utils as Psr7Utils;
use Laravel\Nightwatch\Core as NightwatchCore;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Stripe\HttpClient\CurlClient;
use Stripe\Util\CaseInsensitiveArray;
use Throwable;

/**
 * A minimal CurlClient that emits Nightwatch outgoingRequest() events
 * for standard (non-streaming) Stripe HTTP calls.
 */
class NightwatchCurlClient extends CurlClient
{
    /** Normalize ?callable to ?Closure (PHP doesn't allow callable-typed properties) */
    private ?Closure $redactor;

    public function __construct(
        private readonly NightwatchCore $nightwatch,
        array $defaultOptions = [],
        ?callable $redactor = null,
    ) {
        parent::__construct($defaultOptions);
        $this->redactor = $redactor ? $redactor(...) : null;
    }

    /**
     * @param  string  $method
     * @param  string  $absUrl
     * @param  array<string,string>  $headers
     * @param  array<string,mixed>|string|null  $params
     * @param  bool  $hasFile
     * @param  null|string  $apiMode
     * @return array{0:string,1:int,2:CaseInsensitiveArray}
     */
    public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = null) // @pest-ignore-type
    {
        // Respect Nightwatch's filtering/paused flags (same idea as their Guzzle middleware)
        if ($this->nightwatch->config['filtering']['ignore_outgoing_requests'] || $this->nightwatch->paused()) {
            return parent::request($method, $absUrl, $headers, $params, $hasFile, $apiMode);
        }

        // Build a PSR-7 snapshot of the request *before* executing
        $psrRequest = $this->buildPsr7Request($method, $absUrl, $headers, $params, $hasFile);

        // Get start time from Nightwatch's clock; if it fails, fall back to a plain call
        try {
            $start = $this->nightwatch->clock->microtime();
        } catch (Throwable $e) {
            $this->nightwatch->report($e, handled: true);

            return parent::request($method, $absUrl, $headers, $params, $hasFile, $apiMode);
        }

        // Execute the real Stripe request
        try {
            $response = parent::request($method, $absUrl, $headers, $params, $hasFile, $apiMode);
        } catch (Throwable $e) {
            // Optionally: emit a synthetic "failed" response here if you want it on the timeline.
            try {
                $this->nightwatch->report($e, handled: true);
            } catch (Throwable $e2) {
                $this->nightwatch->report($e2, handled: true);
            }
            throw $e;
        }

        // Convert response to PSR-7 and emit Nightwatch event
        try {
            $end = $this->nightwatch->clock->microtime();

            [$body, $status, $respHeaders] = $response;
            $psrResponse = $this->buildPsr7Response($status, $respHeaders, $body);

            $this->nightwatch->outgoingRequest($start, $end, $psrRequest, $psrResponse);
        } catch (Throwable $e) {
            $this->nightwatch->report($e, handled: true);
        }

        return $response;
    }

    /**
     * Build a PSR-7 request snapshot that mirrors what Stripe will send.
     *
     * We *do not* mutate the original $headers passed to Stripe; we work on a copy
     * so our snapshot can add Content-Type hints for Nightwatch without affecting the real request.
     *
     * @param  array<string,string>  $headers
     * @param  string|array<string,mixed>|null  $params
     */
    private function buildPsr7Request(string $method, string $absUrl, array $headers, array|string|null $params, bool $hasFile): RequestInterface
    {
        $method = strtoupper($method);
        $url = $absUrl;
        $body = null;

        // Clone headers for the snapshot to avoid mutating the actual Stripe call
        $snapshotHeaders = $headers;

        if ($method === 'GET' && is_array($params) && $params !== []) {
            $qs = http_build_query($params);
            $url .= (str_contains($absUrl, '?') ? '&' : '?').$qs;
        } else {
            if (is_array($params)) {
                if ($hasFile) {
                    $snapshotHeaders['Content-Type'] = $snapshotHeaders['Content-Type'] ?? 'multipart/form-data';
                    $body = '[multipart body omitted]';
                } else {
                    $snapshotHeaders['Content-Type'] = $snapshotHeaders['Content-Type'] ?? 'application/x-www-form-urlencoded';
                    $body = http_build_query($params);
                }
            } elseif (is_string($params)) {
                $body = $params;
            }
        }

        // Optional redaction
        [$snapshotHeaders, $body] = $this->maybeRedact($snapshotHeaders, $body);

        return new Psr7Request($method, $url, $this->normalizeHeaders($snapshotHeaders), Psr7Utils::streamFor($body ?? ''));
    }

    /**
     * @param  CaseInsensitiveArray|array<string,string|string[]>  $respHeaders
     */
    private function buildPsr7Response(int $status, array|CaseInsensitiveArray $respHeaders, string $body): ResponseInterface
    {
        $headers = $respHeaders instanceof CaseInsensitiveArray
            ? iterator_to_array($respHeaders)
            : $respHeaders;

        // Optional redaction
        [$headers, $body] = $this->maybeRedact($headers, $body);

        return new Psr7Response($status, $this->normalizeHeaders($headers), Psr7Utils::streamFor($body));
    }

    /**
     * Redact headers/body if a redactor was supplied.
     *
     * The callable signature is: function (string $type, mixed $payload): mixed
     *  - $type in {'headers','body'}
     *  - For 'headers': array<string,string|string[]>
     *  - For 'body': string
     *
     * @param  array<string,string|string[]>  $headers
     * @return array{0:array<string,string|string[]>,1:string|null}
     */
    private function maybeRedact(array $headers, ?string $body): array
    {
        if (! $this->redactor) {
            return [$headers, $body];
        }

        $headers = ($this->redactor)('headers', $headers);
        $body = $body !== null ? ($this->redactor)('body', $body) : null;

        return [$headers, $body];
    }

    /**
     * Normalize header values to PSR-7-compatible arrays.
     *
     * @param  array<string,string|string[]>  $headers
     * @return array<string,string[]>
     */
    private function normalizeHeaders(array $headers): array
    {
        return array_map(function (array|string $header) {
            return is_array($header) ? array_values($header) : [$header];
        }, $headers);
    }
}
