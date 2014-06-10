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

class Default_Model_Interviewrounddetails extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_interviewrounddetails';
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
    public function getInterviewRoundsData($sort, $by, $pageNo, $perPage,$searchQuery,$id)
    {
        $where = "ir.isactive = 1 and ir.interview_id = ".$id;

        if($searchQuery)
            $where .= " AND ".$searchQuery;       

        $irData = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('ir'=>$this->_name),array('ir.*','interview_date' =>"date_format(interview_date,'".DATEFORMAT_MYSQL."')"))                        
                        ->joinInner(array('u'=>'main_users'), "u.id = ir.interviewer_id and u.isactive = 1",array('userfullname'=>'u.userfullname'))
                        ->where($where)
                        ->order("$by $sort") 
                        ->limitPage($pageNo, $perPage);        
		return $irData;       		
    }
    
    public function getinterviewstatus($intid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
        $query = "select * from main_interviewdetails 
                  where isactive = 1 and id = ".$intid;
        $result = $db->query($query);
        return $row = $result->fetch();       
	}
    /**
     * This function is used to save/update data in database.
     * @parameters
     * @param Array $data  =  array of form data.
     * @param String $where =  where condition in case of update.
     * 
     * @return String Primary id when new record inserted,'update' string when a record updated.
     */
    public function SaveorUpdateInterviewroundData($data, $where)
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
    /**
     * This function is used get count of rounds of particular candidate.
     * @param Integer $cand_id = id of candidate.
     * @return Integer count of rounds.
     */
    public function getRoundCnt($cand_id,$interview_id)
    {
        if($cand_id != '' && $interview_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select count(*) cnt from main_interviewrounddetails 
                      where isactive = 1 and interview_id ='".$interview_id."' and candidate_id = ".$cand_id;
            $result = $db->query($query);
            $row = $result->fetch();
            return $row['cnt'];
        }
        else return '0';
    }
	
    public function getSingleRoundData($id)
    {

            $row = $this->fetchRow("id = '".$id."'");
            if (!$row) {
                    throw new Exception("Could not find row $id");
            }
            return $row->toArray();
    }
    public function getRoundDetailsByCandidateId($cand_id)
    {
        $data = array();
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select ir.*,u.userfullname from main_interviewrounddetails ir,main_users u 
                  where ir.candidate_id = ".$cand_id." and ir.isactive = 1 
                  and u.id = ir.interviewer_id and u.isactive = 1 order by ir.interview_round_number";
        $result = $db->query($query);
        $data = $result->fetchAll();
        return $data;
    }
    public function getMaxRoundDateByInterviewId($inter_id)
    {
        $max_date = '';
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select max(interview_date) mx 
                  from main_interviewrounddetails 
                  where interview_id = ".$inter_id."  and isactive = 1";
        $result = $db->query($query);
        $row = $result->fetch();
        $max_date = $row['mx'];
        return $max_date;
    }
    public function getFutureRoundCnt($interview_id,$round_number)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select count(*) cnt 
                  from main_interviewrounddetails 
                  where interview_id = ".$interview_id." and interview_round_number > ".$round_number;
        $result = $db->query($query);
        $row = $result->fetch();
        $cnt = $row['cnt'];
        return $cnt;
    }
}//end of class