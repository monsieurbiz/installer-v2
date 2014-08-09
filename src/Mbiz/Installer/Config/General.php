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

namespace Mbiz\Installer\Config\General;

use Mbiz\Installer\Command\Command as BaseCommand;

class General{

    public function execute(array $params)
    {
        // If no config file
        if (false === $local = $this->_getLocalXml()) {
            return;
        }

        $width = 80;

        // General
        echo red() . "General Configuration\n";
        echo red() . str_repeat('-', $width) . "\n";

        // Database
        if (
            $local->global
            && $local->global->resources
            && $local->global->resources->default_setup
            && $local->global->resources->default_setup->connection
        ) {
            echo green() . "Database\n";
            $c = $local->global->resources->default_setup->connection;
            echo yellow() . 'Host' . white() . ' : ' . trim($c->host) . "\n";
            echo yellow() . 'User' . white() . ' : ' . trim($c->username) . "\n";
            echo yellow() . 'Pass' . white() . ' : ' . trim($c->password) . "\n";
            echo yellow() . 'Name' . white() . ' : ' . trim($c->dbname) . "\n";
        }

        echo "\n";
    }
}