files_meta
==========

File Metadata App for Owncloud.
Owncloud plugin: Meta data per file.
This plugin is used to add Description on file item. 
The Description is stored in the separate database oc_metadata.
The database is designed as key/value storage where we have following columns:
item,namespace,key,value
Actually the meta_data can be any data, one can extend files_meta plugin to store metadata not only for files.



change log:
10/29/2012
Migratged from gitorius to github


change log:
07/23/2012
Share folder handled: meta info is readonly
Additional information is show now only sharing and permission.
version 0.2 is released.


files_meta
an app dedicated  to store additional meta data per file.
The road map is following:
1) add "i" in action stack
2) on click show description
2.1) show owner and shared information, last edit
2.3) some additional text

