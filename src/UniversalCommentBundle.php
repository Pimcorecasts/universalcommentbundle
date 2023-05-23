<?php
/**
 * Created by PhpStorm.
 * User: mercury
 * Date: 31.12.18
 * Time: 11:06
 */

namespace MercuryKojo\Bundle\UniversalCommentBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use MercuryKojo\Bundle\UniversalCommentBundle\Installation\Installer;

class UniversalCommentBundle extends AbstractPimcoreBundle
{
    public function getInstaller(): \Pimcore\Extension\Bundle\Installer\InstallerInterface|null{
        return $this->container->get(Installer::class);
    }


    public function getVersion(): string
    {
        return '0.0.1b';
    }

    public function getDescription(): string
    {
        return 'Universal Comment Bundle providing Templates, Objectstructures and Templating Helper';
    }

}
