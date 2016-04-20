<?php
namespace Wit\Http;

/**
 * Interface
 *
 * @package Wit
 */
interface RequestBodyInterface
{
    /**
     * Get the body of the request to send to Wit.
     *
     * @return string
     */
    public function getBody();
}
