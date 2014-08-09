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


class Misc extends BaseCommand
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (empty($params)) {
            do {
                $name = $this->prompt('Which name?');
            } while (empty($name));
        } else {
            $name = array_shift($params);
        }

        $name = str_replace(' ', '_', strtolower($name));

        $dir = $this->getMiscDir();
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $filename = $dir . '/' . $name . '.php';

        if (!is_file($filename)) {
            file_put_contents($filename, $this->getTemplate('misc'));
        }

    }
}