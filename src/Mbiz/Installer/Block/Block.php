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

namespace Mbiz\Installer\Block;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Helper as InstallerHelper;

class Block extends BaseCommand {

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $_installerHelper = new InstallerHelper();

        $params = $input->getParams();
        if (empty($params)) {
            do {
                $name = ucfirst($_installerHelper->prompt('Class?'));
            } while (empty($name));
        } else {
            $name = array_shift($params);
        }
        $officialName = $name;

        $isAdminhtml = stripos($officialName, 'admin') === 0;

        $names = array_map('ucfirst', explode('_', $name));

        // Create file
        list($dir, $created) = $_installerHelper->getModuleDir('Block', true);
        
        if ($created) {
            $config = $_installerHelper->getConfig();
            if (!isset($config->global)) {
                $config->addChild('global');
            }
            $global = $config->global;
            if (!isset($global['blocks'])) {
                $global->addChild('blocks')->addChild(strtolower($_installerHelper->getModuleName()))->addChild('class', $_installerHelper->getModuleName() . '_Block');
            }
            $_installerHelper->writeConfig();
        }

        $name = array_pop($names);

        foreach ($names as $rep) {
            $dir .= $rep . '/';
            if (!is_dir($dir)) {
                mkdir($dir);
            }
        }

        $filename = $dir . $name . '.php';
        if (!is_file($filename)) {
            file_put_contents($filename, $_installerHelper->getTemplate('block_class', array(
                '{Name}' => implode('_', $names) . (empty($names) ? '' : '_') . $name,
                'Mage_Core_Block_Template' => $isAdminhtml ? 'Mage_Adminhtml_Block_Template' : 'Mage_Core_Block_Template'
            )));
        }

        $phtmlKey = array_search('-p', $params);
        if ($phtmlKey !== false) {
            unset($params[$phtmlKey]);
            $dir = $_installerHelper->getDesignDir('frontend', 'template');
            $dirs = $names;
            array_unshift($dirs, strtolower($_installerHelper->getModuleName()));
            foreach ($dirs as $rep) {
                $dir .= strtolower($rep) . '/';
                if (!is_dir($dir)) {
                    mkdir($dir);
                }
            }
            $phtmlFilepath = strtolower(implode('/', $dirs) . '/' . $name . '.phtml');
            $phtmlFilename = $dir . strtolower($name) . '.phtml';
            if (!is_file($phtmlFilename)) {
                file_put_contents($phtmlFilename, $_installerHelper->getTemplate('phtml', array('{Name}' => implode('_', $names) . (empty($names) ? '' : '_') . $name)));
            }
            $type = lcfirst($this->_namespace) . '_' . strtolower($this->_module) . '/' . implode('_', array_map('lcfirst', explode('_', $officialName)));
            echo "\n" . white() . '<block type="' . red() . $type . white() . '" name="' . lcfirst($name) . '" as="' . red() . lcfirst($name) . white() . '" template="' . red() . $phtmlFilepath . white() . '" />' . "\n\n";
        }

        if (empty($params) && $phtmlKey === false) {
            $params = explode(' ', $_installerHelper->prompt('Methods?'));
        }

        if ($phtmlKey !== false) {
            array_unshift($params, 'TEMPLATE=' . $phtmlFilepath);
        }

        $content = file_get_contents($filename);
        $_installerHelper->replaceVarsAndMethods($content, $params);
        file_put_contents($filename, $content);
        $_installerHelper->setLast(__FUNCTION__, $officialName);
    }
}