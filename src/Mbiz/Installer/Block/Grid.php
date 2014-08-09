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
use Symfony\Component\Console\Input\ArrayInput as ArrayInput;

class Grid extends BaseCommand {

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $_installerHelper = new InstallerHelper();

        $params = $input->getParams();
        // Check entity
        if (empty($params)) {
            do {
                $entity = $_installerHelper->prompt('Which entity?');
            } while (empty($entity));
        } else {
            $entity = array_shift($params);
        }

        // Check entity exists
        $command = $this->getApplication()->find('resources');
        $command->run($input, $output);

        $config = $_installerHelper->getConfig();
        if (!isset($config->global)) {
            $config->addChild('global');
        }
        $resourceModel = $config->global->models->{strtolower($_installerHelper->getModuleName())}->resourceModel;
        $entities = $config->global->models->{$resourceModel}->entities;
        if (!$entities->{strtolower($entity)}) {
            $command = $this->getApplication()->find('entity');
            $arguments = array(
                'command' => 'entity',
                'params'    => $entity
            );

            $input = new ArrayInput($arguments);
            $command->run($input, $output);

        }
        unset($config);

        // Create directories :)
        $names = $entityTab = array_map('ucfirst', explode('_', $entity));
        array_unshift($names, 'Adminhtml');

        list($dir, $created) = $_installerHelper->getModuleDir('Block', true);

        if ($created) {
            $config = $_installerHelper->getConfig();
            $global = $config->global;
            if (!isset($global['blocks'])) {
                $global->addChild('blocks')->addChild(strtolower($_installerHelper->getModuleName()))->addChild('class', $_installerHelper->getModuleName() . '_Block');
            }
            $_installerHelper->writeConfig();
        }

        foreach ($names as $rep) {
            $dir .= $rep . '/';
            if (!is_dir($dir)) {
                mkdir($dir);
            }
        }

        // Create container
        $filename = $dir . '../' . end($names) . '.php';

        if (!is_file($filename)) {
            file_put_contents($filename, $_installerHelper->getTemplate('grid_container_block', array(
                '{Entity}' => end($names),
                '{Name}' => implode('_', $names),
                '{blockGroup}' => strtolower($_installerHelper->getModuleName()),
                '{controller}' => 'adminhtml_' . strtolower($entity)
            )));
        }

        // Create grid
        $filename = $dir . '/Grid.php';

        if (!is_file($filename)) {
            file_put_contents($filename, $_installerHelper->getTemplate('grid_block', array(
                '{Entity}' => end($names),
                '{Name}' => implode('_', $names) . '_Grid',
                '{resource_model_collection}' => strtolower($_installerHelper->getModuleName()) . '/' . strtolower($entity) . '_collection',
                '{Collection_Model}' => $_installerHelper->getModuleName() . '_Model_Resource_' . implode('_', $entityTab) . '_Collection'
            )));
        }

        // Methods
        $methods = $_installerHelper->getTemplate('grid_controller_methods', array(
            '{Entity}' => end($names),
            '{entity}' => strtolower(end($names)),
            '{name}' => strtolower(implode('_', $names)),
            '{grid_name}' => strtolower(implode('_', $names) . '_Grid'),
        ));

        // Grid controller..
        $command = $this->getApplication()->find('controller');
        $arguments = array(
            'command' => 'controller',
            'params'    => array('adminhtml_' . strtolower($this->_module) . '_' . strtolower($entity), '-'), compact('methods')
        );

        $input = new ArrayInput($arguments);
        $command->run($input, $output);

        // Helper data
        $command = $this->getApplication()->find('helper');
        $arguments = array(
            'command' => 'helper',
            'params'    => array('data', '-')
        );

        $input = new ArrayInput($arguments);
        $command->run($input, $output);

        // Router
        $command = $this->getApplication()->find('router');
        $arguments = array(
            'command' => 'router',
            'params'    => array('admin')
        );

        $input = new ArrayInput($arguments);
        $command->run($input, $output);
    }
}