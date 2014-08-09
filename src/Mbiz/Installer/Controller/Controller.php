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

namespace Mbiz\Installer\Controller;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Helper as InstallationHelper;

class Controller{

    public function execute(array $params, array $data = array())
    {

        $_installationHelper = new InstallationHelper();

        if (empty($params)) {
            do {
                $name = ucfirst($_installationHelper->prompt('Name? (enter for index)'));
                if (empty($name)) {
                    $name = 'Index';
                }
            } while (empty($name));
        } else {
            $name = array_shift($params);
        }
        $officialName = $name;

        $isAdminhtml = stripos($officialName, 'admin') === 0;

        $names = array_map('ucfirst', explode('_', $name));
        $name = array_pop($names);

        $dir = $_installationHelper->getModuleDir('controllers');
        foreach ($names as $rep) {
            $dir .= $rep . '/';
            if (!is_dir($dir)) {
                mkdir($dir);
            }
        }

        $filename = $dir . $name . 'Controller.php';
        if (!is_file($filename)) {
            $content = $_installationHelper->getTemplate('controller_class', array(
                '{Name}' => implode('_', $names) . (empty($names) ? '' : '_') . $name,
                'Mage_Core_Controller_Front_Action' => $isAdminhtml ? 'Mage_Adminhtml_Controller_Action' : 'Mage_Core_Controller_Front_Action'
            ));

            // Is allowed method
            if ($isAdminhtml) {
                $tag = $_installationHelper->getTag('new_method');
                $method = $_installationHelper->getTemplate('is_allowed_method');
                $content = str_replace($tag, "$tag\n" . $method, $content);
            }

            file_put_contents($filename, $content);
        }

        if (empty($params)) {
            $params = explode(' ', $_installationHelper->prompt('Action?'));
        }

        // Vars & Methods
        $content = file_get_contents($filename);
        $_installationHelper->replaceVarsAndMethods($content, $params, 'action');

        // Other data
        if (isset($data['consts'])) {
            $tag = $_installationHelper->getTag('new_const');
            $content = str_replace($tag, $data['consts'] . "\n$tag", $content);
        }
        if (isset($data['vars'])) {
            $tag = $_installationHelper->getTag('new_var');
            $content = str_replace($tag, $data['vars'] . "\n$tag", $content);
        }
        if (isset($data['methods'])) {
            $tag = $_installationHelper->getTag('new_method');
            $content = str_replace($tag, $data['methods'] . "\n$tag", $content);
        }

        file_put_contents($filename, $content);

        $_installationHelper->setLast(__FUNCTION__, $officialName);
    }
}