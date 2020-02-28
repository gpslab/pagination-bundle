Page range
==========

You can customize maximum pages in navigation menu.

## Configuration

```yaml
gpslab_pagination:
    max_navigate: 10
```

## Controller

```php
$pagination->setMaxNavigate(10);
```

## Templates

```twig
<nav class="pagination">
    {{- pagination_render(pagination, null, [], 10) -}}
</nav>
```
