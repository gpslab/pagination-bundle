<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $builder->root('gpslab_pagination')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('max_navigate')
                    ->defaultValue(5)
                ->end()
                ->scalarNode('parameter_name')
                    ->defaultValue('page')
                ->end()
                ->scalarNode('template')
                    ->defaultValue('GpsLabPaginationBundle::pagination.html.twig')
                ->end()
            ->end();

        return $builder;
    }
}
