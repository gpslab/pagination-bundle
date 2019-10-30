<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IncorrectPageNumberException extends NotFoundHttpException
{
    /**
     * @param mixed $current_page
     *
     * @return static
     */
    public static function incorrect($current_page)
    {
        return new static(sprintf('Incorrect "%s" page number.', $current_page));
    }
}
