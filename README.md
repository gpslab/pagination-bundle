[![Latest Stable Version](https://poser.pugx.org/anime-db/pagination-bundle/v/stable.png)](https://packagist.org/packages/anime-db/pagination-bundle)
[![Latest Unstable Version](https://poser.pugx.org/anime-db/pagination-bundle/v/unstable.png)](https://packagist.org/packages/anime-db/pagination-bundle)
[![Total Downloads](https://poser.pugx.org/anime-db/pagination-bundle/downloads)](https://packagist.org/packages/anime-db/pagination-bundle)
[![Build Status](https://travis-ci.org/anime-db/pagination-bundle.svg?branch=master)](https://travis-ci.org/anime-db/pagination-bundle)
[![Coverage Status](https://coveralls.io/repos/github/anime-db/pagination-bundle/badge.svg?branch=master)](https://coveralls.io/github/anime-db/pagination-bundle?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/anime-db/pagination-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/anime-db/pagination-bundle/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/47d29f1b-830d-4c11-aaa4-01031f23a8ea/mini.png)](https://insight.sensiolabs.com/projects/47d29f1b-830d-4c11-aaa4-01031f23a8ea)
[![StyleCI](https://styleci.io/repos/55138370/shield)](https://styleci.io/repos/55138370)
[![Dependency Status](https://www.versioneye.com/user/projects/5746f684ce8d0e004505f4db/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5746f684ce8d0e004505f4db)
[![License](https://poser.pugx.org/anime-db/pagination-bundle/license.png)](https://packagist.org/packages/anime-db/pagination-bundle)

# PaginationBundle

## Installation

Pretty simple with [Composer](http://packagist.org), run:

```sh
composer require anime-db/pagination-bundle
```

Add PaginatorBundle to your application kernel

```php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new AnimeDb\Bundle\PaginationBundle\AnimeDbPaginationBundle(),
        // ...
    );
}
```

### Configuration example

You can configure default templates

```yaml
anime_db_pagination:
    max_navigate: 5 # default page range used in pagination control
    template: 'AnimeDbPaginationBundle::pagination.html.twig' # sliding pagination controls template
```

## Usage

```php
namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;

class ArticleController extends Controller
{
    /**
     * @Configuration\Route("/article/", name="article_index")
     * @Configuration\Method({"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $per_page = 100; // articles per page
        $em = $this->get('doctrine.orm.entity_manager');
        $router = $this->get('router');

        // get total articles
        $total = (int)$em
            ->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('AcmeDemoBundle:Article', 'a')
            ->getQuery()
            ->getSingleScalarResult();

        // build pagination
        $pagination = $this
            ->get('pagination')
            ->paginate(
                ceil($total / $per_page), // total pages
                $request->query->get('page') // correct page
            )
            ->setPageLink(function($page) use ($router) { // build page link
                return $router->generate('article_index', ['page' => $page]);
            })
            ->setFirstPageLink($router->generate('article_index')); // build link for first page

        // get articles chunk
        $articles = $em
            ->createQueryBuilder()
            ->select('*')
            ->from('AcmeDemoBundle:Article', 'a')
            ->setFirstResult(($pagination->getCurrentPage() - 1) * $per_page)
            ->setMaxResults($per_page)
            ->getQuery()
            ->getResult();

        // template parameters
        return $this->render('AcmeDemoBundle:Article:index.html.twig', [
            'total' => $total,
            'articles' => $articles,
            'pagination' => $pagination
        ]);
    }
}
```

### From QueryBuilder

```php
namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;
use Acme\DemoBundle\Entity\Article;

class ArticleController extends Controller
{
    /**
     * @var int
     */
    const PER_PAGE = 100;

    /**
     * @Configuration\Route("/article/", name="article_index")
     * @Configuration\Method({"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        // create get articles query
        // would be better move this query to repository class
        $query = $this
            ->getDoctrine()
            ->getRepository('AcmeDemoBundle:Article')
            ->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', Article::STATUS_ENABLED);

        // build pagination
        $pagination = $this
            ->get('pagination')
            ->paginateQuery(
                $query, // query
                self::PER_PAGE, // articles per page
                $request->query->get('page') // correct page
            )
            ->setPageLink(function($page) { // build page link
                return $this->generateUrl('article_index', ['page' => $page]);
            })
            ->setFirstPageLink($this->generateUrl('article_index')); // build link for first page

        // template parameters
        return $this->render('AcmeDemoBundle:Article:index.html.twig', [
            'total' => $pagination->getTotalPages(), // total pages
            'articles' => $query->getQuery()->getResult(), // get articles chunk
            'pagination' => $pagination
        ]);
    }
}
```

### View

```twig
{# total items #}
<div class="total">
    {{ total }}
</div>

{# list articles #}
<table>
    {% for article in articles %}
        <tr{% if loop.index is odd %} class="color"{% endif %}>
            <td>{{ article.id }}</td>
            <td>{{ article.title }}</td>
            <td>{{ article.date|date('Y-m-d, H:i:s') }}</td>
        </tr>
    {% endfor %}
</table>

{# display navigation #}
<div class="navigation">
    {{ pagination_render(pagination) }}
</div>
```

### Custom view

```twig
{# display navigation #}
{{ pagination_render(pagination, 'custom_pagination.html.twig', {custom_var: 'foo'}) }}
```

Example Material Design template for pagination

```twig
{# custom_pagination.html.twig #}

{# print 'foo' #}
{{ custom_var }}

{% if pagination.total > 1 %}
{% spaceless %}
    <ul class="pagination">
        {% if pagination.prev %}
            <li>
                <a href="{{ pagination.prev.link }}" title="{{ 'previous.page'|trans }}">
                    <i class="material-icons">chevron_left</i>
                </a>
            </li>
        {% else %}
            <li class="disabled">
                <a href="#">
                    <i class="material-icons">chevron_left</i>
                </a>
            </li>
        {% endif %}
        {% for item in pagination %}
            {% if item.current %}
                <li class="active">
                    <a href="#">{{ item.page }}</a>
                </li>
            {% else %}
                <li>
                    <a href="{{ item.link }}" title="{{ 'page.number'|trans({'%page%': item.page}) }}">{{ item.page }}</a>
                </li>
            {% endif %}
        {% endfor %}
        {% if pagination.next %}
            <li>
                <a href="{{ pagination.next.link }}" title="{{ 'next.page'|trans }}">
                    <i class="material-icons">chevron_right</i>
                </a>
            </li>
        {% else %}
            <li class="disabled">
                <a href="#">
                    <i class="material-icons">chevron_right</i>
                </a>
            </li>
        {% endif %}
    </ul>
{% endspaceless %}
{% endif %}
```

## License

This bundle is under the [MIT license](http://opensource.org/licenses/MIT). See the complete license in the file: LICENSE
