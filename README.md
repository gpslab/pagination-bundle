[![Latest Stable Version](https://img.shields.io/packagist/v/gpslab/pagination-bundle.svg?maxAge=3600&label=stable)](https://packagist.org/packages/gpslab/pagination-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/gpslab/pagination-bundle.svg?maxAge=3600)](https://packagist.org/packages/gpslab/pagination-bundle)
[![Build Status](https://img.shields.io/travis/gpslab/pagination-bundle.svg?maxAge=3600)](https://travis-ci.org/gpslab/pagination-bundle)
[![Coverage Status](https://img.shields.io/coveralls/gpslab/pagination-bundle.svg?maxAge=3600)](https://coveralls.io/github/gpslab/pagination-bundle?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/gpslab/pagination-bundle.svg?maxAge=3600)](https://scrutinizer-ci.com/g/gpslab/pagination-bundle/?branch=master)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/6e0b6018-9a7e-4f25-9960-b27f6807b6d7.svg?maxAge=3600&label=SLInsight)](https://insight.sensiolabs.com/projects/6e0b6018-9a7e-4f25-9960-b27f6807b6d7)
[![StyleCI](https://styleci.io/repos/86694387/shield?branch=master)](https://styleci.io/repos/86694387)
[![License](https://img.shields.io/packagist/l/gpslab/pagination-bundle.svg?maxAge=3600)](https://github.com/gpslab/pagination-bundle)

# PaginationBundle

![Pagination page 1](pagination_1.png)

![Pagination page 4](pagination_5.png)

![Pagination page 9](pagination_9.png)

## Installation

Pretty simple with [Composer](http://packagist.org), run:

```sh
composer require gpslab/pagination-bundle
```

Add PaginatorBundle to your application kernel

```php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new GpsLab\Bundle\PaginationBundle\GpsLabPaginationBundle(),
        // ...
    );
}
```

## Simplae usage

```php
class ArticleController extends Controller
{
    const PER_PAGE = 30; // articles per page

    public function indexAction(Request $request)
    {
        $rep = $this->getDoctrine()->getRepository('AcmeDemoBundle:Article');

        $total = $rep->getTotalPublished();
        $total_pages = ceil($total / self::PER_PAGE);

        // build pagination
        $pagination = $this->get('pagination')->paginateRequest($request, $total_pages);

        // get articles chunk
        $articles = $rep->getPublished(
            self::PER_PAGE, // limit
            ($pagination->getCurrentPage() - 1) * self::PER_PAGE // offset
        );

        return $this->render('AcmeDemoBundle:Article:index.html.twig', [
            'total' => $total,
            'articles' => $articles,
            'pagination' => $pagination
        ]);
    }
}
```

Display pagination in template:

```twig
<nav class="pagination">
    {{ pagination_render(pagination) }}
</nav>
```


## Documentation

 * [Installation](docs/installation.md)
 * [Configuration](docs/configuration.md)
 * Usage
    * [Base usage](docs/usage/base.md)
    * [From QueryBuilder](docs/usage/query.md)
    * [From HTTP request](docs/usage/request.md)
    * [From HTTP request and QueryBuilder](docs/usage/request_query.md)
 * [Custom view](docs/custom_view.md)

## License

This bundle is under the [MIT license](http://opensource.org/licenses/MIT). See the complete license in the file: LICENSE
