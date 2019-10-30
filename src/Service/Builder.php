<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Service;

use Doctrine\ORM\QueryBuilder;
use GpsLab\Bundle\PaginationBundle\Exception\IncorrectPageNumberException;
use GpsLab\Bundle\PaginationBundle\Exception\OutOfRangeException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Builder
{
    /**
     * @var Router
     */
    private $router;

    /**
     * The number of pages displayed in the navigation.
     *
     * @var int
     */
    private $max_navigate;

    /**
     * Name of URL parameter for page number.
     *
     * @var string
     */
    private $parameter_name;

    /**
     * @param Router $router         Router service
     * @param int    $max_navigate   Maximum showing navigation links in pagination
     * @param string $parameter_name Name of URL parameter for page number
     */
    public function __construct(Router $router, $max_navigate, $parameter_name)
    {
        $this->router = $router;
        $this->max_navigate = $max_navigate;
        $this->parameter_name = $parameter_name;
    }

    /**
     * @param int $total_pages  Total available pages
     * @param int $current_page The current page number
     *
     * @return Configuration
     */
    public function paginate($total_pages = 1, $current_page = 1)
    {
        return (new Configuration($total_pages, $current_page))
            ->setMaxNavigate($this->max_navigate)
            ->setPageLink(sprintf('?%s=%%d', $this->parameter_name))
        ;
    }

    /**
     * @param QueryBuilder $query        Query for select entities
     * @param int          $per_page     Entities per page
     * @param int          $current_page The current page number
     *
     * @return Configuration
     */
    public function paginateQuery(QueryBuilder $query, $per_page, $current_page = 1)
    {
        $counter = clone $query;
        $total = $counter
            ->select(sprintf('COUNT(%s)', current($query->getRootAliases())))
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $total_pages = (int) ceil($total / $per_page);
        $current_page = $this->validateCurrentPage($current_page, $total_pages);

        $query
            ->setFirstResult(($current_page - 1) * $per_page)
            ->setMaxResults($per_page)
        ;

        return $this->paginate($total_pages, $current_page);
    }

    /**
     * @param Request $request        Current HTTP request
     * @param int     $total_pages    Total available pages
     * @param string  $parameter_name Name of URL parameter for page number
     * @param int     $reference_type The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return Configuration
     */
    public function paginateRequest(
        Request $request,
        $total_pages,
        $parameter_name = '',
        $reference_type = UrlGeneratorInterface::ABSOLUTE_PATH
    ) {
        $parameter_name = $parameter_name ?: $this->parameter_name;
        $current_page = $this->validateCurrentPage($request->get($parameter_name), $total_pages);

        return $this->configureFromRequest(
            $request,
            $this->paginate($total_pages, $current_page),
            $parameter_name,
            $reference_type
        );
    }

    /**
     * @param Request      $request        Current HTTP request
     * @param QueryBuilder $query          Query for select entities
     * @param int          $per_page       Entities per page
     * @param string       $parameter_name Name of URL parameter for page number
     * @param int          $reference_type The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return Configuration
     */
    public function paginateRequestQuery(
        Request $request,
        QueryBuilder $query,
        $per_page,
        $parameter_name = 'page',
        $reference_type = UrlGeneratorInterface::ABSOLUTE_PATH
    ) {
        $parameter_name = $parameter_name ?: $this->parameter_name;

        return $this->configureFromRequest(
            $request,
            $this->paginateQuery($query, $per_page, $request->get($parameter_name)),
            $parameter_name,
            $reference_type
        );
    }

    /**
     * @param mixed $current_page
     * @param int   $total_pages
     *
     * @return int
     */
    private function validateCurrentPage($current_page, $total_pages)
    {
        if ($current_page === null) {
            return 1;
        }

        if (!is_int($current_page) && (!is_string($current_page) || !ctype_digit($current_page))) {
            throw IncorrectPageNumberException::incorrect($current_page);
        }

        if ($current_page < 1 || $current_page > $total_pages) {
            throw OutOfRangeException::out((int) $current_page, $total_pages);
        }

        return (int) $current_page;
    }

    /**
     * @param Request       $request
     * @param Configuration $configuration
     * @param string        $parameter_name
     * @param int           $reference_type
     *
     * @return Configuration
     */
    private function configureFromRequest(
        Request $request,
        Configuration $configuration,
        $parameter_name,
        $reference_type
    ) {
        $route = $request->get('_route');
        $route_params = array_merge($request->query->all(), $request->get('_route_params', []));
        unset($route_params[$parameter_name]);

        return $configuration
            ->setPageLink(function ($number) use ($route, $route_params, $parameter_name, $reference_type) {
                $params = array_merge($route_params, [$parameter_name => $number]);

                return $this->router->generate($route, $params, $reference_type);
            })
            ->setFirstPageLink($this->router->generate($route, $route_params, $reference_type))
        ;
    }
}
