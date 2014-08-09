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

namespace Mbiz\Installer\Config;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Helper as InstallerHelper;

class Depends{

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $_installerHelper = new InstallerHelper();

        $params = $input->getParams();
        if (empty($params)) {
            do {
                $params = $_installerHelper->prompt('Modules?');
            } while (empty($params));
            $params = explode(' ', $params);
        }

        $config = $_installerHelper->getConfig();
        $etc = simplexml_load_file($etcFilename = $_installerHelper->getAppDir() . 'etc/modules/' . $_installerHelper->getModuleName() . '.xml');

        if (!$configDepends = $config->modules->{$_installerHelper->getModuleName()}->depends) {
            $configDepends = $config->modules->{$_installerHelper->getModuleName()}->addChild('depends');
        }
        if (!$etcDepends = $etc->modules->{$_installerHelper->getModuleName()}->depends) {
            $etcDepends = $etc->modules->{$_installerHelper->getModuleName()}->addChild('depends');
        }


        foreach ($params as $module) {
            if ($module[0] == '-') {
                $module = substr($module, 1);
                if ($configDepends->{$module}) {
                    unset($configDepends->{$module});
                }
                if ($etcDepends->{$module}) {
                    unset($etcDepends->{$module});
                }
            } else {
                if (!$configDepends->{$module}) {
                    $configDepends->addChild($module);
                }
                if (!$etcDepends->{$module}) {
                    $etcDepends->addChild($module);
                }
            }
        }

        $_installerHelper->writeConfig();

        // Write etc/modules
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = 4;
        $dom->loadXML($etc->asXML());
        $tidy = tidy_parse_string($dom->saveXml(), array(
            'indent' => true,
            'input-xml' => true,
            'output-xml' => true,
            'add-xml-space' => false,
            'indent-spaces' => 4,
            'wrap' => 300
        ));
        $tidy->cleanRepair();
        file_put_contents($etcFilename, (string)$tidy);
        unset($dom);

        $_installerHelper->setLast(__FUNCTION__);
    }
}