<?php

namespace RESO\Log;

abstract class Base
{
    public static function logMessage($message) {
        if(!\RESO\Reso::getLogEnabled()) {
            return false;
        }

        if(\RESO\Reso::getLogConsole()) {
            self::logConsole($message);
        }

        if(\RESO\Reso::getLogFile() && \RESO\Reso::getLogFileName()) {
            self::logFile(\RESO\Reso::getLogFileName(), $message);
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
