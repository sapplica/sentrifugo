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
class Exit_Model_Questionsresponse extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_exit_questions_response';
	private $db;
	
	public function init()
	{
		$this->db = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function SaveorUpdateQuestionsData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_exit_questions_response');
			return $id;
		}	
	}
	//get the data based on user_id and process id 
	public function getResponseData($empid,$process_id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('eq'=>$this->_name),array('eq.*'))
					    ->where('eq.isactive = 1 AND eq.user_id='.$empid.' AND eq.exit_initiation_id='.$process_id);
		return $this->fetchAll($select)->toArray();	
	}

	//function to get employee question ids
	public function getEmployeeQuestionsData($emp_id,$process_id){
		$select = $this->select()
					->setIntegrityCheck(false)	
                    ->from(array('eqr'=>'main_exit_questions_response'),array('eqr.*'))
                    ->where("eqr.isactive = 1 AND eqr.exit_initiation_id = ".$process_id." AND eqr.user_id = ".$emp_id);
                           
		return $this->fetchAll($select)->toArray();    
	}
	//function to get employee questions data
	public function getEmployeeQuestions($question_ids)
	{
		$select = $this->select()
				   ->setIntegrityCheck(false)	
				   ->from(array('eq'=>'main_exit_questions'),array('eq.id','eq.question','eq.description'))
				   ->where("eq.isactive = 1 AND eq.id in (".$question_ids.")");
                           
		return $this->fetchAll($select)->toArray();  
	}
}


?>