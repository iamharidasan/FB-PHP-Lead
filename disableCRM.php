<?php
include("DB.php");
if(isset($_POST['formid'])){    
    $deleteQuery = "DELETE FROM `".TABLE_PREFIX."forms` WHERE `formid` = '".$_POST['formid']."'";
    $db->query($deleteQuery);    
    echo "{status:'Success'}";
}else{
    header('Location: index.php');
}