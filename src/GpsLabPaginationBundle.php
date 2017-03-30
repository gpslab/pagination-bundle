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
     * @return ExtensionInterface|null
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = false;
            $class = $this->getContainerExtensionClass();

            if (class_exists($class)) {
                $extension = new $class();

                if ($extension instanceof ExtensionInterface) {
                    $this->extension = $extension;
                }
            }

        }

        return $this->extension ?: null;
    }
}
