<?php
namespace Wit;

use Wit\Exceptions\WitSDKException;
use Wit\HttpClients\HttpClientsFactory;

/**
 * Class Wit
 *
 * @package Wit
 */
class Wit
{
    /**
     * @const string Version number of the Wit PHP SDK.
     */
    const VERSION = '1.0.0';

    /**
     * @const string Default Wit API version for requests.
     */
    const DEFAULT_WIT_VERSION = '20141022';

    /**
     * @var WitClient The Wit client service.
     */
    protected $client;

    /**
     * @var AccessToken|null The default access token to use with requests.
     */
    protected $defaultAccessToken;

    /**
     * @var string|null The default Wit version we want to use.
     */
    protected $defaultWitVersion;

    /**
     * @var WitResponse|null Stores the last request made to Wit.
     */
    protected $lastResponse;

    /**
     * Instantiates a new Wit super-class object.
     *
     * @param array $config
     *
     * @throws WitSDKException
     */
    public function __construct(array $config = [])
    {
        $config = array_merge([
            'default_wit_version' => static::DEFAULT_WIT_VERSION,
            'default_access_token' => null,
            'http_client_handler' => null,
            'persistent_data_handler' => null,
        ], $config);

        if (is_null($config['default_access_token'])) {
            throw new WitSDKException('You forgot to set your token with param "default_access_token".');
        }

        $this->client = new WitClient(
            HttpClientsFactory::createHttpClient($config['http_client_handler'])
        );

        $this->defaultAccessToken = $config['default_access_token'];
        $this->defaultWitVersion = $config['default_wit_version'];
    }

    /**
     * Returns the default AccessToken entity.
     *
     * @return AccessToken|null
     */
    public function getDefaultAccessToken()
    {
        return $this->defaultAccessToken;
    }

    /**
     * Returns the default Wit version.
     *
     * @return string
     */
    public function getDefaultWitVersion()
    {
        return $this->defaultWitVersion;
    }

    /**
     * Returns the WitClient service.
     *
     * @return WitClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Sends a GET request to Wit and returns the result.
     *
     * @param string                  $endpoint
     *
     * @return WitResponse
     *
     * @throws WitSDKException
     */
    public function get($endpoint)
    {
        return $this->sendRequest(
            'GET',
            $endpoint,
            $params = []
        );
    }

    /**
     * Sends a POST request to Wit and returns the result.
     *
     * @param string                  $endpoint
     * @param array                   $params
     *
     * @return WitResponse
     *
     * @throws WitSDKException
     */
    public function post($endpoint, array $params = [])
    {
        return $this->sendRequest(
            'POST',
            $endpoint,
            $params
        );
    }

    /**
     * Sends a DELETE request to Wit and returns the result.
     *
     * @param string                  $endpoint
     * @param array                   $params
     * @param AccessToken|string|null $accessToken
     * @param string|null             $witVersion
     *
     * @return WitResponse
     *
     * @throws WitSDKException
     */
    public function delete($endpoint, array $params = [])
    {
        return $this->sendRequest(
            'DELETE',
            $endpoint,
            $params
        );
    }

    /**
     * Sends a request to Wit and returns the result.
     *
     * @param string                  $method
     * @param string                  $endpoint
     * @param array                   $params
     *
     * @return WitResponse
     *
     * @throws WitSDKException
     */
    public function sendRequest($method, $endpoint, array $params = [])
    {
        $request = $this->request($method, $endpoint, $params);

        return $this->lastResponse = $this->client->sendRequest($request);
    }
    
    /**
     * Instantiates a new WitRequest entity.
     *
     * @param string                  $method
     * @param string                  $endpoint
     * @param array                   $params
     *
     * @return WitRequest
     *
     * @throws WitSDKException
     */
    public function request($method, $endpoint, array $params = [])
    {
        return new WitRequest(
            $this->defaultAccessToken,
            $method,
            $endpoint,
            $params,
            $this->defaultWitVersion
        );
    }

    /**
     * Returns the last response returned from Wit.
     *
     * @return WitResponse|null
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }
}
