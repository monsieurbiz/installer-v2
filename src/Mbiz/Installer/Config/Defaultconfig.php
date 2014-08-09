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

namespace Mbiz\Installer\Config\Defaultconfig;

use Mbiz\Installer\Command\Command as BaseCommand;

class Defaultconfig{

    protected function _process(array $params)
    {
        if (empty($params)) {
            do {
                $name = $this->prompt("Name?");
            } while (empty($name));
        } else {
            $name = array_shift($params);
        }

        if (!count($params)) {
            do {
                $value = $this->prompt("Value?");
            } while ($value === '');
        } else {
            $value = array_shift($params);
        }

        // conf
        /* @var $config SimpleXMLElement */
        $config = $this->getConfig();
        if (!isset($config->default)) {
            $config->addChild('default');
        }
        $config = $config->default;

        $names = explode('/', strtolower($name));

        foreach ($names as $name) {
            if (!$config->$name) {
                $config->addChild($name);
            }
            $config = $config->$name;
        }

        // Adding text as cdata
        $node = dom_import_simplexml($config[0]);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($value));

        $this->writeConfig();
    }
}