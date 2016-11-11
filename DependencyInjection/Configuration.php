<?php

namespace SmarterSolutions\EzComponents\EzLanguageSwitcherBundle\DependencyInjection;

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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ez_language_switcher');

        $rootNode
            ->children()
                ->arrayNode('names')
                    ->useAttributeAsKey('key')
                    ->example(
                        array(
                            'esl-ES' => 'Castellano',
                            'cat-ES' => 'Català',
                            'eng-GB' => 'English'
                        )
                    )
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
