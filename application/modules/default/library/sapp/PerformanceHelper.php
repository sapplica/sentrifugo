<?php

/* * ******************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
 *   
 *  Sentrifugo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Sentrifugo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Sentrifugo.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  Sentrifugo Support <support@sentrifugo.com>
 * ****************************************************************************** */

/**
 * Description of PerformanceHelper
 *
 * @author ramakrishna
 */
class sapp_PerformanceHelper 
{
   
    public static function check_per_implmentation($businessunit_id,$department_id)
    {
        $output = array();
        if($businessunit_id != '' && $department_id != '')
        {
            $model = new Default_Model_Appraisalinit();
            $output = $model->check_per_implmentation($businessunit_id, $department_id);
        }
        return $output;
    }
    /*
    $query = "select id,performance_app_flag,appraisal_ratings from main_pa_implementation "
            . "where businessunit_id = ".$businessUnit." and isactive = 1";
$query = "select id,performance_app_flag,appraisal_ratings from main_pa_implementation "
        . "where businessunit_id = ".$businessUnit." and department_id = ".$department." and isactive = 1";
*/
    //put your code here
}
