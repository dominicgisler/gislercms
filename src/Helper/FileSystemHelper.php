<?php

namespace GislerCMS\Helper;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileSystemHelper
{
    /**
     * @param string $path
     */
    public static function remove(string $path)
    {
        if (file_exists($path)) {
            if (is_dir($path)) {
                $it = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($it,
                    RecursiveIteratorIterator::CHILD_FIRST);
                foreach ($files as $file) {
                    if ($file->isDir()) {
                        rmdir($file->getRealPath());
                    } else {
                        unlink($file->getRealPath());
                    }
                }
                rmdir($path);
            } else if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
