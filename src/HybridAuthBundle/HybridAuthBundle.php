<?php

namespace HybridAuthBundle;

use HybridAuthBundle\DependencyInjection\HybridAuthExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/3/17
 * Time: 6:37 PM.
 */
class HybridAuthBundle extends Bundle
{
    /**
     * Overridden to allow for the custom extension alias.
     *
     * @return HybridAuthExtension
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            return new HybridAuthExtension();
        }

        return $this->extension;
    }
}
