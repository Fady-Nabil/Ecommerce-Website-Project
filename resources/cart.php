<?php require_once("config.php"); ?>

<?php
//increment quantity of product in shopping cart
if(isset($_GET['add'])){
    $query = query("SELECT * FROM products WHERE product_id=".escape_string($_GET['add'])." ");
    confirm($query);
    while($row = fetch_array($query)){
        $product_title = $row['product_title'];
        $product_quantity = $row['product_quantity'];
        if($product_quantity != $_SESSION['product_' . $_GET['add']]){
            $_SESSION['product_' . $_GET['add']]+=1;
            redirect("../public/checkout.php");
        } else {
            set_message("We only have " . $product_quantity . " " . "Available" ." "."From "."$product_title");
            redirect("../public/checkout.php");
        }
    }
}
//decrement quantity of product in shopping cart
if(isset($_GET['remove'])){
    $_SESSION['product_' . $_GET['remove']] -=1;
    if( $_SESSION['product_' . $_GET['remove']] < 1){
        unset($_SESSION['item_total']);
        unset($_SESSION['item_quantity']);
        redirect("../public/checkout.php");
    } else {
        redirect("../public/checkout.php");
    }
}
//removing product from shopping cart
if(isset($_GET['delete'])){
    $_SESSION['product_' . $_GET['delete']] = '0';
    unset($_SESSION['item_total']);
    unset($_SESSION['item_quantity']);
    redirect("../public/checkout.php");
}

//cart function
function cart(){
    /* this function use to display cart to user display the products
     * that he would like to buy it
     * so how to display these products: the answer by using session
     * in the session we stored all the products then we made foreach loop
     * on the session array and make an if statement to display product if exists
     * and we made if to display products if != to 'product_' after that
     * we get length and id depend on this id we will put it in the select query
     * to select the products that user bought.
     * for example user click on product called product_1
     * then name = product_1
     * length of name = 9
     * len_id = length - 8 = 9 - 8 =1
     * therefore id = 1
     * when user click on product called product_2
     * continue all process
     * and I will use $value that comes from session
     *  to display real quantity for user
     */
    $total = 0;
    $item_quantity = 0;
    $sub = 0;
    $item_name = 1;
    $item_number = 1;
    $amount = 1;
    $quantity = 1;
    foreach ($_SESSION as $name => $value) {
        if($value > 0) {
            if(substr($name,0,8) == "product_"){
                $length = strlen($name);
                $len_id =  $length - 8;
                $id = substr($name, 8, $len_id);
                $query =query("SELECT * FROM products WHERE product_id = ".escape_string($id)." ");
                confirm($query);
                while($row =fetch_array($query)){
                    $product_id       = $row['product_id'];
                    $product_title    = $row['product_title'];
                    $product_price    = $row['product_price'];
                    $product_image    = $row['product_image'];
                    $sub = $product_price * $value;
                    $item_quantity += $value;
                    $product_image = display_image($product_image);
                    $cart = <<<DELIMETER
    <tr>
        <td>{$product_title}<br>
        <img width="100" src="../resources/{$product_image}" alt="">
        </td>
        <td>&#36;{$product_price}</td>
        <td>{$value}</td>
        <td>&#36;{$sub}</td>
        <td>
            <a class="btn btn-warning" href="../resources/cart.php?remove={$product_id}">
                <span class="glyphicon glyphicon-minus"></span>
            </a>
            <a class="btn btn-success" href="../resources/cart.php?add={$product_id}">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
            <a class="btn btn-danger" href="../resources/cart.php?delete={$product_id}">
                <span class="glyphicon glyphicon-remove"></span>
            </a>
        </td>
    </tr>
    <input type="hidden" name="item_name_{$item_name}" value="{$row['product_title']}">
    <input type="hidden" name="item_number_{$item_number}" value="{$row['product_id']}">
    <input type="hidden" name="amount_{$amount}" value="{$row['product_price']}">
    <input type="hidden" name="quantity_{$quantity}" value="{$value}">
DELIMETER;

                }//end while
                echo $cart;
                $item_name++;
                $item_number++;
                $amount++;
                $quantity++;
            }//end if
            $_SESSION['item_total'] = $total += $sub;
            $_SESSION['item_quantity']  = $item_quantity;
        }//end foreach
    }//end if
}//end function

//function to display buy now button
function show_paypal(){
    if(isset($_SESSION['item_quantity'])&& $_SESSION['item_quantity']>= 1) {
    $paypal_button = <<<DELIMETER
<input type="image" name="upload"
               src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif"
               alt="PayPal - The safer, easier way to pay online">
DELIMETER;
return $paypal_button;
    }//end if
}//end function

//process transaction function
function process_transaction(){
    /*
     * this function use to display reports
     */
    if(isset($_GET['tx'])) {
        $amount = $_GET['amt'];
        $currency = $_GET['cc'];
        $transaction = $_GET['tx'];
        $status = $_GET['st'];
        $total = 0;
        $item_quantity = 0;
        $sub = 0;
        foreach ($_SESSION as $name => $value) {
            if ($value > 0) {
                if (substr($name, 0, 8) == "product_") {
                    $length = strlen($name);
                    $len_id = $length - 8;
                    $id = substr($name, 8, $len_id);

                    /*insert orders in database*/
                    $send_order = query("INSERT INTO orders (order_amount, order_transaction, order_currency, order_status) VALUES ('{$amount}','{$transaction}','{$currency}','{$status}')");
                    $last_id = last_id();/*last order inserted in orders table I get the id of this order*/
                    confirm($send_order);

                    /*insert products in database*/
                    $query = query("SELECT * FROM products WHERE product_id = " . escape_string($id) . " ");
                    confirm($query);

                    while ($row = fetch_array($query)) {
                        $product_id = $row['product_id'];
                        $product_title = $row['product_title'];
                        $product_price = $row['product_price'];
                        $sub = $product_price * $value;
                        $item_quantity += $value;

                        $insert_report = query("INSERT INTO reports (product_id,order_id,product_title,product_price,Product_quantity) VALUES ('{$id}','{$last_id}','{$product_title}','{$product_price}','{$value}')");
                        confirm($insert_report);
                    }//end while
                }//end if (substr($name, 0, 8) == "product_")
                $total += $sub;
                $item_quantity;
            }//end if ($value > 0)
        }//end foreach
    }//end if (isset($_GET['tx'])
    else {
        redirect("index.php");
    }//end else
}//end function
?>