<?php

namespace Oneup\AclBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class OneupAclExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('security.xml');

        // if doctrine/orm is available, load orm configuration
        if (class_exists('Doctrine\ORM\EntityManager')) {
            $loader->load('orm.xml');
        }

        if (class_exists('Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle')) {
            $loader->load('configuration.xml');
        }

        $container->setParameter('oneup_acl.remove_orphans', $config['remove_orphans']);
    }
}
