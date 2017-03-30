<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\PaginationBundle\Exception;

class IncorrectPageNumberException extends \InvalidArgumentException
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
