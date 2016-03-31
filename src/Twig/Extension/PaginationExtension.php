<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\PaginationBundle\Twig\Extension;

use AnimeDb\Bundle\PaginationBundle\Service\Configuration;

/**
 * Class PaginationExtension
 * @package AnimeDb\Bundle\PaginationBundle\Twig\Extension
 */
class PaginationExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    protected $template = '';

    /**
     * @param string $template
     */
    public function __construct($template)
    {
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'pagination_render',
                [$this, 'render'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            )
        ];
    }

    /**
     * Renders the pagination template
     *
     * @param \Twig_Environment $env
     * @param Configuration $pagination
     * @param string $template
     * @param array $view_params
     *
     * @return string
     */
    public function render(
        \Twig_Environment $env,
        Configuration $pagination,
        $template = null,
        array $view_params = []
    ) {
        return $env->render(
            $template ?: $this->template,
            array_merge(
                $view_params,
                ['pagination' => $pagination->getView()]
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'anime_db_pagination_extension';
    }
}
