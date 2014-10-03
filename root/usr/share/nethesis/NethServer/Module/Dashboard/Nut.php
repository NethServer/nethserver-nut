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
 
    private $status = "";

    private function readStatus() 
    {
        $status = $this->getPlatform()->getDatabase('configuration')->getProp('ups','status');
        if ($status == 'disabled') {
            return;
        }
        $mode = $this->getPlatform()->getDatabase('configuration')->getProp('ups','Mode');
        $ups = $this->getPlatform()->getDatabase('configuration')->getProp('ups','Ups');
        if (!$ups) {
            $ups = "UPS";
        }
        if ($mode === "master") {
            $cmd = "/usr/bin/upsc $ups@localhost";
        } else {
            $server = $this->getPlatform()->getDatabase('configuration')->getProp('ups','Master');
            $exitc = $this->getPlatform()->exec("/usr/bin/nc $server 3849")->getExitCode();
            if ($exitc == 0) {
                $cmd = "/usr/bin/upsc $ups@$server";
            } else {
                return;
            }
        }

        $output = $this->getPlatform()->exec($cmd)->getOutputArray();
        foreach ($output as $line) {
            $tmp = explode(": ",$line);
            $this->status[$tmp[0]] = $tmp[1];
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        if (!$this->status) {
            $this->readStatus();
        }

        $view['status'] = $this->status;
    }
}
