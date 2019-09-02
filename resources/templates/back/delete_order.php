<?php require_once("../../config.php"); ?>

<?php

if(isset($_GET['id'])){
    $the_order_id = $_GET['id'];
    $query = query("DELETE FROM orders WHERE order_id=".escape_string($the_order_id)."");
    confirm($query);
    set_message("Order Deleted");
    redirect("../../../public/admin/index.php?orders");
} else{
    redirect("../../../public/admin/index.php?orders");
}
?>


