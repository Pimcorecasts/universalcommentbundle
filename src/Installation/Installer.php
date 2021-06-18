<?php

namespace MercuryKojo\Bundle\UniversalCommentBundle\Installation;

use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Model\DataObject;
use Pimcore\Tool\Console;

class Installer extends AbstractInstaller
{

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        if (!DataObject\ClassDefinition::getByName('UniversalComment')) {
            Console::exec('php ' . PIMCORE_PROJECT_ROOT . '/bin/console pimcore:definition:import:class ' . PIMCORE_PROJECT_ROOT . '/vendor/mercurykojo/pimcore-markdown-bundle/src/Installation/Definitions/class_UniversalComment_export.json -f');
        }
    }

    /**
     * @return bool
     */
    public function needsReloadAfterInstall()
    {
        return true;
    }

    public function isInstalled()
    {
        if(!DataObject\ClassDefinition::getByName('UniversalComment')) {
            return false;
        }

        return true;
    }

    public function canBeInstalled()
    {
        return !$this->isInstalled();
    }

    public function uninstall()
    {
    }

}
