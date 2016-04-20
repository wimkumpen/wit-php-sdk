<?php
namespace Wit\HttpClients;

/**
 * Interface WitHttpClientInterface
 *
 * @package Wit
 */
interface WitHttpClientInterface
{
    /**
     * Sends a request to the server and returns the raw response.
     *
     * @param string $url     The endpoint to send the request to.
     * @param string $method  The request method.
     * @param string $body    The body of the request.
     * @param array  $headers The request headers.
     * @param int    $timeOut The timeout in seconds for the request.
     *
     * @return \Wit\Http\WitRawResponse Raw response from the server.
     *
     * @throws \Wit\Exceptions\WitSDKException
     */
    public function send($url, $method, $body, array $headers, $timeOut);
}
