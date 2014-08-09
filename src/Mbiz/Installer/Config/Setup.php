<?php
/**
 * This file is part of Installer version 2.
 *
 * Installer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Installer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Installer.  If not, see <http://www.gnu.org/licenses/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/gpl-3.0.txt>.
 */

namespace Mbiz\Installer\Config\Setup;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Helper as InstallationHelper;

class Setup{

    public function execute(InputInterface $input, OutputInterface $output)
    {
        list($dir, $created) = $this->getModuleDir('sql', true);

        $config = $this->getConfig();
        if (!isset($config->global)) {
            $config->addChild('global');
        }
        $global = $config->global;
        if (!$global->resources || !$global->resources->{strtolower($this->getModuleName()) . '_setup'}) {
            if (!$resources = $global->resources) {
                $resources = $global->addChild('resources');
            }
            if (!$moduleSetup = $resources->{strtolower($this->getModuleName()) . '_setup'}) {
                $moduleSetup = $resources->addChild(strtolower($this->getModuleName()) . '_setup');
            }

            $setup = $moduleSetup->addChild('setup');
            $setup->addChild('module', $this->getModuleName());
            $setup->addChild('class', 'Mage_Core_Model_Resource_Setup');
            $connection = $moduleSetup->addChild('connection');
            $connection->addChild('use', 'core_setup');
            $this->writeConfig();
        }

        $dir = $dir . strtolower($this->getModuleName()) . '_setup/';

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $setupClass = (string) $config
            ->global
            ->resources
            ->{strtolower($this->getModuleName()) . '_setup'}
            ->setup
            ->class
        ;

        $filename = $dir . 'install-' . $this->getConfigVersion() . '.php';
        if (!is_file($filename)) {
            file_put_contents($filename, $this->getTemplate('setup_class', array(
                'Mage_Core_Model_Resource_Setup' => $setupClass
            )));
        }

        $this->_processReloadConfig();

        $_installationHelper = new InstallationHelper();
        $_installationHelper->setLast(__FUNCTION__);
    }
}