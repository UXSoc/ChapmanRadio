<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle;

use CoreBundle\DependencyInjection\CoreExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CoreBundle extends Bundle
{
    /**
     * Overridden to allow for the custom extension alias.
     *
     * @return CoreExtension
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            return new CoreExtension();
        }

        return $this->extension;
    }
}
