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

class Default_Model_Candidateworkdetails extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_candworkdetails';
    protected $_primary = 'id';
    
    /**
     * This function gives data for grid view.
     * @parameters
     * @param $sort          = ascending or descending
     * @param $by            = name of field which to be sort  
     * @param $pageNo        = page number
     * @param $perPage       = no.of records per page
     * @param $searchQuery   = search string
     * 
     * @return  ResultSet;
     */
    public function getCandidatesData($sort, $by, $pageNo, $perPage,$searchQuery)
    {
        $where = "c.isactive = 1";

        if($searchQuery)
            $where .= " AND ".$searchQuery;       

        $roleData = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('c'=>$this->_name),array('c.*',))
                        
                        ->where($where)
                        ->order("$by $sort") 
                        ->limitPage($pageNo, $perPage);        
        return $roleData;       		
    }
    
    /**
     * This function is used to save/update data in database.
     * @parameters
     * @param Array $data  =  array of form data.
     * @param String $where =  where condition in case of update.
     * 
     * @return String  Primary id when new record inserted,'update' string when a record updated.
     */
    public function SaveorUpdateCandidateWorkData($data, $where)
    {
        if($where != '')
        {
            $this->update($data, $where);
            return 'update';
        }
        else 
        {
            $this->insert($data);
            $id=$this->getAdapter()->lastInsertId($this->_name);
            return $id;
        }
    }
    /**
     * This function is used to get table name.
     * @return String Name of table.
     */
    public function getTableName()
    {
        return $this->_name;
    }
    
	public function getcandidateworkData($id)
	{	
            $db = Zend_Db_Table::getDefaultAdapter();	
                
            $workData = $db->query("select * from main_candworkdetails where isactive = 1 AND cand_id=".$id.";");
            if (!$workData) 
            {
                throw new Exception("Problem in cnadidate work details model");
            }
            return $data = $workData->fetchAll();
	}
}//end of class