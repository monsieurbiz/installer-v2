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
use Mbiz\Installer\Helper\Translate as Translate;
use Mbiz\Installer\Helper as InstallerHelper;

class Addtranslate extends BaseCommand
{

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $_installerHelper = new InstallerHelper();
        $config = $_installerHelper->getConfig();
        if (!isset($_installerHelper->frontend)) {
            $config->addChild('frontend');
        }
        if (!isset($config->frontend->translate) && !isset($config->adminhtml->translate)) {
            $_translate = new Translate();
            $_translate->execute($input, $output);
        }

        do {
            $translate = $_installerHelper->prompt('Translate?');
        } while (empty($translate));

        $translate = str_replace('"', '""', $translate);

        foreach ($_installerHelper->getLocales() as $locale) {
            $traduction = $_installerHelper->prompt('Traduction for ' . red() . $locale . white() . '?');
            if (empty($traduction)) {
                $traduction = $translate;
            } else {
                $traduction = str_replace('"', '""', $traduction);
            }
            $dir = $_installerHelper->getAppDir() . 'locale/' . $locale . '/';
            $filename = $dir . $_installerHelper->getModuleName() . '.csv';
            if (is_dir($dir) && is_file($filename)) {
                $fp = fopen($filename, 'a');
                $str = '"' . $translate . '","' . $traduction . '"' . "\n";
                fputs($fp, $str, mb_strlen($str));
                fclose($fp);
            }
        }
    }
}