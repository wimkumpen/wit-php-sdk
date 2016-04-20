<?php
namespace Wit\Exceptions;

use Wit\WitResponse;

/**
 * Class WitResponseException
 *
 * @package Wit
 */
class WitResponseException extends WitSDKException
{
    /**
     * @var WitResponse The response that threw the exception.
     */
    protected $response;

    /**
     * @var array Decoded response.
     */
    protected $responseData;

    /**
     * Creates a WitResponseException.
     *
     * @param WitResponse     $response          The response that threw the exception.
     * @param WitSDKException $previousException The more detailed exception.
     */
    public function __construct(WitResponse $response, WitSDKException $previousException = null)
    {
        $this->response = $response;
        $this->responseData = $response->getDecodedBody();

        $code = $this->get('code', -1);

        if ($this->get('error', false)) {
            $message = $this->get('error');
        } else if ($this->get('errors', false)) {
            foreach($this->get('errors') as $error) {
                $message = $error;
            }
        } else {
            $message = 'Unknown error from Wit.';
        }

        switch ($code) {
            case 'no-auth':
                return new static($response, new WitAuthorizationException($message, 401));
        }

        parent::__construct($message, $code, $previousException);
    }

    /**
     * A factory for creating the appropriate exception based on the response from Wit.
     *
     * @param WitResponse $response The response that threw the exception.
     *
     * @return WitResponseException
     */
    public static function create(WitResponse $response)
    {
        $data = $response->getDecodedBody();

        $code = isset($data['code']) ? $data['code'] : null;

        if (isset($data['error'])) {
            $message = $data['error'];
        } else if (isset($data['errors'])) {
            foreach($data['errors'] as $error) {
                $message = $error;
            }
        } else {
            $message = 'Unknown error from Wit.';
        }

        switch ($code) {
            case 'no-auth':
                return new static($response, new WitAuthorizationException($message, 401));
        }

        return new static($response, new WitOtherException($message, $code));
    }

    /**
     * Checks isset and returns that or a default value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    private function get($key, $default = null)
    {
        if (isset($this->responseData[$key])) {
            return $this->responseData[$key];
        }

        return $default;
    }

    /**
     * Returns the HTTP status code
     *
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->response->getHttpStatusCode();
    }

    /**
     * Returns the sub-error code
     *
     * @return int
     */
    public function getSubErrorCode()
    {
        return $this->get('error_subcode', -1);
    }

    /**
     * Returns the error type
     *
     * @return string
     */
    public function getErrorType()
    {
        return $this->get('type', '');
    }

    /**
     * Returns the raw response used to create the exception.
     *
     * @return string
     */
    public function getRawResponse()
    {
        return $this->response->getBody();
    }

    /**
     * Returns the decoded response used to create the exception.
     *
     * @return array
     */
    public function getResponseData()
    {
        return $this->responseData;
    }

    /**
     * Returns the response entity used to create the exception.
     *
     * @return WitResponse
     */
    public function getResponse()
    {
        return $this->response;
    }
}
