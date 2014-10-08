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

class Services_Bootstrap extends Zend_Application_Module_Bootstrap
{
	//adding this function in Bootstrap class to initilize Zend_Rest_Route.
	 protected function _initRestRoute() {
	  //getting an instance of zend front controller.
	  $frontController = Zend_Controller_Front::getInstance ();
	  //initializing a Zend_Rest_Route
	  $restRoute = new Zend_Rest_Route ( $frontController, array(), array('services') );
	  //let all actions to use Zend_Rest_Route.
	  $frontController->getRouter ()->addRoute ( 'services', $restRoute );
	 }
	
}

