<?php
namespace Wit\Http;

/**
 * Class RequestBodyUrlEncoded
 *
 * @package Wit
 */
class RequestBodyUrlEncoded implements RequestBodyInterface
{
    /**
     * @var array The parameters to send with this request.
     */
    protected $params = [];

    /**
     * Creates a new WitUrlEncodedBody entity.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return json_encode($this->params);
    }
}
