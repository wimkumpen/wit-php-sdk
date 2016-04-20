<?php
namespace Wit;

use Wit\HttpClients\WitHttpClientInterface;
use Wit\HttpClients\WitCurlHttpClient;
use Wit\HttpClients\WitStreamHttpClient;
use Wit\Exceptions\WitSDKException;

/**
 * Class WitClient
 *
 * @package Wit
 */
class WitClient
{
    /**
     * @const string Production Wit API URL.
     */
    const BASE_WIT_URL = 'https://api.wit.ai';

    /**
     * @const int The timeout in seconds for a normal request.
     */
    const DEFAULT_REQUEST_TIMEOUT = 60;

    /**
     * @var WitHttpClientInterface HTTP client handler.
     */
    protected $httpClientHandler;

    /**
     * @var int The number of calls that have been made to Wit.
     */
    public static $requestCount = 0;

    /**
     * Instantiates a new WitClient object.
     *
     * @param WitHttpClientInterface|null $httpClientHandler
     */
    public function __construct(WitHttpClientInterface $httpClientHandler)
    {
        $this->httpClientHandler = $httpClientHandler;
    }

    /**
     * Sets the HTTP client handler.
     *
     * @param WitHttpClientInterface $httpClientHandler
     */
    public function setHttpClientHandler(WitHttpClientInterface $httpClientHandler)
    {
        $this->httpClientHandler = $httpClientHandler;
    }

    /**
     * Returns the HTTP client handler.
     *
     * @return WitHttpClientInterface
     */
    public function getHttpClientHandler()
    {
        return $this->httpClientHandler;
    }

    /**
     * Prepares the request for sending to the client handler.
     *
     * @param WitRequest $request
     *
     * @return array
     */
    public function prepareRequestMessage(WitRequest $request)
    {
        $url = static::BASE_WIT_URL . $request->getUrl();

        $jsonPost = json_encode($request->getPostParams());
        $request->setHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $request->getAccessToken()
        ]);

        return [
            $url,
            $request->getMethod(),
            $request->getHeaders(),
            $jsonPost,
        ];
    }

    /**
     * Makes the request to Wit and returns the result.
     *
     * @param WitRequest $request
     *
     * @return WitResponse
     *
     * @throws WitSDKException
     */
    public function sendRequest(WitRequest $request)
    {
        list($url, $method, $headers, $body) = $this->prepareRequestMessage($request);

        $timeOut = static::DEFAULT_REQUEST_TIMEOUT;

        // Should throw `WitSDKException` exception on HTTP client error.
        // Don't catch to allow it to bubble up.
        $rawResponse = $this->httpClientHandler->send($url, $method, $body, $headers, $timeOut);

        static::$requestCount++;

        $returnResponse = new WitResponse(
            $request,
            $rawResponse->getBody(),
            $rawResponse->getHttpResponseCode(),
            $rawResponse->getHeaders()
        );

        if ($returnResponse->isError()) {
            throw $returnResponse->getThrownException();
        }

        return $returnResponse;
    }
}
