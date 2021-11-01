<?php
require __DIR__ . '/vendor/autoload.php';
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

if (!function_exists('vadu_log')) {
    /**
     * Log to vadu_log.log file
     *
     * @param mixed $var
     * @author Vadu
     */
    function vadu_log($var, ...$moreVars)
    {
        $writer = new Stream(BP . '/var/log/vadu_log.log');
        $logger = new Logger();

        v_log($var, $writer, $logger);

        foreach ($moreVars as $var) {
            v_log($var, $writer, $logger);
        }

        if (1 < func_num_args()) {
            return func_get_args();
        }
    }
}

if (!function_exists('v_log')) {
    /**
     * @param mixed $var
     * @param object $writer
     * @param object $logger
     */
    function v_log($var, $writer, $logger)
    {
        $logger->addWriter($writer);
        if (is_string($var)) {
            $logger->info($var);
        } else {
            $logger->info(print_r($var, true));
        }
    }
}

if (!function_exists("vadu_for")) {
    function vadu_for($arrays, $exit = true) {
        foreach ($arrays as $item) {
            dump($item);
        }
        if ($exit) {
            exit(1);
        }
    }
}

// Debug: get previous call
if (!function_exists('vadu_trace')) {
    /**
     * Tracking previous call
     *
     * @param array $trace | debug_backtrace()
     */
    function vadu_trace($trace)
    {
        $caller = $trace[1];
        $mes = "Called by {$caller['function']}";
        if (isset($caller['class'])) {
            $mes .= " in {$caller['class']} on line {$caller['line']}";
        }
        dump($mes);
    }
}

if (!function_exists('vadu_execution_time')) {
    /**
     * Execution time
     *
     * @param $callback
     * @return float|string
     */
    function vadu_execution_time($callback)
    {
        $startTime = microtime(true);
        $callback();
        $endTime = microtime(true);
        return ($endTime - $startTime);
    }
}
