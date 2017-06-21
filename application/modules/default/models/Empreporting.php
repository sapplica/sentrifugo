<?php
/********************************************************************************* 
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
 ********************************************************************************/

class Default_Model_Empreporting extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_emp_reporting';
    protected $_primary = 'id';
    
    
    /**
     * This function is used to get reporting managers for drop down.
     * 
     * @return Array Array of reporting managers id's and names.
     */
    public function getReportingManagersOptions()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = " select distinct er.reporting_manager_id id,e.emp_name name 
                   from main_emp_reporting er inner join main_employees e 
                   on e.id = er.reporting_manager_id and e.isactive = 1
                   where er.isactive = 1";
        $result = $db->query($query);
        $emp_options = array();
        while($row = $result->fetch())
        {
            $emp_options[$row['id']] = $row['name'];
        }
        return $emp_options;
    }
    
}//end of class