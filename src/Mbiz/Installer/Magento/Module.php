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

namespace Mbiz\Installer\Magento;

class Module
{
    /**
     * Module's vendor
     * @var string
     */
    protected $_vendor;

    /**
     * Module's name
     * @var string
     */
    protected $_module;

    /**
     * Module's pool
     * @var string (local or community)
     */
    protected $_pool;

    /**
     * Configuration
     * @var
     */
    protected $_config;

    /**
     * Reinit (or init..) a new module
     * @param string $vendor The module's vendor name
     * @param string $module The module's name
     * @param string $pool The module's pool (local or community)
     * @return \Mbiz\Installer\Magento\Module
     */
    public function reinit($vendor, $module, $pool)
    {
        // Reinit the config
        $this->_config = null;

        // Set info
        $this->_vendor = $vendor;
        $this->_module = $module;
        $this->_pool = $pool;

        return $this;
    }

}
