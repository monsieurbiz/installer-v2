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
use Mbiz\Installer\Helper as InstallerHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class Doc extends BaseCommand
{
    /**
     * Configure the Command
     * @return \Mbiz\Installer\Misc\Doc
     */
    public function configure()
    {
        return $this
            ->setName('doc')
            ->setDescription('Create README.md file')
            ->addArgument(
                'title',
                InputArgument::REQUIRED,
                'Title of the README.md entered in quotes: "My Module"'
            )
            ->setTemplatesDirectory(__DIR__ . '/Resources/output/')
            ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $title = $input->getArgument('title');

        $installerHelper = new InstallerHelper();
        $dir = $installerHelper->getModuleDir('doc');

        if (!is_file($filename = $dir . '/README.md')) {
            $output->writeLn($this->render('doc.twig', ['title' => $title]));
            file_put_contents($filename, $this->render('doc.twig', ['title' => $title]));
        }

    }
}