<?php
class Assets_Model_Assets extends Zend_Db_Table_Abstract
{
	protected $_name = 'assets';
	protected $_primary = 'id';
	 
	 	public function getAssetsData($sort, $by, $pageNo, $perPage,$searchQuery)
			{
				
				$where = " ac.is_active = 1 AND a.isactive = 1  ";
				if($searchQuery) 
				$where .= " AND ".$searchQuery;
				$db = Zend_Db_Table::getDefaultAdapter();
				
		   $AssetsData = $this->select()
                       ->setIntegrityCheck(false)
                       ->from(array('a'=>'assets'), array('a.*','category_name'=>'ac.name','sub_category_name'=>'ace.name','allocated_name'=>'mu.userfullname'))
                       ->joinLeft(array('ac'=>'assets_categories'),"a.category = ac.id ",array())
					   ->joinLeft(array('ace'=>'assets_categories'),"ace.id = a.sub_category ",array())
					   ->joinleft(array('mu'=>'main_employees_summary'),"a.allocated_to = mu.user_id",array())
					  // ->joinleft(array('mbu'=>'main_businessunits'),"a.location = mbu.id",array())
                       ->where($where)
                       ->order("$by $sort")
                       ->limitPage($pageNo, $perPage);
               return $AssetsData;

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
				$searchValues = json_decode($searchData);
				
				
				if($key == 'category_name'){
					$searchQuery .= " ac.name like '%".$val."%' AND ";
				}
				if($key == 'sub_category_name'){
				
					$searchQuery .= " ace.name like '%".$val."%' AND ";
					
				}if($key == 'allocated_name'){
				
					$searchQuery .= " mu.userfullname like '%".$val."%' AND ";
					
				}else if($key != 'category_name' && $key != 'sub_category_name'){
					
					$searchQuery .= " a.".$key." like '%".$val."%' AND ";
				}
				$searchArray[$key] = $val;
			}
			
			 $searchQuery = rtrim($searchQuery," AND");
		
		}
			
		$objName = 'assets';

		$tableFields = array(
		
					'action'=>'Action',
							'name'  => 'Asset Name',
							'category_name'  => 'Category',
							'sub_category_name'  => 'Sub Category',
							'company_asset_code'=>'Company Asset Code',		
							'is_working'  => 'Working Condition',
							'asset_classification'  => 'Asset Classification',
				            'allocated_name'  => 'Allocated To',
							
							//These Fields are not Displaye
							//'location'  =>	'location',
							//'vendor'  => 'vendor',   
							//'manufacturer'  => 'Manufacturer',
							//'allocated_to'  => 'Allocated to',
							//'responsible_technician'  => 'Responsible',
							
							/*'purchase_date'  => 'purchase_date',
							'invoice_number'  => 'invoice_number',
							'key_number'  => 'key_number',
							'warenty_status'  => 'warenty_status',
							'warenty_end_date'  => 'warenty_end_date',
							'notes'=>'notes' */
		);

		$tablecontent = $this->getAssetsData($sort, $by, $pageNo, $perPage,$searchQuery);

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
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
			'menuName' => 'Assets'
			);
			return $dataTmp;
	}
	
	public function getLocation()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from main_businessunits where isactive = 1 ";
		$result = $db->query($query)->fetchAll(); 
		return $result;
		
	}
	public function getvendorsname()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select name,id from main_vendors where isactive = 1 ";
		$result = $db->query($query)->fetchAll(); 
		return $result;
		
	}
	public function saveOrUpdateAssetsData($data, $where)
	{		
		if($where != '')
			{	
				  $this->update($data, $where);
				return 'update';
			} 
		else
			{
				
				$this->insert($data);
				$id=$this->getAdapter()->lastInsertId('assets');
				return $id;
				
			}
	}
	public function getAssetsDetailsById($id)
	{	
	
	/* 
				
				$where = "ac.is_active = 1";
				if($searchQuery) 
				$where .= " AND ".$searchQuery;
				$db = Zend_Db_Table::getDefaultAdapter();
				
				 $AssetsData = $this->select()
                       ->setIntegrityCheck(false)
                       ->from(array('a'=>'assets'), array('a.*','category_name'=>'ac.name','sub_category_name'=>'ace.name'))
                       ->joinLeft(array('ac'=>'assets_categories'),"a.category = ac.id ",array())
					   ->joinLeft(array('ace'=>'assets_categories'),"ace.parent = ac.id ",array())
                       ->where($where)
                       ->order("$by $sort")
                       ->limitPage($pageNo, $perPage);
               return $AssetsData;

		*/


		 $select = $this->select()
					->setIntegrityCheck(false)
						->from(array('a'=>'assets'),array('a.*','category_name'=>'ac.name','sub_category_name'=>'ace.name','Location'=>'mbu.unitname','AllocatedTo' =>'mu.userfullname','ResponsibleTechnician' =>'mu1.userfullname'))
						->joinLeft(array('ac'=>'assets_categories'),"a.category = ac.id ",array())
						 ->joinLeft(array('ace'=>'assets_categories'),"ace.id = a.sub_category  ",array())
						  ->joinleft(array('mbu'=>'main_businessunits'),"a.location = mbu.id",array())
						   ->joinleft(array('mu'=>'main_employees_summary'),"a.allocated_to = mu.user_id",array())
						   ->joinleft(array('mu1'=>'main_employees_summary'),"a.responsible_technician = mu1.user_id",array())
						->where('ac.is_active = 1 AND a.id='.$id.' '); 
		return $this->fetchAll($select)->toArray();	
	}
	public function getActiveAssetData()
	{
		
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('c'=>$this->_name),array('c.id','c.client_name'))
		->where('c.is_active = 1 ')
		->order('c.client_name');
		return $this->fetchAll($select)->toArray();
	}
	public function saveReceipts($data,$where)
	{
		
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} 
		else 
		{
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('assets');
			return $id;
		}
	}
	public function InsertToHistoryTable($data)
	{
	
		//$query = "insert into assets_history values("$data['id']","$data['allocated_to']")"
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert('assets_history', $data);
		return $id = $db->lastInsertId();
			
	}
	public function getUsernameofAllocatedAsset($id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from assets where isactive = 1 AND  $id= '.$id.'";
		$username = $db->query($query)->fetchAll(); 
		return $username;
		
	}
	public function getAssetHistory($id)
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('c'=>'assets_history'),array('c.*'))
		->joinLeft(array('mc'=>'main_users'),"mc.id = c.createdby ",array('userfullname'=>'mc.userfullname'))
		->where('c.isactive = 1 and asset_id = '.$id);
		return $this->fetchAll($select)->toArray();
	}
public function	isAssetExistForSetting($id){
	
	$db = Zend_Db_Table::getDefaultAdapter();
	$query = "SELECT COUNT(*) as count FROM main_sd_requests_summary WHERE isactive =1 AND service_desk_id=".$id;
	$result = $db->query($query)->fetch();
	return $result['count'];
	
}
public function getAllAssetForCategory($assetcategory_id){
 $assetsData = $this->select()
					->setIntegrityCheck(false)
					->from(array('c'=>$this->_name),array('c.id','c.name'))
					->where('c.isactive = 1 AND c.category='.$assetcategory_id.' ');
		return $this->fetchAll($assetsData)->toArray();
 } 
}