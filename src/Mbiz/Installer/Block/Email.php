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

class Email extends BaseCommand {

    /**
     * Create a custom email template with the good configuration and file
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {

        $_installerHelper = new InstallerHelper();

        // Get email name
        if (empty($params)) {
            do {
                $name = $_installerHelper->prompt('Email identifier?');
            } while (empty($name));
        }
        $name = strtolower($name);

        // Configuration
        $config = $_installerHelper->getConfig();

        // Create templte node if not exists
        if (!isset($config->global)) {
            $config->addChild('global');
        }
        $global = $config->global;

        if (!isset($global->template)) {
            $global->addChild('template');
        }
        $template = $global->template;

        if (!isset($template->email)) {
            $template->addChild('email');
        }
        $email = $template->email;

        // Create email node
        $moduleName = strtolower($_installerHelper->getModuleName());
        $node = $email->addChild($moduleName . '_email_' . $name);
        $node->addAttribute('translate', 'label');
        $node->addAttribute('module', strtolower($_installerHelper->getModuleName()));

        $ext = '.html';
        $node->addChild('label', 'Your email template name');
        $node->addChild('file', $moduleName . '/' . $name . $ext);
        $node->addChild('type', 'html');

        // Default configuration
        $command = $this->getApplication()->find('defaultconfig');
        $arguments = array(
            'command' => 'defaultconfig',
            'params'    => array($moduleName . '/email/' . $name, $moduleName . '_email_' . $name)
        );

        $input = new ArrayInput($arguments);
        $command->run($input, $output);

        // Save configuration
        $_installerHelper->writeconfig();

        // Model
        // Default configuration
        $command = $this->getApplication()->find('model');
        $arguments = array(
            'command' => 'model',
            'params'    => array(
                'email',
                'CONFIG_KEY_EMAIL_' . strtoupper($name) . "=$moduleName/email/$name"
            ), 'model', array(
                'methods' => $_installerHelper->getTemplate('email_method', array(
                    '{NAME}' => strtoupper($name),
                    '{methodName}' => lcfirst($_installerHelper->_camelize('send_' . $name)),
                    '{name}' => $name
                ))
            )
        );

        $input = new ArrayInput($arguments);
        $command->run($input, $output);

        // The file
        $appDir = $_installerHelper->getAppDir();
        foreach ($_installerHelper->getLocales() as $locale) {
            $dir = $appDir . '/locale/' . $locale;
            if (!is_dir($dir)) {
                mkdir($dir, 755);
            }
            $templateDir = $dir . '/template';
            if (!is_dir($templateDir)) {
                mkdir($templateDir, 755);
            }
            $emailsDir = $templateDir . '/email';
            if (!is_dir($emailsDir)) {
                mkdir($emailsDir, 755);
            }
            $finalDir = $emailsDir . '/' . $moduleName;
            if (!is_dir($finalDir)) {
                mkdir($finalDir);
            }
            $filename = $finalDir . '/' . $name . $ext;
            if (!is_file($filename)) {
                file_put_contents($filename, $_installerHelper->getTemplate('email_template'));
            }
        }
    }
}