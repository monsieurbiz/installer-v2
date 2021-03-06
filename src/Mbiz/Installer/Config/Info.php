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

class Info extends BaseCommand {

    public function execute(InputInterface $input, OutputInterface $output)
    {
        // Colors
        $r = red();
        $y = yellow();
        $b = blue();
        $g = green();
        $w = white();

        $_installerHelper = new InstallerHelper();
        $config = $_installerHelper->getConfig();

        $width = 80;

        // Global
        echo $r . "Global Configuration\n";
        echo $r . str_repeat('-', $width) . "\n";

        // Helpers
        if ($config->global && $config->global->helpers) {
            echo $g . "Helpers\n";
            foreach ($config->global->helpers->children() as $child) {
                $namespace = $child->getName();
                if ($child->class) {
                    echo $w . $namespace;
                    echo $b . ' => ';
                    echo $w . $child->class . "\n";
                }
                if ($child->rewrite) {
                    foreach ($child->rewrite->children() as $rewrite) {
                        echo $w . $namespace . '/' . $rewrite->getName();
                        echo $r . ' => ';
                        echo $w . $rewrite . "\n";
                    }
                }
            }
            echo "\n";
        }

        // Blocks
        if ($config->global && $config->global->blocks) {
            echo $g . "Blocks\n";
            foreach ($config->global->blocks->children() as $child) {
                $namespace = $child->getName();
                if ($child->class) {
                    echo $w . $namespace;
                    echo $b . ' => ';
                    echo $w . $child->class . "\n";
                }
                if ($child->rewrite) {
                    foreach ($child->rewrite->children() as $rewrite) {
                        echo $w . $namespace . '/' . $rewrite->getName();
                        echo $r . ' => ';
                        echo $w . $rewrite . "\n";
                    }
                }
            }
            $space = $w . str_repeat(' ', 20);
            echo "\n";
        }

        // Models
        if ($config->global && $config->global->models) {
            echo $g . "Models\n";
            $resourcesModels = array();
            foreach ($config->global->models->children() as $child) {
                $namespace = $child->getName();
                if ($child->class) {
                    echo $w . $namespace;
                    echo $b . ' => ';
                    echo $w . $child->class;
                }
                if ($child->resourceModel) {
                    $resourceModels[(string) $child->resourceModel] = $namespace;
                    echo $w . ' (' . $child->resourceModel . ')';
                }
                if ($child->class) {
                    echo "\n";
                }
                if ($child->entities) {
                    foreach ($child->entities->children() as $entity) {
                        echo $y . '  table ';
                        echo $w . (array_key_exists($namespace, $resourceModels) ? $resourceModels[$namespace] : $namespace) . '/' . $entity->getName();
                        echo $y . ' => ';
                        echo $w . trim($entity->table) . "\n";
                    }
                }
                if ($child->rewrite) {
                    foreach ($child->rewrite->children() as $rewrite) {
                        echo $w . $namespace . '/' . $rewrite->getName();
                        echo $r . ' => ';
                        echo $w . trim($rewrite) . "\n";
                    }
                }
            }
            echo "\n";
        }

        // Events
        if ($config->global && $config->global->events) {
            $command = $this->getApplication()->find('infoevent');
            $arguments = array(
                'command' => 'infoevent',
                'params'    => array('data', $config->global->events)
            );

            $input = new ArrayInput($arguments);
            $command->run($input, $output);
        }

        // Resources
        if ($config->global && $config->global->resources) {
            echo green() . "Resources\n";
            foreach ($config->global->resources->children() as $child) {
                echo $w . $child->getName() . "\n";
                if ($child->setup) {
                    echo $y . '  setup' . $w . ': ' . $child->setup->module;
                    if ($child->setup->class) {
                        echo ' (' . $child->setup->class . ')';
                    }
                    echo "\n";
                }
                if ($child->connection) {
                    echo $y . '  connection' . $w . ': use ' . $child->connection->use . "\n";
                }
                if ($child->use) {
                    echo $y . '  use' . $w . ': ' . $child->use . "\n";
                }
            }
            echo "\n";
        }

        // Template
        if ($config->global && $config->global->template) {
            if ($config->global->template->email) {
                // TODO
            }
        }

        // Frontend
        echo $r . "Frontend Configuration\n";
        echo $r . str_repeat('-', $width) . "\n";

        // Routers
        if ($config->frontend && $config->frontend->routers) {
            $command = $this->getApplication()->find('inforouter');
            $arguments = array(
                'command' => 'inforouter',
                'params'    => array('data', $config->frontend->routers)
            );

            $input = new ArrayInput($arguments);
            $command->run($input, $output);
        }

        // Layout
        if ($config->frontend && $config->frontend->layout) {
            $command = $this->getApplication()->find('infolayout');
            $arguments = array(
                'command' => 'infolayout',
                'params'    => array('data', $config->frontend->layout)
            );

            $input = new ArrayInput($arguments);
            $command->run($input, $output);
        }

        // Translate
        if ($config->frontend && $config->frontend->translate) {
            $command = $this->getApplication()->find('infotranslate');
            $arguments = array(
                'command' => 'infotranslate',
                'params'    => array('data', $config->frontend->translate)
            );

            $input = new ArrayInput($arguments);
            $command->run($input, $output);
        }


        // Admin
        echo $r . "Admin & Adminhtml Configurations\n";
        echo $r . str_repeat('-', $width) . "\n";


        // Routers
        if ($config->admin && $config->admin->routers) {
            $command = $this->getApplication()->find('inforouter');
            $arguments = array(
                'command' => 'inforouter',
                'params'    => array('data', $config->frontend->routers)
            );

            $input = new ArrayInput($arguments);
            $command->run($input, $output);
        }

        // Layout
        if ($config->admin && $config->adminhtml->layout) {
            $command = $this->getApplication()->find('infolayout');
            $arguments = array(
                'command' => 'infolayout',
                'params'    => array('data', $config->frontend->layout)
            );

            $input = new ArrayInput($arguments);
            $command->run($input, $output);
        }

        // Translate
        if ($config->admin && $config->adminhtml->translate) {
            $command = $this->getApplication()->find('infotranslate');
            $arguments = array(
                'command' => 'infotranslate',
                'params'    => array('data', $config->frontend->translate)
            );

            $input = new ArrayInput($arguments);
            $command->run($input, $output);
        }


        // Config
        echo $r . "Default System Configuration\n";
        echo $r . str_repeat('-', $width) . "\n";

        if ($config->default && $config->default) {
            foreach ($config->default->children() as $namespace) {
                foreach ($namespace->children() as $section) {
                    foreach ($section->children() as $key) {
                        echo $w . $namespace->getName();
                        echo $y . '/';
                        echo $w . $section->getName();
                        echo $y . '/';
                        echo $w . $key->getName();
                        $value = (string) $key;
                        if (strlen($value) > 0) {
                            echo $b . ' => ' . $w . $value;
                        }
                        echo "\n";
                    }
                }
            }
        }

        $_installerHelper->_processReloadConfig();
    }
}