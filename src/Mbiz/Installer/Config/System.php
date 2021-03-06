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
use Symfony\Component\Console\Input\ArrayInput as ArrayInput;

class System extends BaseCommand {

    public function execute(InputInterface $input, OutputInterface $output)
    {
        // Helper data
        $command = $this->getApplication()->find('helper');
        $arguments = array(
            'command' => 'helper',
            'params'    => array('data', '-')
        );

        $input = new ArrayInput($arguments);
        $command->run($input, $output);

        $_installerHelper = new InstallerHelper();

        $dir = $_installerHelper->getModuleDir('etc');

        if (!is_file($filename = $dir . '/system.xml')) {
            file_put_contents($filename, $_installerHelper->getTemplate('system_xml', array(
                '{module}' => strtolower($_installerHelper->getModuleName())
            )));
        }
    }
}