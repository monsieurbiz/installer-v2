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

namespace Mbiz\Installer\Config\Data;

use Mbiz\Installer\Command\Command as BaseCommand;

class Data{

    public function execute(array $params)
    {
        list($dir, $created) = $this->getModuleDir('data', true);

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

        $version = $this->getConfigVersion();
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
                file_put_contents($filename, $this->getTemplate('data_file', array()));
            }

            echo 'Upgrade data from ' . red() . $from . white() . ' to ' . red() . $to . white() . ".\n";
        } else {
            $filename = $dir . 'data-install-' . $version . '.php';
            if (!is_file($filename)) {
                file_put_contents($filename, $this->getTemplate('data_file', array()));
            }
        }

        $this->_processReloadConfig();

        $this->setLast(__FUNCTION__);
    }
}