<?php

namespace App\Services;
//use phpDocumentor\Reflection\Types\Integer;

class TestService {

    private $kyrs = 60;

    public function convert($rub) {
	return $rub/$this->kyrs;
    }

}

class LiFlowServices {
    
    public function check_password($my_cluster, $my_user, $my_passwd)
    {
        if ($my_cluster == "imm") $command = 'sshpass -p "' . $my_passwd . '" ssh -o StrictHostKeyChecking=no ' . $my_user . '@umt.imm.uran.ru "/usr/bin/hostname"';
        else $command = 'sshpass -p "' . $my_passwd . '" ssh -p 2222 -o StrictHostKeyChecking=no ' . $my_user . '@cluster.alexbers.com "/usr/bin/hostname"';
        
        exec($command, $arr);

        if ($my_cluster == "imm")
        {
            if ($arr[0] != "umt.imm.uran.ru") return 1;
            else return 0;
        }
        else
        {
            if ($arr[0] != "headnode.imkn") return 1;
            else return 0;
        }
    }
     
}

?>