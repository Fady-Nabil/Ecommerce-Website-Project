<?php require_once("../../config.php"); ?>

<?php

if(isset($_GET['id'])){
    $the_user_id = $_GET['id'];
    $query = query("DELETE FROM users WHERE user_id=".escape_string($the_user_id)."");
    confirm($query);
    set_message("User Deleted");
    redirect("../../../public/admin/index.php?users");
} else{
    redirect("../../../public/admin/index.php?users");
}
?>


