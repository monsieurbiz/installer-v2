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

namespace Mbiz\Installer\Router\Routers;

use Mbiz\Installer\Command\Command as BaseCommand;


class Routers extends BaseCommand
{

    public function execute(InputInterface $input, OutputInterface $output)
    {
        echo green() . "Routers\n";
        foreach ($routers->children() as $child) {
            echo white() . $child->getName() . "\n";
            if ($child->use) {
                echo yellow() . '  type' . white() . ': ' . $child->use . "\n";
            }
            if ($child->args) {
                if ($child->args->module) {
                    echo yellow() . '  module' . white() . ': ' . $child->args->module . "\n";
                }
                if ($child->args->frontName) {
                    echo yellow() . '  frontName' . white() . ': ' . $child->args->frontName . "\n";
                }
                if ($child->args->modules) {
                    foreach ($child->args->modules->children() as $subchild) {
                        echo red() . '  use ';
                        echo white() . $subchild;
                        if ($subchild['before']) {
                            echo red() . ' before ' . white() . $subchild['before'];
                        }
                        echo red() . ' for ' . white() . $subchild->getName() . "\n";
                    }
                }
            }
        }
        echo "\n";
    }

}

