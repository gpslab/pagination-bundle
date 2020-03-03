<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $tree_builder = new TreeBuilder('gpslab_pagination');

        if (method_exists($tree_builder, 'getRootNode')) {
            // Symfony 4.2 +
            $root = $tree_builder->getRootNode();
        } else {
            // Symfony 4.1 and below
            $root = $tree_builder->root('gpslab_pagination');
        }

        // @codeCoverageIgnoreStart
        if (!$root instanceof ArrayNodeDefinition) {
            throw new \RuntimeException(sprintf('Config root node must be a "%s", given "%s".', 'Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition', get_class($root)));
        }
        // @codeCoverageIgnoreEnd

        $root->addDefaultsIfNotSet();
        $root->children()->scalarNode('max_navigate')->defaultValue(5);
        $root->children()->scalarNode('parameter_name')->defaultValue('page');
        $root->children()->scalarNode('template')->defaultValue('GpsLabPaginationBundle::pagination.html.twig');

        return $tree_builder;
    }
}
