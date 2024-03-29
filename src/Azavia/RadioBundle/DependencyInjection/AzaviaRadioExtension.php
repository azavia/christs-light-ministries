<?php

namespace Azavia\RadioBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AzaviaRadioExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $container->setParameter('azavia_radio.processed_track_dir', $config['processed_track_dir']);
        $container->setParameter('azavia_radio.unprocessed_track_dir', $config['unprocessed_track_dir']);
        $container->setParameter('azavia_radio.live365_username', $config['live365_username']);
        $container->setParameter('azavia_radio.live365_password', $config['live365_password']);
        $container->setParameter(
                'azavia_radio.twitter.consumer_key',
                $config['twitter']['consumer_key']);
        $container->setParameter(
                'azavia_radio.twitter.consumer_secret',
                $config['twitter']['consumer_secret']);
        $container->setParameter(
                'azavia_radio.twitter.access_token',
                $config['twitter']['access_token']);
        $container->setParameter(
                'azavia_radio.twitter.access_token_secret',
                $config['twitter']['access_token_secret']);
    }
}
