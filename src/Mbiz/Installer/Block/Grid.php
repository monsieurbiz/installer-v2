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

namespace Mbiz\Installer\Block\Grid;

use Mbiz\Installer\Command\Command as BaseCommand;

class Grid{

    public function execute(InputInterface $input, OutputInterface $output)
    {
        // Check entity
        if (empty($params)) {
            do {
                $entity = $this->prompt('Which entity?');
            } while (empty($entity));
        } else {
            $entity = array_shift($params);
        }

        // Check entity exists
        $this->_processResources(array());

        $config = $this->getConfig();
        if (!isset($config->global)) {
            $config->addChild('global');
        }
        $resourceModel = $config->global->models->{strtolower($this->getModuleName())}->resourceModel;
        $entities = $config->global->models->{$resourceModel}->entities;
        if (!$entities->{strtolower($entity)}) {
            $this->_processEntity(array($entity));
        }
        unset($config);

        // Create directories :)
        $names = $entityTab = array_map('ucfirst', explode('_', $entity));
        array_unshift($names, 'Adminhtml');

        list($dir, $created) = $this->getModuleDir('Block', true);

        if ($created) {
            $config = $this->getConfig();
            $global = $config->global;
            if (!isset($global['blocks'])) {
                $global->addChild('blocks')->addChild(strtolower($this->getModuleName()))->addChild('class', $this->getModuleName() . '_Block');
            }
            $this->writeConfig();
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
            file_put_contents($filename, $this->getTemplate('grid_container_block', array(
                '{Entity}' => end($names),
                '{Name}' => implode('_', $names),
                '{blockGroup}' => strtolower($this->getModuleName()),
                '{controller}' => 'adminhtml_' . strtolower($entity)
            )));
        }

        // Create grid
        $filename = $dir . '/Grid.php';

        if (!is_file($filename)) {
            file_put_contents($filename, $this->getTemplate('grid_block', array(
                '{Entity}' => end($names),
                '{Name}' => implode('_', $names) . '_Grid',
                '{resource_model_collection}' => strtolower($this->getModuleName()) . '/' . strtolower($entity) . '_collection',
                '{Collection_Model}' => $this->getModuleName() . '_Model_Resource_' . implode('_', $entityTab) . '_Collection'
            )));
        }

        // Methods
        $methods = $this->getTemplate('grid_controller_methods', array(
            '{Entity}' => end($names),
            '{entity}' => strtolower(end($names)),
            '{name}' => strtolower(implode('_', $names)),
            '{grid_name}' => strtolower(implode('_', $names) . '_Grid'),
        ));

        // Grid controller..
        $this->_processController(array('adminhtml_' . strtolower($this->_module) . '_' . strtolower($entity), '-'), compact('methods'));

        // Helper data
        $this->_processHelper(array('data', '-'));

        // Router
        $this->_processRouter(array('admin'));
    }
}