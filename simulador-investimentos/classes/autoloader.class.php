<?php
class Autoloader
{
    public static function loader($classname)
    {
        $filename = "classes/" . str_replace("\\", "/", $classname) . ".class.php";

        if (file_exists($filename)) {
            require_once($filename);

            if (class_exists($classname)) {
                return TRUE;
            }
        }
        return FALSE;
    }
}

spl_autoload_extensions('.class.php');
spl_autoload_register('Autoloader::loader');
