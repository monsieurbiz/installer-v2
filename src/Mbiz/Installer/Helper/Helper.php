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
namespace Mbiz\Installer\Helper;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Helper as InstallerHelper;

class Helper
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (empty($params)) {
            $name = ucfirst($_installerHelper->prompt('Class? (enter for Data)'));
            if (empty($name)) {
                $name = 'Data';
            }
        } else {
            $name = ucfirst(array_shift($params));
        }
        $officialName = $name;

        // Create file
        list($dir, $created) = $_installerHelper->getModuleDir('Helper', true);
        $_installerHelper = new InstallerHelper();
        if ($created) {
            $config = $_installerHelper->getConfig();
            if (!isset($config->global)) {
                $config->addChild('global');
            }
            $global = $config->global;
            if (!isset($global['helpers'])) {
                $global->addChild('helpers')->addChild(strtolower($_installerHelper->getModuleName()))->addChild('class', $_installerHelper->getModuleName() . '_Helper');
            }
            $_installerHelper->writeConfig();
        }

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
            file_put_contents($filename, $_installerHelper->getTemplate('helper_class', array('{Name}' => implode('_', $names) . (empty($names) ? '' : '_') . $name)));
        }

        if (empty($params)) {
            $params = explode(' ', $_installerHelper->prompt('Methods?'));
        }

        $content = file_get_contents($filename);
        $_installerHelper->replaceVarsAndMethods($content, $params);
        file_put_contents($filename, $content);

        $_installerHelper->setLast(__FUNCTION__, $officialName);
    }

}