<?php

namespace BMN\Bundle\WikiCategorizer\UtilBundle\Fetcher;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MediaWikiArticleFetcher extends AbstractArticleFetcher
{
    /**
     * {@inheritdoc}
     */
    protected function getUrlParams(OptionsResolverInterface $resolver)
    {
        parent::getUrlParams($resolver);

        $props = array('categories');

        if ($this->options['renderWikiText']) {
            $props[] = 'extracts';
            $resolver->setDefaults(array(
                'explaintext' => '',
                'exlimit' => 1,
            ));
        } else {
            $props[] = 'revisions';
            $resolver->setDefaults(array(
                'rvprop' => 'content',
            ));
        }

        $resolver->setDefaults(array(
            'format' => 'json',
            'action' => 'query',
            'prop' => implode('|', $props),
            'titles' => function (Options $options) {
                return $options['title'];
            },
        ));

    }

    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'renderWikiText' => true,
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

            $content = $this->options['renderWikiText'] ? $page['extract'] : $page['revisions'][0]['*'];
            return compact('content', 'categories');
        }
    }
}
