<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\PaginationBundle\Service;

use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    private $max_navigate = Configuration::DEFAULT_LIST_LENGTH;

    /**
     * @param Router $router       Router service
     * @param int    $max_navigate Maximum showing navigation links in pagination
     */
    public function __construct(Router $router, $max_navigate)
    {
        $this->router = $router;
        $this->max_navigate = $max_navigate;
    }

    /**
     * @param int $total_pages  Total available pages
     * @param int $current_page The current page number
     *
     * @return Configuration
     */
    public function paginate($total_pages = 0, $current_page = 1)
    {
        return (new Configuration($total_pages, $current_page))
            ->setMaxNavigate($this->max_navigate);
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

        $query
            ->setFirstResult(($current_page - 1) * $per_page)
            ->setMaxResults($per_page)
        ;

        return (new Configuration(ceil($total / $per_page), $current_page))
            ->setMaxNavigate($this->max_navigate)
        ;
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
        $parameter_name = 'page',
        $reference_type = UrlGeneratorInterface::ABSOLUTE_PATH
    ) {
        $current_page = $request->get($parameter_name);

        if (is_null($current_page)) {
            $current_page = 1;
        } elseif (!is_numeric($current_page) || $current_page < 1 || $current_page > $total_pages) {
            throw new NotFoundHttpException(sprintf('Incorrect "%s" page number.', $current_page));
        } else {
            $current_page = (int)$current_page;
        }

        return $this
            ->paginate($total_pages, $current_page)
            ->setPageLink(function ($number) use ($request, $reference_type) {
                return $this->router->generate(
                    $request->get('_route'),
                    array_merge($request->get('_route_params'), ['page' => $number]),
                    $reference_type
                );
            })
            ->setFirstPageLink($this->router->generate(
                $request->get('_route'),
                $request->get('_route_params'),
                $reference_type
            ))
        ;
    }
}
