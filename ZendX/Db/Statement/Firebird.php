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
 * @category   ZendX
 * @package    ZendX_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/**
 * @see Zend_Db_Statement
 */
require_once 'Zend/Db/Statement.php';

/**
 * Extends for Firebird
 *
 * @category   ZendX
 * @package    ZendX_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendX_Db_Statement_Firebird extends Zend_Db_Statement
{

    /**
     * The firebird_stmtPrepared resource.
     *
     * @var firebird_stmtPrepared
     */
    protected $_stmtPrepared = null;

    /**
     * The firebird_stmtResult resource.
     *
     * @var firebird_result
     */
    protected $_stmtResult = null;

    /**
     * The firebird_stmtResult resource.
     *
     * @var firebird_result
     */
    protected $_stmtRowCount = 0;

    /**
     * The firebird_stmtResult resource.
     *
     * @var firebird_result
     */
    protected $_stmtColumnCount = 0;

    /**
     * Column names.
     *
     * @var array
     */
    protected $_keys = array();

    /**
     * Fetched result values.
     *
     * @var array
     */
    protected $_values = array();

    /**
     * @var array
     */
    protected $_meta = null;

    /**
     * @param  string $sql
     * @return void
     * @throws ZendX_Db_Statement_Firebird_Exception
     */
    public function _prepare($sql)
    {
        $this->_stmtRowCount = 0;
        $this->_stmtColumnCount = 0;

        $connection = $this->_adapter->getConnection();

        if ($trans = $this->_adapter->getTransaction())
            $this->_stmtPrepared = @ibase_prepare($connection, $trans, $sql);
        else
            $this->_stmtPrepared = @ibase_prepare($connection, $sql);

        if ($this->_stmtPrepared === false) {
            /**
             * @see ZendX_Db_Statement_Firebird_Exception
             */
            require_once 'ZendX/Db/Statement/Firebird/Exception.php';
            throw new ZendX_Db_Statement_Firebird_Exception("Firebird prepare error: " . ibase_errmsg());
        }
    }

    /**
     * Binds a parameter to the specified variable name.
     *
     * @param mixed $parameter Name the parameter, either integer or string.
     * @param mixed $variable  Reference to PHP variable containing the value.
     * @param mixed $type      OPTIONAL Datatype of SQL parameter.
     * @param mixed $length    OPTIONAL Length of SQL parameter.
     * @param mixed $options   OPTIONAL Other options.
     * @return bool
     * @throws ZendX_Db_Statement_Firebird_Exception
     */
    protected function _bindParam($parameter, &$variable, $type = null, $length = null, $options = null)
    {
        return true;
    }

    /**
     * Closes the cursor and the statement.
     *
     * @return bool
     */
    public function close()
    {
        if ($stmt = $this->_stmtResult) {
            @ibase_free_result($this->_stmtResult);
            $this->_stmtResult = null;
        }

        if ($this->_stmtPrepared) {
            $r = @ibase_free_query($this->_stmtPrepared);
            $this->_stmtPrepared = null;
            return $r;
        }
        return false;
    }

    /**
     * Closes the cursor, allowing the statement to be executed again.
     *
     * @return bool
     */
    public function closeCursor()
    {
        if ($stmt = $this->_stmtResult) {
            return @ibase_free_result($this->_stmtResult);
        }
        return false;
    }

    /**
     * Returns the number of columns in the result set.
     * Returns null if the statement has no result set metadata.
     *
     * @return int The number of columns.
     */
    public function columnCount()
    {
        return $this->_stmtColumnCount ? $this->_stmtColumnCount : 0;
    }

    /**
     * Retrieves the error code, if any, associated with the last operation on
     * the statement handle.
     *
     * @return string error code.
     */
    public function errorCode()
    {
        if ($this->_stmtPrepared || $this->_stmtResult) {
            return ibase_errcode();
        }
        return false;        
    }

    /**
     * Retrieves an array of error information, if any, associated with the
     * last operation on the statement handle.
     *
     * @return array
     */
    public function errorInfo()
    {
        if (!$this->_stmtPrepared) {
            return false;
        }
        return array(
            ibase_errcode(),
            ibase_errmsg()
        );
    }

    /**
     * Executes a prepared statement.
     *
     * @param array $params OPTIONAL Values to bind to parameter placeholders.
     * @return bool
     * @throws ZendX_Db_Statement_Firebird_Exception
     */
    public function _execute(array $params = null)
    {
        if (!$this->_stmtPrepared) {
            return false;
        }

        // if no params were given as an argument to execute(),
        // then default to the _bindParam array
        if ($params === null) {
            $params = $this->_bindParam;
        }
        // send $params as input parameters to the statement
        if ($params) {
            array_unshift($params, $this->_stmtPrepared);
            $retval = @call_user_func_array(
                'ibase_execute',
                $params
            );
        } else
            // execute the statement
            $retval = @ibase_execute($this->_stmtPrepared);
        $this->_stmtResult = $retval;

        if ($retval === false) {
            $last_error = ibase_errmsg();
            $this->_stmtRowCount = 0;
        }        
        
        //Firebird php ibase extension, auto-commit is not after each call, but at
        //end of script. Disabled when transaction is active
        if (!$this->_adapter->getTransaction())
            ibase_commit_ret();
            
        if ($retval === false) {
            /**
             * @see ZendX_Db_Statement_Firebird_Exception
             */
            require_once 'ZendX/Db/Statement/Firebird/Exception.php';
            throw new ZendX_Db_Statement_Firebird_Exception("Firebird statement execute error : " . $last_error);
        }               

        // statements that have no result set do not return metadata
        if (is_resource($this->_stmtResult)) {

            // get the column names that will result
            $this->_keys = array();
            $coln = ibase_num_fields($this->_stmtResult);
            $this->_stmtColumnCount = $coln;
            for ($i = 0; $i < $coln; $i++) {
                $col_info = ibase_field_info($this->_stmtResult, $i);
                $this->_keys[] = $this->_adapter->foldCase($col_info['name']);
            }

            // set up a binding space for result variables
            $this->_values = array_fill(0, count($this->_keys), null);

            // set up references to the result binding space.
            // just passing $this->_values in the call_user_func_array()
            // below won't work, you need references.
            $refs = array();
            foreach ($this->_values as $i => &$f) {
                $refs[$i] = &$f;
            }
        }

        if ($trans = $this->_adapter->getTransaction())
            $this->_stmtRowCount = ibase_affected_rows($trans);
        else
            $this->_stmtRowCount = ibase_affected_rows($this->_adapter->getConnection());
        return true;
    }

    /**
     * Fetches a row from the result set.
     *
     * @param int $style  OPTIONAL Fetch mode for this fetch operation.
     * @param int $cursor OPTIONAL Absolute, relative, or other.
     * @param int $offset OPTIONAL Number for absolute or relative cursors.
     * @return mixed Array, object, or scalar depending on fetch mode.
     * @throws Zend_Db_Statement_Exception
     */
    public function fetch($style = null, $cursor = null, $offset = null)
    {
        if (!$this->_stmtResult) {
            return false;
        }

        if ($style === null) {
            $style = $this->_fetchMode;
        }
        
        switch ($style) {
            case Zend_Db::FETCH_NUM:
                $row = ibase_fetch_row($this->_stmtResult, IBASE_TEXT);
                break;
            case Zend_Db::FETCH_ASSOC:
                $row = ibase_fetch_assoc($this->_stmtResult, IBASE_TEXT);
                break;
            case Zend_Db::FETCH_BOTH:
                $row = ibase_fetch_assoc($this->_stmtResult, IBASE_TEXT);
                if ($row !== false)
                    $row = array_merge($row, array_values($row));
                break;
            case Zend_Db::FETCH_OBJ:
                $row = ibase_fetch_object($this->_stmtResult, IBASE_TEXT);
                break;
            case Zend_Db::FETCH_BOUND:
                $row = ibase_fetch_assoc($this->_stmtResult, IBASE_TEXT);
                if ($row !== false){
                    $row = array_merge($row, array_values($row));
                    $row = $this->_fetchBound($row);
                }
                break;
            default:
                /**
                 * @see ZendX_Db_Adapter_Firebird_Exception
                 */
                require_once 'ZendX/Db/Statement/Firebird/Exception.php';
                throw new ZendX_Db_Statement_Firebird_Exception(
                    "Invalid fetch mode '$style' specified"
                );
                break;
        }


        return $row;
    }

    /**
     * Retrieves the next rowset (result set) for a SQL statement that has
     * multiple result sets.  An example is a stored procedure that returns
     * the results of multiple queries.
     *
     * @return bool
     * @throws ZendX_Db_Statement_Firebird_Exception
     */
    public function nextRowset()
    {
        /**
         * @see ZendX_Db_Statement_Firebird_Exception
         */
        require_once 'ZendX/Db/Statement/Firebird/Exception.php';
        throw new ZendX_Db_Statement_Firebird_Exception(__FUNCTION__.'() is not implemented');
    }

    /**
     * Returns the number of rows affected by the execution of the
     * last INSERT, DELETE, or UPDATE statement executed by this
     * statement object.
     *
     * @return int     The number of rows affected.
     * @throws Zend_Db_Statement_Exception
     */
    public function rowCount()
    {
        return $this->_stmtRowCount ? $this->_stmtRowCount : 0;
    }

}
