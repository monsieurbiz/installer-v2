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

namespace Mbiz\Installer\Misc\Tmp;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Core\Module as Module;
use Mbiz\Installer\Router\Router as Router;
use Mbiz\Installer\Controller\Controller as Controller;

class Tmp extends BaseCommand
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $_module = new Module();
        $_module->execute(array(self::$_config->company_name_short, 'tmp', 'local'), true);

        // Router
        $_router = new Router();
        $_router->execute(array('front', 'tmp'));

        if (empty($params)) {
            $params = array('index');
        }
        array_unshift($params, 'index');
        $_controller = new Controller();
        $_controller->execute($params);
    }
}