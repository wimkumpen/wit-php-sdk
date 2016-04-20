<?php
namespace Wit;

use Wit\Url\WitUrlManipulator;
use Wit\Exceptions\WitSDKException;

/**
 * Class Request
 *
 * @package Wit
 */
class WitRequest
{
    /**
     * @var string|null The access token to use for this request.
     */
    protected $accessToken;

    /**
     * @var string The HTTP method for this request.
     */
    protected $method;

    /**
     * @var string The Wit endpoint for this request.
     */
    protected $endpoint;

    /**
     * @var array The headers to send with this request.
     */
    protected $headers = [];

    /**
     * @var array The parameters to send with this request.
     */
    protected $params = [];

    /**
     * @var string Wit version to use for this request.
     */
    protected $witVersion;

    /**
     * Creates a new Request entity.
     *
     * @param AccessToken|string|null $accessToken
     * @param string|null             $method
     * @param string|null             $endpoint
     * @param array|null              $params
     * @param string|null             $witVersion
     */
    public function __construct($accessToken = null, $method = null, $endpoint = null, array $params = [], $witVersion = null)
    {
        $this->setAccessToken($accessToken);
        $this->setMethod($method);
        $this->setEndpoint($endpoint);
        $this->setParams($params);
        $this->witVersion = $witVersion ?: Wit::DEFAULT_WIT_VERSION;
    }

    /**
     * Set the access token for this request.
     *
     * @param AccessToken|string
     *
     * @return WitRequest
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Return the access token for this request.
     *
     * @return string|null
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set the HTTP method for this request.
     *
     * @param string
     *
     * @return WitRequest
     */
    public function setMethod($method)
    {
        $this->method = strtoupper($method);
        
        return $this;
    }

    /**
     * Return the HTTP method for this request.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Validate that the HTTP method is set.
     *
     * @throws WitSDKException
     */
    public function validateMethod()
    {
        if (!$this->method) {
            throw new WitSDKException('HTTP method not specified.');
        }

        if (!in_array($this->method, ['GET', 'POST', 'DELETE'])) {
            throw new WitSDKException('Invalid HTTP method specified.');
        }
    }

    /**
     * Set the endpoint for this request.
     *
     * @param string
     *
     * @return WitRequest
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        
        return $this;
    }

    /**
     * Return the HTTP method for this request.
     *
     * @return string
     */
    public function getEndpoint()
    {
        // For batch requests, this will be empty
        return $this->endpoint;
    }

    /**
     * Generate and return the headers for this request.
     *
     * @return array
     */
    public function getHeaders()
    {
        $headers = static::getDefaultHeaders();

        return array_merge($this->headers, $headers);
    }

    /**
     * Set the headers for this request.
     *
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
        
        return $this;
    }

    /**
     * Set the params for this request without filtering them first.
     *
     * @param array $params
     *
     * @return WitRequest
     */
    public function dangerouslySetParams(array $params = [])
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }
    
    /**
     * Set the params for this request.
     *
     * @param array $params
     *
     * @return WitRequest
     *
     * @throws WitSDKException
     */
    public function setParams(array $params = [])
    {
        $this->dangerouslySetParams($params);

        return $this;
    }

    /**
     * Generate and return the params for this request.
     *
     * @return array
     */
    public function getParams()
    {
        $params = $this->params;

        return $params;
    }

    /**
     * Only return params on POST requests.
     *
     * @return array
     */
    public function getPostParams()
    {
        if ($this->getMethod() === 'POST') {
            return $this->getParams();
        }

        return [];
    }

    /**
     * The Wit version used for this request.
     *
     * @return string
     */
    public function getWitVersion()
    {
        return $this->witVersion;
    }

    /**
     * Generate and return the URL for this request.
     *
     * @return string
     */
    public function getUrl()
    {
        $this->validateMethod();

        $url = WitUrlManipulator::forceSlashPrefix($this->getEndpoint());

        if ($this->getMethod() !== 'POST') {
            $params = $this->getParams();
            $params['v'] = $this->getWitVersion();
            $url = WitUrlManipulator::appendParamsToUrl($url, $params);
        } else {
            $url = WitUrlManipulator::appendParamsToUrl($url, array('v' => $this->getWitVersion())); 
        }

        return $url;
    }

    /**
     * Return the default headers that every request should use.
     *
     * @return array
     */
    public static function getDefaultHeaders()
    {
        return [
            'User-Agent' => 'wit-php-' . Wit::VERSION,
            'Accept-Encoding' => '*',
        ];
    }
}
