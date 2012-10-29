<?php

/**
 * ownCloud - App files_meta 
 * Gives an aditional description per file.
 * @author Arman Khalatyan
 * @copyright Arman Khalatyan arm2arm@gmail.com
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either 
 * version 3 of the License, or any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *  
 * You should have received a copy of the GNU Lesser General Public 
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

/**
 * Set of functions to manage meta data information
 */
class OC_FilesMeta {

    private $description;
    private $filename;

    public function __construct() {
        
    }

    public static function isStartingWith($haystack, $needle) {
        return (strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
    }

    /**
     *  An Utility function which returns folder or file 
     * return string ;
     */
    protected static function getItemType($source) {
        $itemtype = OC_Filesystem::is_file($source) ? 'file' : 'folder';
        return $itemtype;
    }

    public static function getDescription($source) {
        $source = OC_Filesystem::normalizePath($source);
        $realpath = '/' . OCP\USER::getUser() . '/files' . $source;
        $strippedsource = '';
        $sharedstr = '/Shared';
        if (OC_FilesMeta::isStartingWith($source, $sharedstr)) {
            // get the real file name from shares.
            $strippedsource = substr($source, strlen($sharedstr));
            $realpath = OCP\Share::getItemSharedWithBySource('', $realpath);
        }


        $isexist = OC_Filesystem::file_exists($source);
        if (!$isexist)
            return array('info:' => 'No Meta Data exist');

        $mimetype = OC_Filesystem::getMimeType($source);

        $shareinfo = 'None';

        $w = array(false => '-', true => 'w');
        $r = array(false => '-', true => 'r');
        //var_dump($realpath);
       // $sharei = OCP\Share::getItemSharedWith(self::getItemType($source), $realpath);
        $sharei = OCP\Share::getItemsShared(self::getItemType($source), $source);
        var_dump(OCP\Share::getItemsShared(self::getItemType($source), OCP\Share::FORMAT_STATUSES));
        if ($sharei) {
            $shareinfo = '<ul>';
            //foreach ($sharei as $k => $v) 
                {
                $wr = 'readonly';
                if ($sharei['permissions'])
                    $wr = 'can edit';
                $shareinfo.='<li>' . 'uid_shared_with' . " <strong>" . $wr . '</strong></li>';
            }
            $shareinfo.='</ul>';
        }



        $pstr = $r[OC_Filesystem::is_readable($source)] . $w[OC_Filesystem::is_writable($source)];
        $fsize = OC_Helper::humanFileSize(OC_Filesystem::filesize($source));

        $description = OC_FilesMeta::getByKey($realpath, 'description');



        $metaSorted = array('status' => 'success',
            'data' => array('general' => array(
                    'filename' => $source, 'size' => $fsize, 'perm' => $pstr, 'MIME' => $mimetype)
            ), 'shared' => $shareinfo,
            'description' => $description
        );


        return $metaSorted;
    }

    public static function getByKey($source, $key, $default = 'none') {
        $result = '';
        $query = OCP\DB::prepare("SELECT value FROM *PREFIX*metadata  WHERE `item`=?  AND `key`=? limit 1 ");

        $result = $query->execute(array($source, $key))->fetchAll();

        if (count($result) > 0) {
            //var_dump($result);
            return $result[0]['value'];
        } else {
            return $default;
        }
    }

    public static function setByKey($source, $key, $value) {

        $query = OCP\DB::prepare("DELETE FROM  *PREFIX*metadata WHERE `item`=?  ");
        //      error_log("file_meta:" . $source);

        $query->execute(array($source));

        $query = OCP\DB::prepare("INSERT INTO  *PREFIX*metadata (`item`,`key`,`value`) VALUES(?,?,?) ");
        $query->execute(array($source, $key, $value));
//        error_log("file_meta:" . $source . $key . $value);
    }

}

;
