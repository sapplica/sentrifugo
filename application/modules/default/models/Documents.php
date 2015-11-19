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

class Default_Model_Documents extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_pd_documents';
	private $db;

	public function init()
	{
		$this->db = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function getPrivileges($userGroup, $userRole,$userPrivilege)
	{
		try
		{
			$qry = 'SELECT m.*,mp.addpermission,mp.editpermission,mp.deletepermission,mp.viewpermission,mp.uploadattachments,mp.viewattachments 
				FROM main_menu m,main_privileges mp 
                WHERE m.isactive in (1,2) AND m.id=mp.object AND mp.isactive = 1 AND mp.role = '.$userRole;
			if($userGroup)		
				$qry .= ' AND mp.group_id = '.$userGroup;
			else
				$qry .= ' AND mp.group_id IS NULL';

			$qry.= ' AND mp.object = '.MANAGE_POLICY_DOCS.' AND m.parent IS NOT NULL ORDER BY m.parent,m.menuOrder';

			$res = $this->db->query($qry);

			if(!empty($res)) 
			{
				$res = $res->fetch();
				$permission = $res[$userPrivilege];
				return $permission;			
			}
			return 'error';
		}
		catch(Exception $e)
		{
			//print_r($e);
		}
	}

	public function getDocumentsCount($categoryId = '')
	{
		try
		{
			$where = 'd.isactive = 1 AND c.isactive = 1 ';
			if($categoryId)
				$where .= 'AND d.category_id = '.$categoryId;

			$res = $this->select()
					->from(array('d' => 'main_pd_documents'),'COUNT(d.id) as cnt')
					->joinInner(array('c' => 'main_pd_categories'),'c.id = d.category_id',array('c.category'))
					->where($where)
					->order('d.document_name');

			
			return $this->fetchAll($res)->toArray();
		}
		catch(Exception $e)
		{
			//print_r($e);
		}
	}
	public function getDocumentsByCategory($columns,$sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "d.isactive = 1 AND c.isactive = 1 ";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		if(empty($by) && empty($sort)) 
		{
			$by = 'd.document_name';
			$sort = 'ASC';
		}
		$res = $this->select()
			->setIntegrityCheck(false)
			->from(array('d' => 'main_pd_documents'),$columns)
			->joinInner(array('c' => 'main_pd_categories'),'c.id = d.category_id',array())
			->where($where)
			->order("$by $sort")
			->limitPage($pageNo, $perPage);
		
		return $res;
	}

	public function getDocumentsGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
	{
		$searchQuery = '';
        $searchArray = array();
        $data = array();

		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				$searchQuery .= " d.".$key." like '%".$val."%' AND ";
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");					
		}
		if(!empty($a) && !empty($searchQuery))
			$searchQuery .= ' AND d.category_id = '.$a;
		else if(!empty($a) && empty($searchQuery))
			$searchQuery = ' d.category_id = '.$a;
		
		$columns = 'd.*';	
		$objName = 'policydocuments';
		
		$tableFields = array('action'=>'Action','document_name'=>'Document','description' => 'Description','document_version' => 'Version','file_name' => 'Uploaded Document');
		
		$tablecontent = $this->getDocumentsByCategory($columns,$sort, $by, $pageNo, $perPage,$searchQuery);     

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

	public function savePolicyDocument($data,$where='')
	{
		if($where)
		{
			$this->update($data,$where);
			return 'update';
		}
		else
		{
			$this->insert($data);
			$id = $this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}

	public function getDocumentsById($docId)
	{
		$where = "d.isactive = 1 AND c.isactive = 1 ";
		if($docId) $where .= " AND d.id = ".$docId;	

		$res = $this->select()
			->setIntegrityCheck(false)
			->from(array('d' => 'main_pd_documents'),'d.*')
			->joinInner(array('c' => 'main_pd_categories'),'c.id = d.category_id',array('c.category'))
			->where($where);
		
		$tmp = $this->fetchAll($res)->toArray();
		if(!empty($tmp))
		{
			return $tmp = $tmp[0];
		}
	}
	public function getCategoryById($id)
	{
		try
		{
			$where = 'c.id = '.$id.' AND c.isactive = 1';
			$res = $this->select()
					->setIntegrityCheck(false)
					->from(array('c' => 'main_pd_categories'),array('c.id','c.isused','c.category','c.description'))
					->where($where);

			$tmp = $this->fetchAll($res)->toArray();
			if(!empty($tmp))
			{
				return $tmp = $tmp[0];
			}
		}
		catch(Exception $e)
		{
			//print_r($e);
		}
	}	

	public function getCategoryByDocId($docId)
	{
		$where = "d.isactive = 1 AND c.isactive = 1 ";
		if($docId) $where .= " AND d.id = ".$docId;	

		$res = $this->select()
			->setIntegrityCheck(false)
			->from(array('d' => 'main_pd_documents'),'')
			->joinInner(array('c' => 'main_pd_categories'),'c.id = d.category_id',array('c.id','c.category'))
			->where($where);
		
		$tmp = $this->fetchAll($res)->toArray();
		if(!empty($tmp))
		{
			return $tmp = $tmp[0];
		}
		return false;
	}


}
?>