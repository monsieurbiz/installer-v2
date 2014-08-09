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

namespace Mbiz\Installer\Config\Layout;

use Mbiz\Installer\Command\Command as BaseCommand;

class Layout{

    public function execute(array $params)
    {
        if (!empty($params) && in_array($params[0], array('admin', 'front'))) {
            $where = $params[0];
        } else {
            do {
                $where = $this->prompt('Where? (enter for front)');
                if (empty($where)) {
                    $where = 'front';
                }
            } while (!in_array($where, array('admin', 'front')));
        }

        if ($where == 'admin') {
            $where = 'adminhtml';
        } else {
            $where = 'frontend';
        }

        $config = $this->getConfig();

        if (!isset($config->{$where})) {
            $config->addChild($where);
        }

        if (!isset($config->{$where}->layout)) {
            $file = strtolower($this->getModuleName()) . '.xml';
            $child = $config->{$where}
                ->addChild('layout')
                ->addChild('updates')
                ->addChild(strtolower($this->getModuleName()));
            $child->addAttribute('module', $this->getModuleName());
            $child->addChild('file', $file);
            $this->writeConfig();
            $dir = $this->getAppDir() . 'design/' . $where . '/';

            if ($this->_pool == 'community') {
                $dirs = array('base', 'default');
            } else {
                $dirs = explode('_', self::$_config->design);
            }

            foreach ($dirs as $d) {
                if (!is_dir($dir = $dir . $d . '/')) {
                    mkdir($dir);
                }
            }

            $dirs = array('layout', 'etc', 'template');

            foreach ($dirs as $d) {
                if (!is_dir($dd = $dir . $d . '/')) {
                    mkdir($dd);
                }
            }

            if (!file_exists($dir . 'layout/' . $file)) {
                file_put_contents($dir . 'layout/' . $file, $this->getTemplate('layout_xml'));
            }
        }

        $this->_processReloadConfig();

        $this->setLast(__FUNCTION__);
    }
}