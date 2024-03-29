<?php
require __DIR__ . '/vendor/autoload.php';

use \Logger as LoggerPhp;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

if (!function_exists('vadu_log')) {
    function vadu_log($var, ...$moreVars) {
        LoggerPhp::configure(
            [
                'rootLogger' => ['appenders' => ['vaduLog']],
                'appenders' => [
                    'vaduLog' => [
                        'class' => 'LoggerAppenderFile',
                        'layout' => [
                            'class' => 'LoggerLayoutPattern'
                        ],
                        'params' => [
                            'file' => __DIR__ . '/var/log/vadu.log',
                            'append' => true
                        ]
                    ]
                ]
            ]
        );
        $logger = LoggerPhp::getLogger('vaduLog');
        $logger->info($var);
        foreach ($moreVars as $var) {
            $logger->info($var);
        }
    }
}

//if (!function_exists('vadu_log')) {
//    /**
//     * Log to vadu_log.log file
//     *
//     * @param mixed $var
//     * @author Vadu
//     */
//    function vadu_log($var, ...$moreVars)
//    {
//        $writer = new \Laminas\Log\Writer\Stream(__DIR__ . '/var/log/vadu_log.log');
//        $logger = new \Laminas\Log\Logger();
//
//        v_log($var, $writer, $logger);
//
//        foreach ($moreVars as $var) {
//            v_log($var, $writer, $logger);
//        }
//
//        if (1 < func_num_args()) {
//            return func_get_args();
//        }
//    }
//}
//
//if (!function_exists('v_log')) {
//    /**
//     * @param mixed $var
//     * @param object $writer
//     * @param object $logger
//     */
//    function v_log($var, $writer, $logger)
//    {
//        $logger->addWriter($writer);
//        if (is_string($var)) {
//            $logger->info($var);
//        } else {
//            $logger->info(print_r($var, true));
//        }
//    }
//}

if (!function_exists('vadu_html')) {
    /**
     * Log to var/log/vadu.log file
     *
     * // Chú ý cần custom trong file LoggerLayoutHtml:157
     *
     * @param mixed $var
     * @throws Exception
     * @author Vadu
     */
    function vadu_html(...$vars): void
    {
        LoggerPhp::configure(
            [
                'rootLogger' => ['appenders' => ['vaduLog']],
                'appenders' => [
                    'vaduLog' => [
                        'class' => 'LoggerAppenderFile',
                        'layout' => [
                            'class' => 'LoggerLayoutHtml'
                        ],
                        'params' => [
                            'file' => __DIR__ . '/var/log/vadu.html',
                            'append' => true
                        ]
                    ]
                ]
            ]
        );
        $logger = LoggerPhp::getLogger('vaduLog');

        foreach ($vars as $v) {
            if ($logContent = VarDumperLogToHtml::getLogHtmlContent($v)) {
                $logger->info($logContent);
            } else {
                $logger->info('Rỗng');
            }
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

class VarDumperLogToHtml {
    /**
     * Dump to html file
     *
     * @throws ErrorException
     */
    public static function dump($var, $file = null): void
    {
        $output = null;
        $cloner        = new VarCloner();
        $dumper        = new HtmlDumper();

        if (null === $output) {
            if ($file == null) {
                $now = new \DateTime();
                /** @psalm-suppress InvalidOperand */
                $file = __DIR__ . "/var/log/" . $now->format('Y-m-d\TH-i-s-u') . '_' . random_int(0, mt_getrandmax()) . '.html';
            }

            $output = fopen($file, 'a+b');
        }

        if (false === is_resource($output)) {
            throw new \RuntimeException('Something went wrong creating the dump file.');
        }

        $dumper->dump($cloner->cloneVar($var), $output, []);
    }

    /**
     * Dump to html file then get content in that html after that, delete it (for log table)
     *
     * @param mixed $var
     * @return string|null
     * @throws Exception
     */
    public static function getLogHtmlContent($var): ?string
    {
        $now = new \DateTime();
        /** @psalm-suppress InvalidOperand */
        $file = __DIR__ . "/var/log/" . $now->format('Y-m-d\TH-i-s-u') . '_' . random_int(0, mt_getrandmax()) . '.html';
        try {
            VarDumperLogToHtml::dump($var, $file);
        } catch (\Exception $e) {
            return null;
        }

        if (file_exists($file)) {
            $content = file_get_contents($file);
             unlink($file);
            return $content;
        }

        return null;
    }
}
