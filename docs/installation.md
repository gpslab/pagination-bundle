Installation
============

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
