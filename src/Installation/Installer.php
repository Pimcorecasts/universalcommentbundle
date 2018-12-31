<?php

namespace MercuryKojo\Bundle\UniversalCommentBundle;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Migrations\Version;
use Pimcore\Extension\Bundle\Installer\MigrationInstaller;
use Pimcore\Model\DataObject;
use Pimcore\Tool\Console;

class Installer extends MigrationInstaller
{
    public function getMigrationVersion(): string
    {
        return '0';
    }

    /**
     * {@inheritdoc}
     */
    public function migrateInstall(Schema $schema, Version $version)
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

    protected function beforeUninstallMigration()
    {
        $this->migrateToVersion('0');
        $this->outputWriter->write(PHP_EOL);

        // or manually revert a single migration - the second parameter defines the migration as being migrated down
        // $this->executeMigration('20170822151849', false);
    }

    public function migrateUninstall(Schema $schema, Version $version)
    {
    }

}