<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category    ZendX
 * @package     ZendX_JQuery
 * @subpackage  View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 * @version     $Id: Slider.php 20165 2010-01-09 18:57:56Z bkarwin $
 */

/**
 * @see ZendX_JQuery_View_Helper_UiWidget
 */
require_once "ZendX/JQuery/View/Helper/UiWidget.php";

/**
 * jQuery Slider View Helper
 *
 * @uses 	   Zend_Json
 * @package    ZendX_JQuery
 * @subpackage View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendX_JQuery_View_Helper_Slider extends ZendX_JQuery_View_Helper_UiWidget
{
    /**
     * Create jQuery slider that updates its values into a hidden form input field.
     *
     * @link   http://docs.jquery.com/UI/Slider
     * @param  string $id
     * @param  string $value
     * @param  array  $params
     * @param  array  $attribs
     * @return string
     */
    public function slider($id, $value = null, array $params = array(), array $attribs = array())
    {
        if(!isset($attribs['id'])) {
            $attribs['id'] = $id;
        }

        $jqh = ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();

        $params = $this->initializeStartingValues($value, $params);
        $handleCount = $this->getHandleCount($params);

        // Build the Change/Update functionality of the Slider via javascript, updating hidden fields. aswell as hidden fields
        $hidden = "";
        if(!isset($params['change'])) {
            $sliderUpdateFn = 'function(e, ui) {'.PHP_EOL;
            for($i = 0; $i < $handleCount; $i++) {
                // Js Func
                if($i === 0) {
                    $sliderHiddenId = $attribs['id'];
                } else {
                    $sliderHiddenId = $attribs['id']."-".$i;
                }
                $sliderUpdateFn .= $this->getChangeCallback($jqh, $sliderHiddenId, $attribs['id'], $i);

                // Hidden Fields
                $startValue = $this->getHandleValue($i, $params);
                $hiddenAttribs = array('type' => 'hidden', 'id' => $sliderHiddenId, 'name' => $sliderHiddenId, 'value' => $startValue);
                $hidden .= '<input' . $this->_htmlAttribs($hiddenAttribs) . $this->getClosingBracket(). PHP_EOL;
            }
            $sliderUpdateFn .= "}".PHP_EOL;
            $params['change'] = new Zend_Json_Expr($sliderUpdateFn);
        }

        $attribs['id'] .= "-slider";

        if(count($params) > 0) {
            $params = ZendX_JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js = sprintf('%s("#%s").slider(%s);', $jqh, $attribs['id'], $params);
        $this->jquery->addOnLoad($js);

        $html = '<div' . $this->_htmlAttribs($attribs) . '>';
        for($i = 0; $i < $handleCount; $i++) {
            $html .= '<div class="ui-slider-handle"></div>';
        }
        $html .= '</div>';

        return $hidden.$html;
    }

    protected function getChangeCallback($jqh, $sliderHiddenId, $elementId, $handlerNum)
    {
        if(version_compare($this->jquery->getUiVersion(), "1.7.0") >= 0) {
            return sprintf('    %s("#%s").attr("value", %s("#%s-slider").slider("values", %d));'.PHP_EOL,
                $jqh, $sliderHiddenId, $jqh, $elementId, $handlerNum
            );
        } else {
            return sprintf('    %s("#%s").attr("value", %s("#%s-slider").slider("value", %d));'.PHP_EOL,
                $jqh, $sliderHiddenId, $jqh, $elementId, $handlerNum
            );
        }
    }

    protected function getHandleCount($params)
    {
        if(version_compare($this->jquery->getUiVersion(), "1.7.0") >= 0) {
            return count($params['values']);
        } else {
            return count($params['handles']);
        }
    }

    protected function getHandleValue($handleNum, $params)
    {
        if(version_compare($this->jquery->getUiVersion(), "1.7.0") >= 0) {
            return $params['values'][$handleNum];
        } else {
            return $params['handles'][$handleNum]['start'];
        }
    }

    protected function initializeStartingValues($value, $params)
    {
        $values = array();
        if(isset($params['value'])) {
            $values[] = $params['value'];
            unset($params['value']);
        } else if(isset($params['values'])) {
            $values = $params['values'];
            unset($params['values']);
        } else if(isset($params['handles'])) {
            for($i = 0; $i < count($params['handles']); $i++) {
                $values[] = $params['handles'][$i]['start'];
            }
            unset($params['handles']);
        } else if(isset($params['startValue'])) {
            $values[] = $params['startValue'];
            unset($params['startValue']);
        } else if(is_numeric($value)) {
            $values[] = $value;
        }

        if(version_compare($this->jquery->getUiVersion(), "1.7.0") >= 0) {
            $params['values'] = $values;
        } else {
            $params['handles'] = array();
            for($i = 0; $i < count($values); $i++) {
                $params['handles'][$i]['start'] = $values[$i];
            }
        }
        return $params;
    }
}