files_meta
==========

File Metadata App for Owncloud.
Owncloud plugin: Meta data per file.
This plugin is used to add Description on file item. 
The Description is stored in the separate database oc_metadata.
The database is designed as key/value storage where we have following columns:
item,namespace,key,value
Actually the meta_data can be any data, one can extend files_meta plugin to store metadata not only for files.

