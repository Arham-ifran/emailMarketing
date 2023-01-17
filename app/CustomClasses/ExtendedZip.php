<?php

namespace App\CustomClasses;

use File;
use ZipArchive;


class ExtendedZip extends ZipArchive {

    // Member function to add a whole file system subtree to the archive
    public function addTree($dirname, $localname = '') {
        if ($localname)
            $this->addEmptyDir($localname);
        $this->_addTree($dirname, $localname);
    }

    // Internal function, to recurse
    protected function _addTree($dirname, $localname) {
        $dir = opendir($dirname);
        while ($filename = readdir($dir)) {
            // Discard . and ..
            if ($filename == '.' || $filename == '..')
                continue;

            // Proceed according to type
            $path = $dirname . '/' . $filename;
            $localpath = $localname ? ($localname . '/' . $filename) : $filename;
            if (is_dir($path)) {
                // Directory: add & recurse
                $this->addEmptyDir($localpath);
                $this->_addTree($path, $localpath);
            }
            else if (is_file($path)) {
                // File: just add
                $this->addFile($path, $localpath);
            }
        }
        closedir($dir);
    }

    // Helper function
    public static function zipTree($dirname, $user, $flags = 0, $localname = '') 
    {
        $tmp_file = tempnam(public_path().'/storage/temp/', '');
        $zip = new self();
        $zip->open($tmp_file, $flags);
        $zip->addTree($dirname, $localname);
        $zip->close();

        # getting name of temp file
        $filename = basename($tmp_file);
        chmod($tmp_file, 0777);

        # Save temp file name in db
        $user->update([
            'temp_zip_file' => $filename
        ]);
    }
}
