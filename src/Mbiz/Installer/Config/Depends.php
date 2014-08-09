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

namespace Mbiz\Installer\Config\Depends;

use Mbiz\Installer\Command\Command as BaseCommand;

class Depends{

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (empty($params)) {
            do {
                $params = $this->prompt('Modules?');
            } while (empty($params));
            $params = explode(' ', $params);
        }

        $config = $this->getconfig();
        $etc = simplexml_load_file($etcFilename = $this->getAppDir() . 'etc/modules/' . $this->getModuleName() . '.xml');

        if (!$configDepends = $config->modules->{$this->getModuleName()}->depends) {
            $configDepends = $config->modules->{$this->getModuleName()}->addChild('depends');
        }
        if (!$etcDepends = $etc->modules->{$this->getModuleName()}->depends) {
            $etcDepends = $etc->modules->{$this->getModuleName()}->addChild('depends');
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

        $this->writeConfig();

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

        $this->setLast(__FUNCTION__);
    }
}