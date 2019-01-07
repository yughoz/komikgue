<?php
/*****************************
 *
 * RouterOS PHP API class v1.6
 * Author: Denis Basta
 * Contributors:
 *    Nick Barnes
 *    Ben Menking (ben [at] infotechsc [dot] com)
 *    Jeremy Jefferson (http://jeremyj.com)
 *    Cristian Deluxe (djcristiandeluxe [at] gmail [dot] com)
 *    Mikhail Moskalev (mmv.rus [at] gmail [dot] com)
 *
 * http://www.mikrotik.com
 * http://wiki.mikrotik.com/wiki/API_PHP_class
 *
 ******************************/
namespace App\library ;

class yuuLTElib
{
    var $debug     = false; //  Show debug information
    /* Check, can be var used in foreach  */
    public function test($var)
    {
        return "test : ".$var;
    }

    function checkAccess($module,$action = "")
    {
        $dataSession = Session::get('moduleACC');
        // $dataSessionUser = ;

        if (in_array( Session::get('dataAPL.id'),config('adminlte.users_dev'))) {
            return true;
       }
        
        if (!empty($action)) {
            if (empty($dataSession[$module]->$action)) {
                return false;
            }
        } else {
            if (empty($dataSession[$module])) {
                return false;
            }
        }
        return true;
    }


}
