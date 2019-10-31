<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle;

use GpsLab\Bundle\PaginationBundle\DependencyInjection\GpsLabPaginationExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GpsLabPaginationBundle extends Bundle
{
    /**
     * @return GpsLabPaginationExtension
     */
    public function getContainerExtension()
    {
        if (!($this->extension instanceof GpsLabPaginationExtension)) {
            $this->extension = new GpsLabPaginationExtension();
        }

        return $this->extension;
    }
}
