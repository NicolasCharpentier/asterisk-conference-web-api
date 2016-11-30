<?php

namespace Spitchee\Util\HttpClient;

use Curl\Curl;

/**
 * Class CurlClient
 * @package Spitchee\Util\HttpClient
 */
class CurlClient extends Curl
{
    /**
     * @return null
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * @return null
     */
    public function getBrutResponse() {
        return $this->rawResponse;
    }

    /**
     * @param $url
     * @param $params
     * @return mixed
     */
    protected function buildURI($url, $params) {
        foreach ($params as $key => $value) {
            $url = str_replace(":$key", urlencode($value), $url);
        }
        
        return $url;
    }

    /**
     * @param $url
     * @param array $data
     * @param bool $follow303
     * @return $this
     */
    protected function fluidPost($url, $data = [], $follow303 = false) {
        $this->post($url, $data, $follow303);

        return $this;
    }

    /**
     * @param $url
     * @param array $data
     * @param bool $follow303
     * @return CurlClient
     */
    protected function fluidJsonPost($url, $data = [], $follow303 = false) {
        $this->setHeader('Content-type', 'application/json');

        return $this->fluidPost($url, $data, $follow303);
    }
}