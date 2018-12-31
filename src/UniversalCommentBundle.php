<?php
/**
 * Created by PhpStorm.
 * User: mercury
 * Date: 31.12.18
 * Time: 11:06
 */

namespace MercuryKojo\Bundle\UniversalCommentBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class UniversalCommentBundle extends AbstractPimcoreBundle
{
    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }


    public function getVersion()
    {
        return '0.0.1b';
    }

    public function getDescription()
    {
        return 'Universal Comment Bundle providing Templates, Objectstructures and Templating Helper';
    }

}