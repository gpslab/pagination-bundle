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

class OutOfRangeException extends NotFoundHttpException
{
    /**
     * @param int $current_page
     * @param int $total_pages
     *
     * @return static
     */
    public static function out($current_page, $total_pages)
    {
        return new static(sprintf('Select page "%s" is out of range "%s".', $current_page, $total_pages));
    }
}
