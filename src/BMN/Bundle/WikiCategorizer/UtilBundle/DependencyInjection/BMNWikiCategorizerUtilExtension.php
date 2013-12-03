<?php

namespace BMN\Bundle\WikiCategorizer\UtilBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BMNWikiCategorizerUtilExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('bmn_wiki_categorizer_util.mediawiki_article_fetcher.options', $config['mediawiki_article_fetcher']);
        $container->setParameter('bmn_wiki_categorizer_util.mediawiki_tokenizer.options', $config['mediawiki_tokenizer']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setAlias('lorem_generator', 'bmn_wiki_categorizer_util.lorem_generator');
        $container->setAlias('mediawiki_article_fetcher', 'bmn_wiki_categorizer_util.mediawiki_article_fetcher');
        $container->setAlias('mediawiki_tokenizer', 'bmn_wiki_categorizer_util.mediawiki_tokenizer');
    }
}
