<?php

namespace BMN\Bundle\WikiCategorizer\UtilBundle\Fetcher;

class MediaWikiArticleFetcher extends AbstractArticleFetcher
{
    /**
     * MediaWiki Host URL
     * @var string
     */
    private $url;


    public function __construct($userAgent, $url)
    {
        parent::__construct($userAgent);
        $this->url = $url;
    }


    /**
     * {@inheritdoc}
     */
    protected function getUrl()
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    protected function getUrlParams(array $options)
    {
        if (!isset($options['title']))
            throw new InvalidArgumentException('Missing "title" key for MediaWikiArticleFetcher');

        $superParams = parent::getUrlParams($options);

        return array_merge($superParams, array(
            'format' => 'json',
            'action' => 'query',
            'prop' => implode('|', array('revisions', 'categories')),
            'rvprop' => 'content',
            'cllimit' => 500,
            'titles' => $options['title'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function handleData($json)
    {
        $wikiData = json_decode($json, true);

        foreach ($wikiData['query']['pages'] as $id => $page) {
            // there should only be one $page
            if (isset($page['missing'])) return false;
            $categories = '';
            if (isset($page['categories']))
                foreach ($page['categories'] as $category)
                    $categories .= $category['title'] . "\n";

            $categories = trim($categories);

            $content = $page['revisions'][0]['*'];
            return compact('content', 'categories');
        }
    }
}
