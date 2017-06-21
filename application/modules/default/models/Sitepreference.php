<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2015 Sapplica
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

class Default_Model_Sitepreference extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_sitepreference';
    protected $_primary = 'id';
	
	public function getSystemPreferenceData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "s.isactive = 1 AND d.isactive=1 AND tm.isactive=1 AND c.isactive=1 AND pw.isactive=1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$positionData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('s'=>'main_sitepreference'),array('s.*'))
						  
                           ->joinLeft(array('d'=>'main_dateformat'), 'd.id=s.dateformatid',array('d.dateformat')) 
                           ->joinLeft(array('tm'=>'main_timeformat'), 'tm.id=s.timeformatid',array('tm.timeformat'))
                           
                           ->joinLeft(array('c'=>'main_currency'), 'c.id=s.currencyid',array('currency'=>'concat(c.currencyname," ",c.currencycode)'))
                           ->joinLeft(array('pw'=>'tbl_password'), 'pw.id=s.passwordid',array('pw.passwordtype')) 						   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $positionData;       		
	}
	public function getsingleSystemPreferanceData($id)
	{
		$row = $this->fetchRow("id = '".$id."' and isactive = 1");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function SitePreferanceData()
	{
	   $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'main_sitepreference'),array('s.*'))
					    ->where('s.isactive = 1');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateSystemPreferanceData($data, $where)
	{
		
	    if($where != ''){
	    	
		$this->update($data, $where);
		
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_sitepreference');
			return $id;
		}
		
	
	}
	
	public function getPasswordData()
	{
	   $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('p'=>'tbl_password'),array('p.*'))
					    ->where('p.isactive = 1');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getSinglePasswordData($id)
	{
	   $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('p'=>'tbl_password'),array('p.*'))
					    ->where('p.id='.$id.' AND p.isactive = 1');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getActiveRecord()
	{
	    $select =  $this->select()
                            ->setIntegrityCheck(false)	
                            ->from(array('s'=>'main_sitepreference'),array('s.*'))
                            ->joinLeft(array('d'=>'main_dateformat'), 'd.id=s.dateformatid AND d.isactive=1',array('date_description'=>'d.description','date_example'=> 'd.example','date_format'=>'d.dateformat','mysql_dateformat'=>'d.mysql_dateformat','js_dateformat'=>'d.js_dateformat')) 
                            ->joinLeft(array('tm'=>'main_timeformat'), 'tm.id=s.timeformatid AND tm.isactive=1',array('time_description'=>'tm.description','time_example'=> 'tm.example','time_format'=>'tm.timeformat'))
                            ->joinLeft(array('c'=>'main_currency'), 'c.id=s.currencyid AND c.isactive=1',array('currency'=>'concat(c.currencyname,"(",c.currencycode,")")'))							
                            ->joinLeft(array('z'=>'main_timezone'), 'z.id=s.timezoneid AND z.isactive=1',array('tz_value' => 'z.timezone','timezone'=>'concat(z.timezone," [",z.timezone_abbr,"]")'))
                            ->joinLeft(array('pw'=>'tbl_password'), 'pw.id=s.passwordid AND pw.isactive=1',array('pw.passwordtype','pwddescription'=>'pw.description')) 						   
                            ->where('s.isactive = 1');
    	  
		return $this->fetchAll($select)->toArray();   
	
	}
}