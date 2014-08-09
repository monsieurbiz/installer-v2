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

namespace Mbiz\Installer\Core;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Helper as InstallerHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mbiz\Installer\Shell;

class Module extends BaseCommand {

    /**
     * Configure the Command
     * @return \Mbiz\Installer\Core\Module
     */
    public function configure()
    {
        return $this
            ->setName('module')
            ->setDescription('Open a module shell.')
            ->addArgument(
                'vendor',
                InputArgument::OPTIONAL,
                'Vendor name'
            )
            ->addArgument(
                'module',
                InputArgument::OPTIONAL,
                'Module name'
            )
            ->addArgument(
                'pool',
                InputArgument::OPTIONAL,
                'Pool (community or local)'
            )
        ;
    }

    /**
     * Execute the command
     * @param \Symfony\Component\Console\Input\InputInterface $input The input
     * @param \Symfony\Component\Console\Output\OutputInterface $output The output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // Vendor name
        $vendor = $input->getArgument('vendor');
        $checkVendor = function ($answer) {
            if (!$answer) {
                throw new \RunTimeException(
                    "You need to specify a vendor name."
                );
            }
            return $answer;
        };

        try {
            $vendor = $checkVendor($vendor);
        } catch (\RunTimeException $e) {
            $vendor = $this->getDialog()->askAndValidate(
                $output,
                'Please enter the module\'s vendor name:',
                $checkVendor
            );
        }

        // Module name
        $module = $input->getArgument('module');
        $checkModule = function ($answer) {
            if (!$answer) {
                throw new \RunTimeException(
                    "You need to specify a module name."
                );
            }
            return $answer;
        };

        try {
            $module = $checkModule($module);
        } catch (\RunTimeException $e) {
            $module = $this->getDialog()->askAndValidate(
                $output,
                'Please enter the module\'s name:',
                $checkModule
            );
        }

        // Pool
        $checkPool = function ($answer) {
            if (!$answer || !in_array($answer, ['local', 'community'])) {
                throw new \RunTimeException(
                    "You need to specify a pool: community or local."
                );
            }
            return $answer;
        };
        try {
            $pool = $checkPool($input->getArgument('pool'));
        } catch (\RunTimeException $e) {
            $output->writeLn('<error>' . $e->getMessage() . '</error>');
            $pool = $this->getDialog()->askAndValidate(
                $output,
                'Please enter the module\'s pool, <comment>community or local (by default)</comment>:',
                $checkPool,
                false,
                'local'
            );
        }

        $_installerHelper = new InstallerHelper();
        $_installerHelper->setLast();

        // Init the module
        $this->getApplication()->initModule($vendor, $module, $pool);
    }

}
