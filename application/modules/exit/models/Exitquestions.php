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
class Exit_Model_Exitquestions extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_exit_questions';
	private $db;

	public function init()
	{
		$this->db = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addExittype($data)
	{
		
		if(!empty($data))
		{
			$this->insert($data);
			$id = $this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}

	/**
	** function to retrieve exit type
	** @con = 'add' gets active exit type 
	** @con = 'cnt' gets active exit type count
	** @con = 'grid' gets active exit type data
	**/
	public function getExitquestions($con,$sort='', $by='', $pageNo='', $perPage='',$searchQuery='')
	{
		 
		 $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {		
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
     	}
        $where = "eq.isactive = 1 and et.isactive=1";
        if($searchQuery)
            $where .= " AND ".$searchQuery;
       /*  if($loginuserRole != 1)
        {
            if($loginuserGroup != MANAGER_GROUP)
                $where .= " AND (aq.createdby_group in ( ".$loginuserGroup.") or aq.createdby_group is null ) ";
            else 
                $where .= " AND (aq.createdby_group in ( ".$loginuserGroup.") ) ";
        }
		 */
        $db = Zend_Db_Table::getDefaultAdapter();		
		
		$exitQuestionsData = $this->select()
                                ->setIntegrityCheck(false)	
                                ->from(array('eq'=>'main_exit_questions'),array('eq.*'))
                                ->joinInner(array('et'=>'main_exit_types'), 'eq.exit_type_id = et.id', array('exit_type' => 'et.exit_type'))
                                ->where($where)
                                ->order("$by $sort") 
                                ->limitPage($pageNo, $perPage); 
        return $exitQuestionsData;  
	}

	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
	{
		$searchQuery = '';
        $searchArray = array();
        $data = array();
		
		if($searchData != '' && $searchData!='undefined')
			{
				$searchValues = json_decode($searchData);
				foreach($searchValues as $key => $val)
				{
					if(isset($key) && $key == "exit_type")
					{
						$searchQuery .= " et.".$key." like '%".($val)."%' AND ";
					}
					else
					{
						$searchQuery .= " eq.".$key." like '%".($val)."%' AND ";
					}
					$searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'configureexitqs';
		
		$tableFields = array('action'=>'Action','exit_type'=>'Exit type','question'=>'Question','description'=>'Description');
		
		$tablecontent = $this->getExitquestions('grid',$sort, $by, $pageNo, $perPage,$searchQuery);     
		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'add' =>'add',
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
		);
		return $dataTmp;
	}
	public function SaveorUpdateExitQuestionData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_exit_questions');
			return $id;
		}	
	}
	//get questions data based on id
	public function getExitQuestionbyID($id)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
		
		$loginuserRole = $auth->getStorage()->read()->emprole;
		$loginuserGroup = $auth->getStorage()->read()->group_id;
     	}
		$where = 'eq.isactive = 1 AND eq.id='.$id.' ';
		
            /* if($loginuserRole != 1)
            {
                if($loginuserGroup != MANAGER_GROUP)
                    $where .= " AND (aq.createdby_group = ".$loginuserGroup." or aq.createdby_group is null) ";
                else 
                    $where .= " AND (aq.createdby_group = ".$loginuserGroup." ) ";
            }
			 */
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('eq'=>'main_exit_questions'),array('eq.*'))
					    ->where($where);
		return $this->fetchAll($select)->toArray();	
	}
	//get questions related to exit type
	public function getExitQuestionsByexitId($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('eq'=>'main_exit_questions'),array('eq.*'))
					    ->where('eq.isactive = 1 AND eq.exit_type_id='.$id.' ')
						 ->order('eq.modifieddate DESC') ;
		return $this->fetchAll($select)->toArray();	
	}
		// update questions to isactive=0 when we delete  exit types
	public function UpdateExitQuestionData($data)
	{
       
		$db = Zend_Db_Table::getDefaultAdapter();
	    $qry = "update main_exit_questions set isactive=0 where id in ($data)";
		$db->query($qry);
        
	}
}
?>