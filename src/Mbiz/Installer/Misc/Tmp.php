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
use Symfony\Component\Console\Input\ArrayInput as ArrayInput;

class Tmp extends BaseCommand
{
    public function execute(InputInterface $input, OutputInterface $output)
    {

        $params = $input->getParams();

        $command = $this->getApplication()->find('module');
        $arguments = array(
            'command' => 'module',
            'params'    => array(array(self::$_config->company_name_short, 'tmp', 'local'), true)
        );

        $input = new ArrayInput($arguments);
        $command->run($input, $output);

        $command = $this->getApplication()->find('router');
        $arguments = array(
            'command' => 'router',
            'params'    => array('front', 'tmp')
        );

        $input = new ArrayInput($arguments);
        $command->run($input, $output);

        if (empty($params)) {
            $params = array('index');
        }
        array_unshift($params, 'index');

        $command = $this->getApplication()->find('controller');
        $arguments = array(
            'command' => 'controller',
            'params'    => array('data', $params)
        );

        $input = new ArrayInput($arguments);
        $command->run($input, $output);
    }
}