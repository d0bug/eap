<?php
class Debugger {
    private static $errorFiles = array();
    private static $errors = array();
    public static function addErrors($moduleName, $errorFile) {
        $fileKey = md5($errorFile);
        $moduleName = strtoupper($moduleName);
        if (false == self::$errorFiles[$fileKey]) {
            self::$errorFiles[$fileKey] = require_once($errorFile);
        }
        if (false == self::$errors[$moduleName]) {
            self::$errors[$moduleName] = self::$errorFiles[$fileKey];
        }
    }

    public static function trace($moduleName, $errorCode) {
        ob_clean();
        $moduleName = strtoupper($moduleName);
        if (false == self::$erros[$moduleName]) {
            die('Error:Not defined Error');
        }
        die('Error<' . $errorCode . '>:' . self::$errors[$moduleName][$errorCode]);
    }
};
?>