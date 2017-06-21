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

class Default_Model_Cronstatus extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_cronstatus';
    protected $_primary = 'id';
	
	
    public function SaveorUpdateCronStatusData($data, $where)
    {
        if($where != ''){
                    $this->update($data, $where);
                    return 'update';
            } else {
                    $this->insert($data);
                    $id=$this->getAdapter()->lastInsertId($this->_name);
                    return $id;
            }


    }
    
    public function getActiveCron($cron_type)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select count(*) cnt from ".$this->_name." where cron_status = 1 and cron_type = '".$cron_type."'";
        $result = $db->query($query);
        $row = $result->fetch();
        if($row['cnt'] >0)
            $status = "no";
        else 
            $status = "yes";
        return $status;
    }        
}
?>