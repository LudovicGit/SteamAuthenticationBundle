<?php

namespace Soljian\SteamAuthenticationBundle\DependencyInjection;

use Soljian\SteamAuthenticationBundle\Security\Authentication\Validator\RequestValidator;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Soljian
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('soljian_steam_authentication');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('api_key')->end()
                ->scalarNode('login_route')->end()
                ->scalarNode('login_redirect')->end()
                ->scalarNode('user_class')->end()
                ->scalarNode('request_validator_class')->defaultValue(RequestValidator::class)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
