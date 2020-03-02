[![Latest Stable Version](https://img.shields.io/packagist/v/gpslab/pagination-bundle.svg?maxAge=3600&label=stable)](https://packagist.org/packages/gpslab/pagination-bundle)
[![PHP from Travis config](https://img.shields.io/travis/php-v/gpslab/pagination-bundle.svg?maxAge=3600)](https://packagist.org/packages/gpslab/pagination-bundle)
[![Build Status](https://img.shields.io/travis/gpslab/pagination-bundle.svg?maxAge=3600)](https://travis-ci.org/gpslab/pagination-bundle)
[![Coverage Status](https://img.shields.io/coveralls/gpslab/pagination-bundle.svg?maxAge=3600)](https://coveralls.io/github/gpslab/pagination-bundle?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/gpslab/pagination-bundle.svg?maxAge=3600)](https://scrutinizer-ci.com/g/gpslab/pagination-bundle/?branch=master)
[![StyleCI](https://styleci.io/repos/86694387/shield?branch=master)](https://styleci.io/repos/86694387)
[![License](https://img.shields.io/packagist/l/gpslab/pagination-bundle.svg?maxAge=3600)](https://github.com/gpslab/pagination-bundle)

# PaginationBundle

![Pagination page 1](docs/pagination_page_1.png)

![Pagination page 4](docs/pagination_page_5.png)

![Pagination page 9](docs/pagination_page_9.png)

## Installation

Pretty simple with [Composer](http://packagist.org), run:

```sh
composer req gpslab/pagination-bundle
```

## Configuration

Default configuration

```yaml
gpslab_pagination:
    # Page range used in pagination control
    max_navigate: 5

    # Name of URL parameter for page number
    parameter_name: 'page'

    # Sliding pagination controls template
    template: 'GpsLabPaginationBundle::pagination.html.twig'
```

## Simple usage

```php
use GpsLab\Bundle\PaginationBundle\Service\Configuration;

class ArticleController extends Controller
{
    private const ARTICLES_PER_PAGE = 30;

    public function index(ArticleRepository $rep, Configuration $pagination): Response
    {
        $pagination->setTotalPages(ceil($rep->getTotalPublished() / self::ARTICLES_PER_PAGE));

        // get articles chunk
        $offset = ($pagination->getCurrentPage() - 1) * self::ARTICLES_PER_PAGE;
        $articles = $rep->getPublished(self::ARTICLES_PER_PAGE, $offset);

        return $this->render('AcmeDemoBundle:Article:index.html.twig', [
            'articles' => $articles,
            'pagination' => $pagination
        ]);
    }
}
```

Display pagination in template:

```twig
<nav class="pagination">
    {{- pagination_render(pagination) -}}
</nav>
```

## Documentation

 * [Base usage](docs/base_usage.md)
 * [From QueryBuilder](docs/from_qb.md)
 * [From HTTP request](docs/from_request.md)
 * [From HTTP request and QueryBuilder](docs/from_request_and_qb.md)
 * [Request parameter name](docs/parameter_name.md)
 * [Navigation pages range](docs/page_range.md)
 * [Custom view](docs/custom_view.md)

## License

This bundle is under the [MIT license](http://opensource.org/licenses/MIT). See the complete license in the file: LICENSE
