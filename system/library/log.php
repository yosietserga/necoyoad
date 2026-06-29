<?php
final class Log {
	private $filename;
	private $time_start;
    private $total_time;
	private $time_end;

    private $mem_start;
    private $total_mem;
    private $mem_end;

	public function __construct($filename) {
        if (!is_dir(DIR_LOGS)) mkdir(DIR_LOGS, 0755, true);
		$this->filename = $filename;
		$this->time_start = microtime(true);
        $this->total_time = $this->time_start;

        $this->mem_start = memory_get_peak_usage(true);
        $this->total_mem = $this->mem_start;
	}
	
	public function write($message) {
		$file = DIR_LOGS . $this->filename;
		$handle = fopen($file, 'a+');
		fwrite($handle, date('Y-m-d G:i:s') . ' - ' . $message . "\n");
		fclose($handle); 
	}

	public function trace($info = null) {
        $trace = debug_backtrace();
        if (isset($trace[1])) {
            $msg = "\r\n";
            $msg .= str_repeat("-", 20);
            $msg .= "\r\n";

            $msg .= isset($trace[1]['file']) ? "File: {$trace[1]['file']} \n" : "";
            $msg .= isset($trace[1]['line']) ? "Line: {$trace[1]['line']} \n" : "";
            if (isset($trace[1]['class'])) $msg .= "Class: {$trace[1]['class']} \n";
            if (isset($trace[1]['function'])) $msg .= "Function: {$trace[1]['function']} \n";
            //if (isset($trace[1]['args'])) $msg .= "Args:\n". (print_r($trace[1]['args'], true)) ."\n";
            //if (isset($trace[1]['object'])) $msg .= "Object:\n". (print_r($trace[1]['object'], true)) ."\n";

            if (isset($info)) $msg .= "DATA:\n". (print_r($info, true)) ."\n";

            $this->time_end = microtime(true);
            $this->mem_end = memory_get_peak_usage(true);
            $msg .= "IP: {$_SERVER['REMOTE_ADDR']} ".

            "\nTime Exec: ". ($this->time_end - $this->time_start) . " seconds ".
            "\nMemory Usage: ". ($this->mem_end - $this->mem_start) . " Bytes ".

            "\nTime Accum: ". ($this->time_end - $this->total_time) . " seconds ".
            "\nMemory Accum: ". ($this->mem_end/1024 - $this->total_mem/1024) ."KB\n";

            $this->mem_start = $this->mem_end;
            $this->time_start = $this->time_end;

            $msg .= "\r\n";
            $msg .= str_repeat("-", 20);
            $msg .= "\r\n";

            $this->write($msg);
        }
    }
}
