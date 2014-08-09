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

namespace Mbiz\Installer\Core\Delete;

use Mbiz\Installer\Command\Command as BaseCommand;

class Delete{

    protected function execute()
    {
        do {
            $response = strtolower($this->prompt('Are you sure you want to delete the module ' . red() . $this->getModuleName() . white() . '? (yes/no)'));
        } while (!in_array($response, array('yes', 'no')));

        if ($response === 'yes') {
            $this->_rmdir($this->getModuleDir());
            @unlink($this->getAppDir() . 'etc/modules/' . $this->getModuleName() . '.xml');
            $this->_rmdir($this->getDesignDir('frontend') . strtolower($this->getModuleName()));
            $this->_rmdir($this->getDesignDir('adminhtml') . strtolower($this->getModuleName()));
            foreach ($this->getLocales() as $locale) {
                @unlink($this->getAppDir() . 'locale/' . $locale . '/' . $this->getModuleName() . '.csv');
            }
            $this->_namespace = null;
            $this->_module = null;
            $this->_pool = null;
            $this->_mageConfig = null;
        }
    }
}