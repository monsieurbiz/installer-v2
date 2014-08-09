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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Unicorn extends BaseCommand
{

    /**
     * Configure the Command
     * @return \Mbiz\Installer\Core\Unicorn
     */
    public function configure()
    {
        return $this
            ->setName('unicorn')
            ->setDescription('Unicorn shit')
            ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $unicorn = <<<UNICORN
                                            ,
                                            :
                                           ,.
                                          ,,
                                          ,,
                                     Ej E:,,
                                   t  E  ,
                                 ttt     ,,
                                tt;,,   ,,,,
                               tt,,, G  ,,,,, .t
                              tt,,it:L  t,,,,it,t
                              t,,,tt;EEDitt,,,,tt
                             tt,,tt EEEEE,tttttt
                             t;,,ttEEEE:EE ;ttt
                             t,,tt EEEE EEEEEED
                             t,,tt EEEE EEEEEEE
                             t,,tt.EEEEEEEEEEEEE
             .tt          t it,,ttfEEEEEEEEEEEEEE
              ,tt        i;,,t,,ttEEEEEEEEEEEEEEEE
              ,,tt: :,    ,,,t,,ttEEEEEEEEEEEEEEEE
              t,,ttttttt  :t,,,,ttEEEiEEEEEEEEEEEEE
              t,,,,;tttt   iii,,t EEEEEEEEEEEEEEEEE
              t;,,,,,,tt EE:,,,;tGEEEE EEEEEEED EEE
              tt,,,,,,,fEEEE,,; tEEEEE iEEEEEEEEEEE
              ttt,,,,,.EEEEEEffDEEEEEE  EEEEEEEEEEE
               tttti;tDEEEEEEEEEEEEEE    EEEEEEEEE
                ttttttDEEEEEEEEEEEEEE     EEEEEE
                 ,ttttEEEEEEEEEEEEEEE
                       EEEEEEEEEEEEEE
                    :EEEEEEEEEEEEEEEL
                 :EEEEEEEEEEEEEEEEEDD.
              . EEEEEEE E EEEEEEDEEEtEE
              ,.DEEEE EE     jEE. EEE:EE
              ,, EELEEE           EEEE EEL
              :,, .EEt            ;EEEE;EE,.
                 ,,.               EED ,.,,,,
                 :,                i.,,,,,,,
                                     ,,,,,,:
                                     ,,, :,
                                       ,,,
                                     ,,,,

UNICORN;
        $output->writeLn($unicorn, OutputInterface::OUTPUT_RAW);
    }

}
