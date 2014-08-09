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

namespace Mbiz\Installer\Core\Module;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Helper as InstallationHelper;

class Module{

    protected function execute(array $params = array(), $force = false)
    {
        if ($force || !$this->_namespace) {
            // Namespace
            if (isset($params[0])) {
                $this->_namespace = ucfirst($params[0]);
            } else {
                $this->_namespace = ucfirst($this->prompt("Namespace? (enter for " . self::$_config->company_name_short . ")"));
                if (empty($this->_namespace)) {
                    $this->_namespace = self::$_config->company_name_short;
                }
            }
            // Module
            if (isset($params[1])) {
                $this->_module = ucfirst($params[1]);
            } else {
                do {
                    $this->_module = ucfirst($this->prompt("Module?"));
                } while (empty($this->_module));
            }
            // Pool
            if (isset($params[2]) && in_array($params[2], array('local', 'community'))) {
                $this->_pool = $params[2];
            } else {
                $this->_pool = strtolower($this->prompt("Pool? (enter for local)"));
                if (empty($this->_pool)) {
                    $this->_pool = 'local';
                }
            }

            $filename = $this->getModuleDir('etc') . 'config.xml';
            if (!is_file($filename)) {
                file_put_contents($filename, $this->getTemplate('config_xml'));
                file_put_contents($filename, $this->getTemplate('config_xml'));
                file_put_contents(
                    $this->getAppDir() . 'etc/modules/' . $this->getModuleName() . '.xml',
                    $this->getTemplate(
                        'module_xml',
                        array('{pool}' => $this->_pool)
                    )
                );
            }

            $this->_mageConfig = null;

            echo red() . "Using: " . white() . $this->getModuleName() . ' in ' . $this->_pool . "\n";
        }

        $_installationHelper = new InstallationHelper();
        $_installationHelper->setLast();
    }
}