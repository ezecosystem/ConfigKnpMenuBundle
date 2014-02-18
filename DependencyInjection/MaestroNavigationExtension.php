<?php

namespace Maestro\Bundle\NavigationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MaestroNavigationExtension extends Extension
{
    const ADMIN_MENU = "maestro_admin_menu";

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $adminMenu = array();

        foreach ($container->getParameter('kernel.bundles') as $bundle) {
            $reflection = new \ReflectionClass($bundle);
            if (is_file($file = dirname($reflection->getFilename()) . '/Resources/config/navigation.yml')) {
                $bundleConfig = Yaml::parse(realpath($file));

                $adminMenu = $this->mergeConfig($adminMenu, $bundleConfig);
            }
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container
            ->getDefinition('maestro.menu.builder')
            ->addMethodCall('loadConfiguration', array($adminMenu));
    }

    /**
     * Merge Bundle Configuration with Admin Menu Configuration
     *
     * @param array $adminMenu the current admin menu configuration
     * @param array $config the configuration parsed in the bundle
     *
     * @return array
     */
    protected function mergeConfig(array $adminMenu, array $config)
    {
        return array_merge($adminMenu, $config);
    }
}