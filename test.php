<?php
 $page =2; 
include './includes/load.php';
//phpinfo();

echo $db->query("DELIMITER $
CREATE DEFINER=`embarkdb`@`106.210.217.189` FUNCTION `SPLIT_STRING`(val TEXT, delim VARCHAR(12), pos INT) RETURNS text CHARSET latin1
	NO SQL
	DETERMINISTIC
BEGIN
        DECLARE output TEXT;
        SET output = REPLACE(SUBSTRING(SUBSTRING_INDEX(val, delim, pos), CHAR_LENGTH(SUBSTRING_INDEX(val, delim, pos - 1)) + 1), delim, '');
        IF output = '' THEN
            SET output = null;
        END IF;
        RETURN output;
    END $
DELIMITER ;;");


?>