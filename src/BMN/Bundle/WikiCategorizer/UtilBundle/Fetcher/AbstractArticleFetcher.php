<?php

namespace BMN\Bundle\WikiCategorizer\UtilBundle\Fetcher;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class AbstractArticleFetcher
{
    protected $options;


    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        $this->options = $resolver->resolve($options);
    }


    /**
     * Fetch article data
     * @param  array  $opts extra options for fetch operation
     * @return mixed           handled fetch data
     */
    public function fetch(array $opts = array())
    {
        // build query options
        $resolver = new OptionsResolver();
        $this->getUrlParams($resolver);

        // send API request
        $ch = curl_init($this->options['url'] . '?' . http_build_query($resolver->resolve($opts)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->options['useragent']);
        $data = curl_exec($ch);
        if (!$data)
            throw new \RuntimeException("Could not fetch article data");
        return $this->handleData($data);
    }

    /**
     * Get default Fetch URL parameters
     * @return array default URL parameters
     */
    protected function getUrlParams(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'title',
        ));
    }

    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'useragent',
            'url',
        ));
    }

    /**
     * Handle fetched data
     * @param  string $data raw data from HTTP request
     * @return mixed        processed data
     */
    abstract protected function handleData($data);
}
