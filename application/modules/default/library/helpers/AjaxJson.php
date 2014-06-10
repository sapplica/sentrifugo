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

/**
 *
 * @author Manjunath
 * @version
 */
require_once 'Zend/View/Interface.php';

/**
 * AjaxJson helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_AjaxJson extends Zend_View_Helper_Abstract
{

	/**
	 * @var Zend_View_Interface
	 */
	public $view;

	/**
	 *
	 */
	public function ajaxJson($data, $keepLayouts = false) {
            
		$options = array();
		if (is_array($keepLayouts))
		{
			$options     = $keepLayouts;
			$keepLayouts = (array_key_exists('keepLayouts', $keepLayouts))
			? $keepLayouts['keepLayouts']
			: false;
			unset($options['keepLayouts']);
		}

		$data = Zend_Json::encode($data, null, $options);
		if (!$keepLayouts) {
			require_once 'Zend/Layout.php';
			$layout = Zend_Layout::getMvcInstance();
			if ($layout instanceof Zend_Layout) {
				$layout->disableLayout();
			}
		}

		$response = Zend_Controller_Front::getInstance()->getResponse();
		$response->setHeader('Content-Type', 'text/html', true);
		return $data;
	}

	
}
