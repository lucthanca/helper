<?php

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

// Debug: get previous call
if (!function_exists('vadu_trace')) {
    function vadu_trace($trace)
    {
        $caller = $trace[1];
        $mes = "Called by {$caller['function']}";
        if (isset($caller['class'])) {
            $mes .= " in {$caller['class']}";
        }
        dump($mes);
    }
}
