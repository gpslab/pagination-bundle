<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GpsLabPaginationBundle extends Bundle
{
    /**
     * @return ExtensionInterface|bool
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $extension = $this->createContainerExtension();

            if ($extension instanceof ExtensionInterface) {
                $this->extension = $extension;
            } else {
                $this->extension = false;
            }
        }

        return $this->extension;
    }
}
