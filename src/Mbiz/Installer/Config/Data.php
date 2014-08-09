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

namespace Mbiz\Installer\Config;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Helper as InstallationHelper;

class Data{

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $_installationHelper = new InstallationHelper();

        list($dir, $created) = $_installationHelper->getModuleDir('data', true);

        $config = $_installationHelper->getConfig();
        if (!isset($config->global)) {
            $config->addChild('global');
        }
        $global = $config->global;
        if (!$global->resources || !$global->resources->{strtolower($_installationHelper->getModuleName()) . '_setup'}) {
            if (!$resources = $global->resources) {
                $resources = $global->addChild('resources');
            }
            if (!$moduleSetup = $resources->{strtolower($_installationHelper->getModuleName()) . '_setup'}) {
                $moduleSetup = $resources->addChild(strtolower($_installationHelper->getModuleName()) . '_setup');
            }

            $setup = $moduleSetup->addChild('setup');
            $setup->addChild('module', $_installationHelper->getModuleName());
            $setup->addChild('class', 'Mage_Core_Model_Resource_Setup');
            $connection = $moduleSetup->addChild('connection');
            $connection->addChild('use', 'core_setup');
            $_installationHelper->writeConfig();
        }

        $dir = $dir . strtolower($_installationHelper->getModuleName()) . '_setup/';

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $version = $_installationHelper->getConfigVersion();
        if (!empty($params)) {
            if (count($params) == 1) {
                $to = array_shift($params);
                $from = $version;
            } else {
                $from = array_shift($params);
                $to = array_shift($params);
            }

            $filename = $dir . 'data-upgrade-' . $from . '-' . $to . '.php';
            if (!is_file($filename)) {
                file_put_contents($filename, $_installationHelper->getTemplate('data_file', array()));
            }

            echo 'Upgrade data from ' . red() . $from . white() . ' to ' . red() . $to . white() . ".\n";
        } else {
            $filename = $dir . 'data-install-' . $version . '.php';
            if (!is_file($filename)) {
                file_put_contents($filename, $_installationHelper->getTemplate('data_file', array()));
            }
        }

        $this->_processReloadConfig();

        $_installationHelper->setLast(__FUNCTION__);
    }
}