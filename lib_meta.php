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

    public static function getDescription($source) {
        
        $realpath = '/' . OCP\USER::getUser() . '/files' . $source;
        
        $sharedstr = '/Shared';
        if (OC_FilesMeta::isStartingWith($source, $sharedstr)) {
            // get the real file name from shares.
            $strippedsource = substr($source, strlen($sharedstr));
            $realpath=OC_Share::getSource($realpath);
        }
        
        //var_dump();
        //var_dump($realpath);
        //var_dump($strippedsource);
        
       




        $isexist = OC_Filesystem::file_exists($source);
        if (!$isexist)
            return array('info:' => 'No Meta Data exist');

        $mimetype = OC_Filesystem::getMimeType($source);


        $shareinfo = 'None';

        $w = array(false => '-', true => 'w');
        $r = array(false => '-', true => 'r');

        $sharei = OC_Share::getMySharedItem($realpath);
        //var_dump($sharei);

        if ($sharei) {
            $shareinfo = '<ul>';
            foreach ($sharei as $k => $v) {
                $wr = 'readonly';
                if ($v['permissions'])
                    $wr = 'can edit';
                $shareinfo.='<li>' . $v['uid_shared_with'] . " <strong>" . $wr . '</strong></li>';
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
        $query->execute(array($source));

        $query = OCP\DB::prepare("INSERT INTO  *PREFIX*metadata (`item`,`key`,`value`) VALUES(?,?,?) ");
        $query->execute(array($source, $key, $value));
    }

}

;
