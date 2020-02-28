<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\ParamConverter;

use GpsLab\Bundle\PaginationBundle\Service\Configuration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class PaginationParamConverter implements ParamConverterInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var int
     */
    private $max_navigate;

    /**
     * @var string
     */
    private $parameter_name;

    /**
     * @param RouterInterface $router
     * @param int             $max_navigate
     * @param string          $parameter_name
     */
    public function __construct(RouterInterface $router, $max_navigate, $parameter_name)
    {
        $this->router = $router;
        $this->max_navigate = $max_navigate;
        $this->parameter_name = $parameter_name;
    }

    /**
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        return 'GpsLab\Bundle\PaginationBundle\Service\Configuration' === $configuration->getClass();
    }

    /**
     * @param Request        $request
     * @param ParamConverter $converter
     *
     * @return bool
     */
    public function apply(Request $request, ParamConverter $converter)
    {
        $options = $converter->getOptions();
        $max_navigate = $this->max_navigate;
        $param_name = $this->parameter_name;
        $reference_type = UrlGeneratorInterface::ABSOLUTE_PATH;
        $reference_types = [
            'absolute_url' => UrlGeneratorInterface::ABSOLUTE_PATH,
            'absolute_path' => UrlGeneratorInterface::ABSOLUTE_PATH,
            'relative_path' => UrlGeneratorInterface::RELATIVE_PATH,
            'network_path' => UrlGeneratorInterface::NETWORK_PATH,
        ];

        if (isset($options['max_navigate'])) {
            $max_navigate = $options['max_navigate'];
        }

        if (isset($options['parameter_name'])) {
            $param_name = $options['parameter_name'];
        }

        if (isset($options['reference_type']) &&
            array_key_exists($options['reference_type'], $reference_types)
        ) {
            $reference_type = $reference_types[$options['reference_type']];
        }

        $current_page = (int) $request->get($param_name, 1);
        $current_page = $current_page > 1 ? $current_page : 1;

        // get routing params
        $route = $request->attributes->get('_route');
        $route_params = array_merge($request->query->all(), $request->attributes->get('_route_params', []));
        unset($route_params[$param_name]);

        // impossible resolve total pages here
        $total_pages = 0;

        $configuration = new Configuration($total_pages, $current_page);
        $configuration->setMaxNavigate($max_navigate);
        $configuration->setFirstPageLink($this->router->generate($route, $route_params, $reference_type));
        $configuration->setPageLink(function ($number) use ($route, $route_params, $param_name, $reference_type) {
            return $this->router->generate($route, [$param_name => $number] + $route_params, $reference_type);
        });

        $request->attributes->set($converter->getName(), $configuration);

        return true;
    }
}
