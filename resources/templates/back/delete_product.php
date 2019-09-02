<?php require_once("../../config.php"); ?>

<?php

if(isset($_GET['id'])){
    $the_product_id = $_GET['id'];
    $query = query("DELETE FROM products WHERE product_id=".escape_string($the_product_id)."");
    confirm($query);
    set_message("Product Deleted");
    redirect("../../../public/admin/index.php?products");
} else{
    redirect("../../../public/admin/index.php?products");
}
?>


