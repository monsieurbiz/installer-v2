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
use Mbiz\Installer\Helper\Helper as Helper;
use Mbiz\Installer\Config\Resources as Resources;
use Mbiz\Installer\Model\Entity as Entity;
use Mbiz\Installer\Controller\Controller as Controller;
use Mbiz\Installer\Router\Router as Router;
use Mbiz\Installer\Helper as InstallationHelper;

class Form{

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $_installationHelper = new InstallationHelper();

        // Check entity
        if (empty($params)) {
            do {
                $entity = $_installationHelper->prompt('Which entity?');
            } while (empty($entity));
        } else {
            $entity = array_shift($params);
        }

        // Check entity exists
        $_resources = new Resources();
        $_resources->execute(array());

        $config = $_installationHelper->getConfig();
        if (!isset($config->global)) {
            $config->addChild('global');
        }
        $resourceModel = $config->global->models->{strtolower($_installationHelper->getModuleName())}->resourceModel;
        $entities = $config->global->models->{$resourceModel}->entities;
        if (!$entities->{strtolower($entity)}) {
            $_entity = new Entity();
            $_entity->execute(array($entity));
        }
        unset($config);

        // Create directories :)
        $names = $entityTab = array_map('ucfirst', explode('_', $entity));
        array_unshift($names, 'Adminhtml');
        array_push($names, 'Edit');

        list($dir, $created) = $_installationHelper->getModuleDir('Block', true);

        if ($created) {
            $config = $_installationHelper->getConfig();
            $global = $config->global;
            if (!isset($global['blocks'])) {
                $global->addChild('blocks')->addChild(strtolower($_installationHelper->getModuleName()))->addChild('class', $_installationHelper->getModuleName() . '_Block');
            }
            $_installationHelper->writeConfig();
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
            file_put_contents($filename, $_installationHelper->getTemplate('form_container_block', array(
                '{Entity}' => end($entityTab),
                '{entity}' => strtolower(end($entityTab)),
                '{current}' => strtolower(end($entityTab)),
                '{Name}' => implode('_', $names),
                '{blockGroup}' => strtolower($_installationHelper->getModuleName()),
                '{controller}' => 'adminhtml_' . strtolower($entity),
                '{entity_mage_identifier}' => strtolower($_installationHelper->getModuleName() . '/' . implode('_', $entityTab)),
                '{Entity_Name}' => $_installationHelper->getModuleName() . '_Model_' . implode('_', $entityTab),
            )));
        }

        // Create form
        $filename = $dir . '/Form.php';

        if (!is_file($filename)) {
            file_put_contents($filename, $_installationHelper->getTemplate('form_block', array(
                '{Entity}' => end($entityTab),
                '{Name}' => implode('_', $names) . '_Form',
                '{current}' => strtolower(end($entityTab)),
                '{id_field}' => strtolower(end($entityTab)) . '_id',
                '{Entity_Name}' => $_installationHelper->getModuleName() . '_Model_' . implode('_', $entityTab),
            )));
        }

        // Methods
        $methods = $_installationHelper->getTemplate('form_controller_methods', array(
            '{Entity}' => end($entityTab),
            '{entity}' => strtolower(end($entityTab)),
            '{current}' => strtolower(end($entityTab)),
            '{form_name}' => strtolower(implode('_', $names)),
            '{entity_mage_identifier}' => strtolower($_installationHelper->getModuleName() . '/' . implode('_', $entityTab)),
        ));

        // Grid controller..
        $_controller = new Controller();
        $_controller->execute(array('adminhtml_' . strtolower($this->_module) .'_'. strtolower($entity) , '-'), compact('methods'));

        // Helper data
        $_helper = new Helper();
        $_helper->execute(array('data', '-'));

        // Router
        $_router = new Router();
        $_router->execute(array('admin'));
    }
}