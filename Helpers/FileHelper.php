<?php

class FileHelper
{
    public static function rrmdir($dir) 
    { 
        if (is_dir($dir)) { 
            $objects = scandir($dir);
            foreach ($objects as $object) { 
                if ($object != "." && $object != "..") { 
                    if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
                    static::rrmdir($dir. DIRECTORY_SEPARATOR .$object);
                    else
                    unlink($dir. DIRECTORY_SEPARATOR .$object); 
                } 
            }
            rmdir($dir); 
        } 
    }

    public static function rename($from_path, $to_name) : bool
    {
        $exploded_path = explode(DIRECTORY_SEPARATOR, $from_path);

        $exploded_path[count($exploded_path) -1] = $to_name;

        $new_path = implode(DIRECTORY_SEPARATOR, $exploded_path);
        // var_dump($from_path);
        // var_dump($new_path);

        // die();
        return rename($from_path, $new_path);
    }
}

?>