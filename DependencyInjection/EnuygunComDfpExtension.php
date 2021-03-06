<?php

namespace EnuygunCom\DfpBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class EnuygunComDfpExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();


        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');


        $container->setParameter('enuygun_com_dfp.publisher_id', $config['publisher_id']);
        $container->setParameter('enuygun_com_dfp.default_class', $config['default_class']);
        $container->setParameter('enuygun_com_dfp.targets', $config['targets']);
        $container->setParameter('enuygun_com_dfp.env', $config['env']);
        $container->setParameter('enuygun_com_dfp.cache_lifetime', $config['cache_lifetime']);
    }
}
