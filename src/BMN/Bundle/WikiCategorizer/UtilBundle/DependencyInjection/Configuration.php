<?php

namespace BMN\Bundle\WikiCategorizer\UtilBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bmn_wiki_categorizer_util');

        $rootNode
            ->children()
                ->arrayNode('mediawiki_article_fetcher')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('user_agent')->isRequired()->end()
                        ->scalarNode('url')->isRequired()->end()
                        ->booleanNode('render_wiki_text')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('mediawiki_tokenizer')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('strip_tags')->defaultTrue()->end()
                        ->booleanNode('strip_apostrophes')->defaultTrue()->end()
                        ->booleanNode('remove_stop_words')->defaultTrue()->end()
                        ->booleanNode('stemming')->defaultTrue()->end()
                        ->booleanNode('parse_wiki_text')->defaultTrue()->end()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
