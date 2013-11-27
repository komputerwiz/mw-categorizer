<?php

namespace BMN\Bundle\WikiCategorizer\UtilBundle\Fetcher;

abstract class AbstractArticleFetcher
{
    /**
     * User Agent to use when making HTTP requests
     * @var string
     */
    private $userAgent;


    public function __construct($userAgent)
    {
        $this->userAgent = $userAgent;
    }


    /**
     * Fetch article data
     * @param  array  $opts extra options for fetch operation
     * @return mixed           handled fetch data
     */
    public function fetch(array $opts)
    {
        // send API request
        $ch = curl_init($this->getUrl() . '?' . http_build_query($this->getUrlParams($opts)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        $data = curl_exec($ch);
        return $this->handleData($data);
    }


    /**
     * Get URL to which API requests should be made
     * @return string
     */
    abstract protected function getUrl();

    /**
     * Get default Fetch URL parameters
     * @return array default URL parameters
     */
    protected function getUrlParams(array $opts)
    {
        return array();
    }

    /**
     * Handle fetched data
     * @param  string $data raw data from HTTP request
     * @return mixed        processed data
     */
    abstract protected function handleData($data);
}
