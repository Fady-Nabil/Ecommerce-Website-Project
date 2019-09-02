<?php require_once("../../config.php"); ?>

<?php

if(isset($_GET['id'])){
    $the_cat_id = $_GET['id'];
    $query = query("DELETE FROM categories WHERE cat_id=".escape_string($the_cat_id)."");
    confirm($query);
    set_message("Category Deleted");
    redirect("../../../public/admin/index.php?categories");
} else{
    redirect("../../../public/admin/index.php?categories");
}
?>


