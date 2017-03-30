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

class PaginationExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $template = '';

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
                [$this, 'renderPagination'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    /**
     * @param \Twig_Environment $env
     * @param Configuration     $pagination
     * @param string            $template
     * @param array             $view_params
     *
     * @return string
     */
    public function renderPagination(
        \Twig_Environment $env,
        Configuration $pagination,
        $template = null,
        array $view_params = []
    ) {
        return $env->render(
            $template ?: $this->template,
            array_merge($view_params, ['pagination' => $pagination->getView()])
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
