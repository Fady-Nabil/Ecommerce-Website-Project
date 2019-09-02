<?php require_once("../../config.php"); ?>

<?php

if(isset($_GET['id'])){
    $the_report_id = $_GET['id'];
    $query = query("DELETE FROM reports WHERE report_id=".escape_string($the_report_id)."");
    confirm($query);
    set_message("Report Deleted");
    redirect("../../../public/admin/index.php?reports");
} else{
    redirect("../../../public/admin/index.php?reports");
}
?>


