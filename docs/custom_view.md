Custom view
===========

You can customize presents pagination.

You can change template for all pagination on your project from config:

```yaml
gpslab_pagination:
    # sliding pagination controls template
    template: 'custom_pagination.html.twig'
```

Or you can change template for concrete pagination:

```twig
{# display navigation #}
{{ pagination_render(pagination, 'custom_pagination.html.twig', {custom_var: 'foo'}) }}
```

Example [Material Design](https://material.io/guidelines/) template for pagination:

```twig
{# custom_pagination.html.twig #}

{# print 'foo' #}
{{ custom_var }}

{% if pagination.total > 1 %}
{% spaceless %}
    <ul class="pagination">
        {% if pagination.prev %}
            <li>
                <a href="{{ pagination.prev.link }}" title="{{ 'pagination.previous_page.title'|trans }}">
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
                    <a href="#" title="{{ 'pagination.current_page.title'|trans }}">
                        {{ item.page }}
                    </a>
                </li>
            {% else %}
                <li>
                    <a href="{{ item.link }}" title="{{ 'pagination.page_number.title'|trans({'%page%': item.page}) }}">
                        {{ item.page }}
                    </a>
                </li>
            {% endif %}
        {% endfor %}
        {% if pagination.next %}
            <li>
                <a href="{{ pagination.next.link }}" title="{{ 'pagination.next_page.title'|trans }}">
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
