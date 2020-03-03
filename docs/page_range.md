Navigation pages range
======================

You can customize maximum pages in navigation menu.

Configuration
-------------

```yaml
gpslab_pagination:
    max_navigate: 10
```

Templates
---------

```twig
<nav class="pagination">
    {{- pagination_render(pagination, null, [], 10) -}}
</nav>
```

Controller
----------

```php
$pagination->setMaxNavigate(10);
```

Annotations
-----------

```php
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @ParamConverter("pagination", options={"max_navigate": 10})
 */
public function index(Configuration $pagination): Response
{
    // ...
}
```
