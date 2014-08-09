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

namespace Mbiz\Installer\Misc;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Helper as InstallationHelper;

class Clean
{

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $cache = false;
        $logs = false;

        if (!count($params)) {
            $params = array('all');
        }

        foreach ($params as $param) {
            switch ($param) {
                case 'log':
                case 'logs':
                    $logs = true;
                    break;
                case 'cache':
                    $cache = true;
                    break;
                case 'all':
                default:
                    $cache = true;
                    $logs = true;
                    break;
            }
        }

        $path = trim(self::$_config->path, '/');
        $varDir = self::$_config->pwd . (!empty($path) ? '/' . $path : '') . '/var/';
        if (is_dir($varDir)) {
            if ($logs) {
                $logDir = $varDir . 'log/';
                if (is_dir($logDir)) {
                    $files = glob("{$logDir}*.log");
                    foreach ($files as $file) {
                        $fp = fopen($file, 'w');
                        ftruncate($fp, 0);
                        fclose($fp);
                    }
                }
                echo green() . "[OK] Logs\n";
            }
            if ($cache) {
                $_installationHelper = new InstallationHelper();
                $cacheDir = $varDir . 'cache/';
                if (is_dir($cacheDir)) {
                    $_installationHelper->_rmdir($cacheDir);
                }
                $fpcDir = $varDir . 'full_page_cache/';
                if (is_dir($fpcDir)) {
                    $_installationHelper->_rmdir($fpcDir);
                }
                echo green() . "[OK] Cache\n";
            }
        }
    }
}