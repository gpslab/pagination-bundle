[![Latest Stable Version](https://poser.pugx.org/anime-db/pagination-bundle/v/stable.png)](https://packagist.org/packages/anime-db/pagination-bundle)
[![Latest Unstable Version](https://poser.pugx.org/anime-db/pagination-bundle/v/unstable.png)](https://packagist.org/packages/anime-db/pagination-bundle)
[![Total Downloads](https://poser.pugx.org/anime-db/pagination-bundle/downloads)](https://packagist.org/packages/anime-db/pagination-bundle)
[![Build Status](https://travis-ci.org/anime-db/pagination-bundle.svg?branch=master)](https://travis-ci.org/anime-db/pagination-bundle)
[![Code Coverage](https://scrutinizer-ci.com/g/anime-db/pagination-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/anime-db/pagination-bundle/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/anime-db/pagination-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/anime-db/pagination-bundle/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/47d29f1b-830d-4c11-aaa4-01031f23a8ea/mini.png)](https://insight.sensiolabs.com/projects/47d29f1b-830d-4c11-aaa4-01031f23a8ea)
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

## Usage

```php
// Acme\DemoBundle\Controller\ArticleController.php

public function listAction($page)
{
    $em = $this->get('doctrine.orm.entity_manager');
    $router = $this->get('router');

    $total = (int)$em->createQueryBuilder()
        ->select('COUNT(*)')
        ->from('AcmeDemoBundle:Article', 'a')
        ->getQuery()
        ->getSingleScalarResult();

    $pagination = $this->get('pagination')
        ->paginate($total, $page)
        ->setPageLink(function($page) use ($router) {
            return $router->generate('article_list', ['page' => $page]);
        })
        ->setFirstPageLink(function() use ($router) {
            return $router->generate('article_list');
        });

    $per_page = 100; // articles per page
    $articles = $em->createQueryBuilder()
        ->select('*')
        ->from('AcmeDemoBundle:Article', 'a')
        ->setFirstResult(($pagination->getCurrentPage() - 1) * $per_page)
        ->setMaxResults($per_page)
        ->getQuery()
        ->getResult();

    // parameters to template
    return $this->render('AcmeDemoBundle:Article:list.html.twig', [
        'total' => $total,
        'articles' => $articles,
        'pagination' => $pagination
    ]);
}
```


### View

```twig
{# total items count #}
<div class="count">
    {{ total }}
</div>
<table>

{# table body #}
{% for article in articles %}
<tr {% if loop.index is odd %}class="color"{% endif %}>
    <td>{{ article.id }}</td>
    <td>{{ article.title }}</td>
    <td>{{ article.date | date('Y-m-d') }}, {{ article.time | date('H:i:s') }}</td>
</tr>
{% endfor %}
</table>

{# display navigation #}
<div class="navigation">
    {{ pagination(pagination) }}
</div>
