<?php
class classException
{


/**
Based on
https://gist.github.com/abtris/1437966
**/
public static function getExceptionTraceAsString($exception) {
    $rtn = "";
    $count = 0;
    foreach ($exception->getTrace() as $frame) {
        $args = "";
        if (isset($frame['args'])) {
            $args = array();
            foreach ($frame['args'] as $arg) {
                if (is_string($arg)) {
                    $args[] = "'" . $arg . "'";
                } elseif (is_array($arg)) {
                    $args[] = "Array";
                } elseif (is_null($arg)) {
                    $args[] = 'NULL';
                } elseif (is_bool($arg)) {
                    $args[] = ($arg) ? "true" : "false";
                } elseif (is_object($arg)) {
                    $args[] = get_class($arg);
                } elseif (is_resource($arg)) {
                    $args[] = get_resource_type($arg);
                } else {
                    $args[] = $arg;
                }
            }
            $args = join(", ", $args);
        }
        $rtn .= sprintf( "#%s %s(%s): %s(%s)\n",
                                 $count,
                                 isset($frame['file']) ? $frame['file'] : 'unknown file',
                                 isset($frame['line']) ? $frame['line'] : 'unknown line',
                                 (isset($frame['class']))  ? $frame['class'].$frame['type'].$frame['function'] : $frame['function'],
                                 $args );
        $count++;
    }
    return $rtn;
}
}
