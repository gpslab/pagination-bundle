Request parameter name
======================

As default used `page` as request parameter name. So for first page will be generated `/` link, for second `/?page=2`,
for third `/?page=3` and etc. You can change this parameter name.

Configuration
-------------

```yaml
gpslab_pagination:
    parameter_name: 'p'
```

Controller
----------

```php
$pagination = new Configuration();
$pagination->setFirstPageLink('/');
$pagination->setPageLink('/?p=%d');
// or you can use callback function
//$pagination->setPageLink(static function (int $number): string {
//    return sprintf('/?p=%d', $number);
//});
```

Annotations
-----------

```php
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @ParamConverter("pagination", options={"parameter_name": "p"})
 */
public function index(Configuration $pagination): Response
{
    // ...
}
```