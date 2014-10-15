<?php
namespace NethServer\Module\Dashboard;

/*
 * Copyright (C) 2013 Nethesis S.r.l.
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Retrieve system release reading /etc/nethserver-release
 *
 * @author Giacomo Sanchietti
 */
class Nut extends \Nethgui\Controller\AbstractController
{

    public $sortId = 30;
 
    private function readStatus() 
    {
        $status = $this->getPlatform()->getDatabase('configuration')->getProp('ups','status');
        if ($status == 'disabled') {
            return;
        }
        $mode = $this->getPlatform()->getDatabase('configuration')->getProp('ups','Mode');
        $ups = $this->getPlatform()->getDatabase('configuration')->getProp('ups','Ups');

        if ( ! $ups) {
            $ups = "UPS";
        }

        if($mode === 'master') {
            $server = 'localhost';
        } else {
            $server = $this->getPlatform()->getDatabase('configuration')->getProp('ups','Master');
        }

        $process = $this->getPlatform()->exec('/usr/bin/nc -w 1 -z ${1} 3493 &>/dev/null && /usr/bin/upsc ${2}', array($server, "${ups}@${server}"));

        $status = array();
        if($process->getExitCode() === 0) {
            foreach ($process->getOutputArray() as $line) {
                $tmp = explode(": ",$line);
                $status[$tmp[0]] = $tmp[1];
            }
        } 
        return $status;
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $view['status'] = $this->readStatus();
    }
}
