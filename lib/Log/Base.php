<?php

namespace RESO\Log;

abstract class Base
{
    public static function logMessage($message) {
        if(!\RESO\RESO::getLogEnabled()) {
            return false;
        }

        if(\RESO\RESO::getLogConsole()) {
            self::logConsole($message);
        }

        if(\RESO\RESO::getLogFile() && \RESO\RESO::getLogFileName()) {
            self::logFile(\RESO\RESO::getLogFileName(), $message);
        }
    }

    public static function logConsole($message) {
        $message = self::getTimeString()." ".$message;
        echo($message."\n");
        return true;
    }

    public static function logFile($file_name, $message) {
        $message = self::getTimeString()." ".$message;
        if(is_dir(dirname($file_name))) {
            file_put_contents($file_name, $message . "\n", FILE_APPEND);
            return true;
        } else {
            return false;
        }
    }

    public static function getTimeString() {
        return "[".date("c")."]";
    }
}
