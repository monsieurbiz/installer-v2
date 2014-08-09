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
use Mbiz\Installer\Helper as InstallerHelper;

class Controller extends BaseCommand {

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $_installerHelper = new InstallerHelper();

        $params = $input->getParams();
        if (empty($params)) {
            do {
                $name = ucfirst($_installerHelper->prompt('Name? (enter for index)'));
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

        $dir = $_installerHelper->getModuleDir('controllers');
        foreach ($names as $rep) {
            $dir .= $rep . '/';
            if (!is_dir($dir)) {
                mkdir($dir);
            }
        }

        $filename = $dir . $name . 'Controller.php';
        if (!is_file($filename)) {
            $content = $_installerHelper->getTemplate('controller_class', array(
                '{Name}' => implode('_', $names) . (empty($names) ? '' : '_') . $name,
                'Mage_Core_Controller_Front_Action' => $isAdminhtml ? 'Mage_Adminhtml_Controller_Action' : 'Mage_Core_Controller_Front_Action'
            ));

            // Is allowed method
            if ($isAdminhtml) {
                $tag = $_installerHelper->getTag('new_method');
                $method = $_installerHelper->getTemplate('is_allowed_method');
                $content = str_replace($tag, "$tag\n" . $method, $content);
            }

            file_put_contents($filename, $content);
        }

        if (empty($params)) {
            $params = explode(' ', $_installerHelper->prompt('Action?'));
        }

        // Vars & Methods
        $content = file_get_contents($filename);
        $_installerHelper->replaceVarsAndMethods($content, $params, 'action');

        // Other data
        if (isset($data['consts'])) {
            $tag = $_installerHelper->getTag('new_const');
            $content = str_replace($tag, $data['consts'] . "\n$tag", $content);
        }
        if (isset($data['vars'])) {
            $tag = $_installerHelper->getTag('new_var');
            $content = str_replace($tag, $data['vars'] . "\n$tag", $content);
        }
        if (isset($data['methods'])) {
            $tag = $_installerHelper->getTag('new_method');
            $content = str_replace($tag, $data['methods'] . "\n$tag", $content);
        }

        file_put_contents($filename, $content);

        $_installerHelper->setLast(__FUNCTION__, $officialName);
    }
}