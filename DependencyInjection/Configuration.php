<?php

namespace EnuygunCom\DfpBundle\DependencyInjection;

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
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('enuygun_com_dfp');

        $rootNode
            ->children()
            ->scalarNode('publisher_id')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('default_class')->defaultValue('dfp-ad-unit')->end()
            ->scalarNode('cache_lifetime')->defaultValue(300)->end()
            ->variableNode('env')->defaultValue(array('prod'))->end()
            ->variableNode('targets')->end()
            ->end()
        ;

        return $treeBuilder;
    }
    /**
     * Generates the configuration tree.
     */
    public function getConfigTree()
    {
        return $this->getConfigTreeBuilder()->buildTree();
    }
}
