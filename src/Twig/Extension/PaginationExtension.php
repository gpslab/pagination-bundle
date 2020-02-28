<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Twig\Extension;

use GpsLab\Bundle\PaginationBundle\Service\Configuration;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PaginationExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $template;

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
            new TwigFunction(
                'pagination_render',
                [$this, 'renderPagination'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    /**
     * @param \Twig\Environment $env
     * @param Configuration     $pagination
     * @param string            $template
     * @param array             $view_params
     * @param int               $max_navigate
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     * @return string
     */
    public function renderPagination(
        Environment $env,
        Configuration $pagination,
        $template = null,
        array $view_params = [],
        $max_navigate = 0
    ) {
        if ($max_navigate > 0) {
            // not change original object
            $new_pagination = clone $pagination;
            $new_pagination->setMaxNavigate($max_navigate);

            $pagination_view = $new_pagination->getView();
        } else {
            $pagination_view = $pagination->getView();
        }

        return $env->render(
            $template ?: $this->template,
            array_merge($view_params, ['pagination' => $pagination_view])
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gpslab_pagination_extension';
    }
}
