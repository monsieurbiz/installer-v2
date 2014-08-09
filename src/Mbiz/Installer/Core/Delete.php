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
use Mbiz\Installer\Helper as InstallationHelper;

class Delete{

    protected function execute()
    {
        $_installationHelper = new InstallationHelper();
        do {
            $response = strtolower($_installationHelper->prompt('Are you sure you want to delete the module ' . red() . $_installationHelper->getModuleName() . white() . '? (yes/no)'));
        } while (!in_array($response, array('yes', 'no')));



        if ($response === 'yes') {
            $_installationHelper->_rmdir($_installationHelper->getModuleDir());
            @unlink($_installationHelper->getAppDir() . 'etc/modules/' . $_installationHelper->getModuleName() . '.xml');
            $_installationHelper->_rmdir($_installationHelper->getDesignDir('frontend') . strtolower($_installationHelper->getModuleName()));
            $_installationHelper->_rmdir($_installationHelper->getDesignDir('adminhtml') . strtolower($_installationHelper->getModuleName()));
            foreach ($_installationHelper->getLocales() as $locale) {
                @unlink($_installationHelper->getAppDir() . 'locale/' . $locale . '/' . $_installationHelper->getModuleName() . '.csv');
            }
            $this->_namespace = null;
            $this->_module = null;
            $this->_pool = null;
            $this->_mageConfig = null;
        }
    }
}