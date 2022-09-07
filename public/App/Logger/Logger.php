<?php

namespace App\Logger;

use App\Interface\Logs\LogsInterface;
use config\config;

class Logger
implements LogsInterface
{

    protected $name;
    protected $file;
    protected $fp;
    protected static $loggers = array();

    public function __construct($name, $file = null)
    {
        $this->name = $name;
        $this->file = $file;
        $this->open();
    }

    public function open()
    {
        if (config::$LOG_PATH == null) {
            return;
        }

        $this->fp = fopen($this->file == null ? config::$LOG_PATH . '/' . $this->name . '.log' : config::$LOG_PATH . '/' . config::$LOG_FILE_DEFAULT, 'a+');
    }

    public static function getLogger($name = 'root', $file = null)
    {
        if (!isset(self::$loggers[$name])) {
            self::$loggers[$name] = new Logger($name, $file);
        }

        return self::$loggers[$name];
    }

    public function log($message)
    {
        if (!is_string($message)) {
            $this->logPrint($message);
            return;
        }

        $log = '';

        $log .= '[' . date('D M d H:i:s Y', time()) . '] ';
        if (func_num_args() > 1) {
            $params = func_get_args();

            $message = call_user_func_array('sprintf', $params);
        }

        $log .= $message;
        $log .= "\n";

        $this->_write($log);
    }

    public function logPrint($obj)
    {
        ob_start();

        print_r($obj);

        $ob = ob_get_clean();
        $this->log($ob);
    }

    protected function _write($string)
    {
        fwrite($this->fp, $string);
        if (config::$LOG_DEBUG)
            die($string);
    }

    public function __destruct()
    {
        fclose($this->fp);
    }
}
