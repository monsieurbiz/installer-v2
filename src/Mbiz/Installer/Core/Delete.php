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

namespace Mbiz\Installer\Core;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Helper as InstallerHelper;

class Delete{

    protected function execute()
    {
        $_installerHelper = new InstallerHelper();
        do {
            $response = strtolower($_installerHelper->prompt('Are you sure you want to delete the module ' . red() . $_installerHelper->getModuleName() . white() . '? (yes/no)'));
        } while (!in_array($response, array('yes', 'no')));



        if ($response === 'yes') {
            $_installerHelper->_rmdir($_installerHelper->getModuleDir());
            @unlink($_installerHelper->getAppDir() . 'etc/modules/' . $_installerHelper->getModuleName() . '.xml');
            $_installerHelper->_rmdir($_installerHelper->getDesignDir('frontend') . strtolower($_installerHelper->getModuleName()));
            $_installerHelper->_rmdir($_installerHelper->getDesignDir('adminhtml') . strtolower($_installerHelper->getModuleName()));
            foreach ($_installerHelper->getLocales() as $locale) {
                @unlink($_installerHelper->getAppDir() . 'locale/' . $locale . '/' . $_installerHelper->getModuleName() . '.csv');
            }
            $this->_namespace = null;
            $this->_module = null;
            $this->_pool = null;
            $this->_mageConfig = null;
        }
    }
}