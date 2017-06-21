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
 * @category  ZendX
 * @package   ZendX_Console
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: Unix.php 20165 2010-01-09 18:57:56Z bkarwin $
 */


/**
 * ZendX_Console_Process_Unix allows you to spawn a class as a separated process
 *
 * @category  ZendX
 * @package   ZendX_Console
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class ZendX_Console_Process_Unix
{
    /**
     * Void method
     */
    const VOID_METHOD = 'void_method';

    /**
     * Return method
     */
    const RETURN_METHOD = 'void_method';
    
    /**
     * Unique thread name
     *
     * @var string
     */
    private $_name;

    /**
     * PID of the child process
     *
     * @var integer
     */
    private $_pid = null;

    /**
     * UID of the child process owner
     *
     * @var integer
     */
    private $_puid = null;

    /**
     * GUID of the child process owner
     *
     * @var integer
     */
    private $_guid = null;

    /**
     * Whether the process is yet forked or not
     *
     * @var boolean
     */
    private $_isRunning = false;

    /**
     * Wether we are into child process or not
     *
     * @var boolean
     */
    private $_isChild = false;

    /**
     * A data structure to hold data for Inter Process Communications
     *
     * @var array
     */
    private $_internalIpcData = array();

    /**
     * Key to access to Shared Memory Area.
     *
     * @var integer
     */
    private $_internalIpcKey;

    /**
     * Key to access to Sync Semaphore.
     *
     * @var integer
     */
    private $_internalSemKey;

    /**
     * Is Shared Memory Area OK? If not, the start() method will block.
     * Otherwise we'll have a running child without any communication channel.
     *
     * @var boolean
     */
    private $_ipcIsOkay;

    /**
     * Filename of the IPC segment file
     *
     * @var string
     */
    private $_ipcSegFile;

    /**
     * Filename of the semaphor file
     *
     * @var string
     */
    private $_ipcSemFile;

    /**
     * Constructor method
     *
     * Allocates a new pseudo-thread object. Optionally, set a PUID, a GUID and
     * a UMASK for the child process. This also initialize Shared Memory
     * Segments for process communications.
     *
     * @param  integer $puid
     * @param  integer $guid
     * @param  integer $umask
     * @throws ZendX_Console_Process_Exception When running on windows
     * @throws ZendX_Console_Process_Exception When running in web enviroment
     * @throws ZendX_Console_Process_Exception When shmop_* functions don't exist
     * @throws ZendX_Console_Process_Exception When pcntl_* functions don't exist
     * @throws ZendX_Console_Process_Exception When posix_* functions don't exist
     */
    public function __construct($puid = null, $guid = null, $umask = null)
    {
        if (substr(PHP_OS, 0, 3) === 'WIN') {
            require_once 'ZendX/Console/Process/Exception.php';
            throw new ZendX_Console_Process_Exception('Cannot run on windows');
        } else if (!in_array(substr(PHP_SAPI, 0, 3), array('cli', 'cgi'))) {
            require_once 'ZendX/Console/Process/Exception.php';
            throw new ZendX_Console_Process_Exception('Can only run on CLI or CGI enviroment');
        } else if (!function_exists('shmop_open')) {
            require_once 'ZendX/Console/Process/Exception.php';
            throw new ZendX_Console_Process_Exception('shmop_* functions are required');
        } else if (!function_exists('pcntl_fork')) {
            require_once 'ZendX/Console/Process/Exception.php';
            throw new ZendX_Console_Process_Exception('pcntl_* functions are required');
        } else if (!function_exists('posix_kill')) {
            require_once 'ZendX/Console/Process/Exception.php';
            throw new ZendX_Console_Process_Exception('posix_* functions are required');
        }
    
        $this->_isRunning = false;

        $this->_name = md5(uniqid(rand()));
        $this->_guid = $guid;
        $this->_puid = $puid;

        if ($umask !== null) {
            umask($umask);
        }

        // Try to create the shared memory segment. The variable
        // $this->_ipcIsOkay contains the return code of this operation and must
        // be checked before forking
        if ($this->_createIpcSegment() && $this->_createIpcSemaphore()) {
            $this->_ipcIsOkay = true;
        } else {
            $this->_ipcIsOkay = false;
        }
    }
    
    /**
     * Stop the child on destruction
     */
    public function __destruct()
    {
        if ($this->isRunning()) {
            $this->stop();
        }
    }
    
    /**
     * Causes this pseudo-thread to begin parallel execution.
     *
     * This method first checks of all the Shared Memory Segment. If okay, it
     * forks the child process, attaches signal handler and returns immediatly.
     * The status is set to running, and a PID is assigned. The result is that
     * two pseudo-threads are running concurrently: the current thread (which
     * returns from the call to the start() method) and the other thread (which
     * executes its run() method).
     * 
     * @throws ZendX_Console_Process_Exception When SHM segments can't be created
     * @throws ZendX_Console_Process_Exception When process forking fails
     * @return void
     */
    public function start()
    {
        if (!$this->_ipcIsOkay) {
            require_once 'ZendX/Console/Process/Exception.php';
            throw new ZendX_Console_Process_Exception('Unable to create SHM segments for process communications');
        }

        // @see http://www.php.net/manual/en/function.pcntl-fork.php#41150
        @ob_end_flush();
        
        pcntl_signal(SIGCHLD, SIG_IGN);

        $pid = @pcntl_fork();
        if ($pid === -1) {
            require_once 'ZendX/Console/Process/Exception.php';
            throw new ZendX_Console_Process_Exception('Forking process failed');
        } else if ($pid === 0) {
            // This is the child
            $this->_isChild = true;
           
            // Sleep a second to avoid problems
            sleep(1);
            
            // Install the signal handler
            pcntl_signal(SIGUSR1, array($this, '_sigHandler'));

            // If requested, change process identity
            if ($this->_guid !== null) {
                posix_setgid($this->_guid);
            }

            if ($this->_puid !== null) {
                posix_setuid($this->_puid);
            }

            // Run the child
            try {
                $this->_run();
            } catch (Exception $e) {
                // We have to catch any exceptions and clean up the process,
                // else we will have a memory leak.
            }

            // Destroy the child after _run() execution. Required to avoid
            // unuseful child processes after execution
            exit(0);
        } else {
            // Else this is the parent
            $this->_isChild   = false;
            $this->_isRunning = true;
            $this->_pid       = $pid;
        }
    }
    
    /**
     * Causes the current thread to die.
     *
     * The relative process is killed and disappears immediately from the
     * processes list.
     *
     * @return boolean
     */
    public function stop()
    {
        $success = false;

        if ($this->_pid > 0) {
            $status = 0;
            
            posix_kill($this->_pid, 9);
            pcntl_waitpid($this->_pid, $status, WNOHANG);
            $success = pcntl_wifexited($status);
            $this->_cleanProcessContext();
        }

        return $success;
    }

    /**
     * Test if the pseudo-thread is already started.
     *
     * @return boolean
     */
    public function isRunning()
    {       
        return $this->_isRunning;
    }

    /**
     * Set a variable into the shared memory segment, so that it can accessed
     * both from the parent and from the child process. Variable names 
     * beginning with underlines are only permitted to interal functions.
     *
     * @param  string $name
     * @param  mixed  $value
     * @throws ZendX_Console_Process_Exception When an invalid variable name is supplied
     * @return void
     */
    public function setVariable($name, $value)
    {
        if ($name[0] === '_') {
            require_once 'ZendX/Console/Process/Exception.php';
            throw new ZendX_Console_Process_Exception('Only internal functions may use underline (_) as variable prefix');
        }

        $this->_writeVariable($name, $value);
    }

    /**
     * Get a variable from the shared memory segment. Returns NULL if the
     * variable doesn't exist.
     *
     * @param  string $name
     * @return mixed
     */
    public function getVariable($name)
    {
        $this->_readFromIpcSegment();

        if (isset($this->_internalIpcData[$name])) {
            return $this->_internalIpcData[$name];
        } else {
            return null;
        }
    }

    /**
     * Read the time elapsed since the last child setAlive() call.
     *
     * This method is useful because often we have a pseudo-thread pool and we
     * need to know each pseudo-thread status. If the child executes the
     * setAlive() method, the parent with getLastAlive() can know that child is
     * alive.
     *
     * @return integer
     */
    public function getLastAlive()
    {
        $pingTime = $this->getVariable('_pingTime');

        return ($pingTime === null ? 0 : (time() - $pingTime));
    }

    /**
     * Returns the PID of the current pseudo-thread.
     *
     * @return integer
     */
    public function getPid()
    {
        return $this->_pid;
    }
    
    /**
     * Set a pseudo-thread property that can be read from parent process
     * in order to know the child activity.
     *
     * Practical usage requires that child process calls this method at regular
     * time intervals; parent will use the getLastAlive() method to know
     * the elapsed time since the last pseudo-thread life signals...
     * 
     * @return void
     */
    protected function _setAlive()
    {
        $this->_writeVariable('_pingTime', time());
    }
    

    /**
     * This is called from within the parent; all the communication stuff
     * is done here.
     *
     * @param  string $methodName
     * @param  array  $argList
     * @param  string $type
     * @return mixed
     */
    protected function _callCallbackMethod($methodName, array $argList = array(), $type = self::VOID_METHOD)
    {
        // This is the parent, so we really cannot execute the method. Check
        // arguments passed to the method.
        if ($type === self::RETURN_METHOD) {
            $this->_internalIpcData['_callType'] = self::RETURN_METHOD;
        } else {
            $this->_internalIpcData['_callType'] = self::VOID_METHOD;
        }

        // These setting are common to both the calling types
        $this->_internalIpcData['_callMethod'] = $methodName;
        $this->_internalIpcData['_callInput']  = $argList;

        // Write the IPC data to the shared segment
        $this->_writeToIpcSegment();

        // Now we need to differentiate a bit.
        switch ($this->_internalIpcData['_callType']) {
            case VOID_METHOD:
                // Notify the child so it can process the request
                $this->_sendSigUsr1();
                break;

            case RETURN_METHOD:
                // Set the semaphorew
                shmop_write($this->_internalSemKey, 1, 0);

                // Notify the child so it can process the request
                $this->_sendSigUsr1();

                // Block until the child process return
                $this->_waitForIpcSemaphore();

                // Read from the SHM segment. The result is stored into
                // $this->_internalIpcData['_callOutput']
                $this->_readFromIpcSegment();

                // Data are returned. Now we can reset the semaphore
                shmop_write($this->_internalSemKey, 0, 1);

                // Return the result. Hence no break required here
                return $this->_internalIpcData['_callOutput'];
        }
    }
    
    /**
     * This method actually implements the pseudo-thread logic.
     * 
     * @return void
     */
    abstract protected function _run();
    
    /**
     * Sends signal to the child process
     * 
     * @return void
     */
    private function _sendSigUsr1()
    {
        if ($this->_pid > 0) {
            posix_kill($this->_pid, SIGUSR1);
        }
    }
    
    /**
     * Acutally Write a variable to the shared memory segment
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    private function _writeVariable($name, $value)
    {
        $this->_internalIpcData[$name] = $value;
        $this->_writeToIpcSegment();
    }

    /**
     * Destroy thread context and free relative resources.
     * 
     * @return void
     */
    private function _cleanProcessContext()
    {
        shmop_delete($this->_internalIpcKey);
        shmop_delete($this->_internalSemKey);

        shmop_close($this->_internalIpcKey);
        shmop_close($this->_internalSemKey);

        @unlink($this->_ipcSegFile);
        @unlink($this->_ipcSemFile);

        $this->_isRunning = false;
        $this->_pid       = null;
    }

    /**
     * This is the signal handler that makes the communications between client
     * and server possible.
     *
     * @param  integer $signo
     * @return void
     */
    private function _sigHandler($signo)
    {
        switch ($signo) {
            case SIGTERM:
                // Handle shutdown tasks. Hence no break is require
                exit;

            case SIGUSR1:
                // This is the User-defined signal we'll use. Read the SHM segment
                $this->_readFromIpcSegment();

                if (isset($this->_internalIpcData['_callType'])) {
                    $method = $this->_internalIpcData['_callMethod'];
                    $params = $this->_internalIpcData['_callInput'];

                    switch ($this->_internalIpcData['_callType']) {
                        case self::VOID_METHOD:
                            // Simple call the (void) method and return immediatly
                            // no semaphore is placed into parent, so the processing
                            // is async
                            call_user_func(array($this, $method), $params);
                            break;

                        case self::RETURN_METHOD:
                            // Process the request
                            $this->_internalIpcData['_callOutput'] = call_user_func(array($this, $method), $params);

                            // Write the result into IPC segment
                            $this->_writeToIPCsegment();

                            // Unlock the semaphore but block _writeToIpcSegment()
                            shmop_write($this->_internalSemKey, 0, 0);
                            shmop_write($this->_internalSemKey, 1, 1);
                            break;
                    }
                }
                break;
                
            default:
                // Ignore all other singals
                break;
        }
    }

    /**
     * Wait for IPC Semaphore
     * 
     * @return void
     */
    private function _waitForIpcSemaphore()
    {
        while (true) {
            $okay = shmop_read($this->_internalSemKey, 0, 1);

            if ($okay === 0) {
                break;
            }

            usleep(10);
        }
    }

    /**
     * Read data from IPC segment
     * 
     * @throws ZendX_Console_Process_Exception When writing of SHM segment fails
     * @return void
     */
    private function _readFromIpcSegment()
    {
        $serializedIpcData = shmop_read($this->_internalIpcKey,
                                        0,
                                        shmop_size($this->_internalIpcKey));

        if ($serializedIpcData === false) {
            require_once 'ZendX/Console/Process/Exception.php';
            throw new ZendX_Console_Process_Exception('Fatal error while reading SHM segment');
        }

        $data = @unserialize($serializedIpcData);
        
        if ($data !== false) {
            $this->_internalIpcData = $data;
        }
    }

    /**
     * Write data to IPC segment
     * 
     * @throws ZendX_Console_Process_Exception When writing of SHM segment fails
     * @return void
     */
    private function _writeToIpcSegment()
    {
        // Read the transaction bit (2 bit of _internalSemKey segment). If it's
        // value is 1, we're into the execution of a PHP_FORK_RETURN_METHOD, so
        // we must not write to segment (data corruption)
        if (shmop_read($this->_internalSemKey, 1, 1) === 1) {
            return;
        }

        $serializedIpcData = serialize($this->_internalIpcData);

        // Set the exchange array (IPC) into the shared segment
        $shmBytesWritten = shmop_write($this->_internalIpcKey,
                                       $serializedIpcData,
                                       0);

        // Check if lenght of SHM segment is enougth to contain data
        if ($shmBytesWritten !== strlen($serializedIpcData)) {
            require_once 'ZendX/Console/Process/Exception.php';
            throw new ZendX_Console_Process_Exception('Fatal error while writing to SHM segment');
        }
    }

    /**
     * Create an IPC segment
     *
     * @throws ZendX_Console_Process_Exception When SHM segment can't be created
     * @return boolean
     */
    private function _createIpcSegment()
    {
        $this->_ipcSegFile = realpath(sys_get_temp_dir()) . '/' . rand() . $this->_name . '.shm';
        touch($this->_ipcSegFile);

        $shmKey = ftok($this->_ipcSegFile, 't');
        if ($shmKey === -1) {
            require_once 'ZendX/Console/Process/Exception.php';
            throw new ZendX_Console_Process_Exception('Could not create SHM segment');
        }

        $this->_internalIpcKey = @shmop_open($shmKey, 'c', 0644, 10240);

        if (!$this->_internalIpcKey) {
            @unlink($this->_ipcSegFile);
            return false;
        }

        return true;
    }

    /**
     * Create IPC semaphore
     *
     * @throws ZendX_Console_Process_Exception When semaphore can't be created
     * @return boolean
     */
    private function _createIpcSemaphore()
    {
        $this->_ipcSemFile = realpath(sys_get_temp_dir()) . '/' . rand() . $this->_name . '.sem';
        touch($this->_ipcSemFile);

        $semKey = ftok($this->_ipcSemFile, 't');
        if ($semKey === -1) {
            require_once 'ZendX/Console/Process/Exception.php';
            throw new ZendX_Console_Process_Exception('Could not create semaphore');
        }

        $this->_internalSemKey = @shmop_open($semKey, 'c', 0644, 10);

        if (!$this->_internalSemKey) {
            @unlink($this->_ipcSemFile);
            return false;
        }

        return true;
    }
}
