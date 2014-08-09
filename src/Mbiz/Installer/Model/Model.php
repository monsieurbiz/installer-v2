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

namespace Mbiz\Installer\Routers\Model;

use Mbiz\Installer\Command\Command as BaseCommand;


class Model extends BaseCommand
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (empty($params)) {
            do {
                $name = ucfirst($this->prompt('Class?'));
            } while (empty($name));
        } else {
            $name = ucfirst(array_shift($params));
        }
        $officialName = $name;

        list($dir, $created) = $this->_createModelDir();

        $names = array_map('ucfirst', explode('_', $name));
        $name = array_pop($names);

        foreach ($names as $rep) {
            $dir .= $rep . '/';
            if (!is_dir($dir)) {
                mkdir($dir);
            }
        }

        $filename = $dir . $name . '.php';
        if (!is_file($filename)) {
            file_put_contents($filename, $this->getTemplate('model_class', array(
                '{Name}' => implode('_', $names) . (empty($names) ? '' : '_') . $name,
                'Mage_Core_Model_Abstract' => ($name == 'Session' ? 'Mage_Core_Model_Session_Abstract' : 'Mage_Core_Model_Abstract')
            )));
        }

        if (empty($params)) {
            $params = explode(' ', $this->prompt('Methods?'));
        }

        $content = file_get_contents($filename);
        $this->replaceVarsAndMethods($content, $params, $type);

        // Other data
        if (isset($data['consts'])) {
            $tag = $this->getTag('new_const');
            $content = str_replace($tag, $data['consts'] . "\n$tag", $content);
        }
        if (isset($data['vars'])) {
            $tag = $this->getTag('new_var');
            $content = str_replace($tag, $data['vars'] . "\n$tag", $content);
        }
        if (isset($data['methods'])) {
            $tag = $this->getTag('new_method');
            $content = str_replace($tag, $data['methods'] . "\n$tag", $content);
        }

        file_put_contents($filename, $content);

        $this->setLast(__FUNCTION__, $officialName);
    }
}