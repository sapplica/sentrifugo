<?php
/**
 * dateTimePicker
 * @author Darius Matulionis
 */
/**
 * @see Zend_Registry
 */
require_once "Zend/Registry.php";
require_once '../public/constants.php';

/**
 * @see ZendX_JQuery_View_Helper_UiWidget
 */
require_once "ZendX/JQuery/View/Helper/UiWidget.php";

class ZendX_JQuery_View_Helper_DateTimePicker extends ZendX_JQuery_View_Helper_UiWidget
{

    public function dateTimePicker($id, $value = null, array $params = array(), array $attribs = array())
    {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);

        $params2 = ZendX_JQuery::encodeJson($params);

        $pr = array();
        foreach ($params as $key => $val){
            $pr[] = '"'.$key.'":'.ZendX_JQuery::encodeJson ( $val );
        }
        $pr = '{'.implode(",", $pr).'}';

        $js = sprintf('%s("#%s").datetimepicker(%s);',
                ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
                $attribs['id'],
                $pr
        );

        $this->jquery->addOnLoad($js);
        $this->jquery->addJavascriptFile(MEDIA_PATH . 'jquery/js/jquery-ui-timepicker-addon.js');

        return $this->view->formText($id, $value, $attribs);
    }
}

