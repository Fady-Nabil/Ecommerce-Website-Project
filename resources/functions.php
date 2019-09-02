<?php

$uploads = "uploads";
/************************ helper functions ********************/
//set message function
function set_message($msg){
    if(!empty($msg)){
        $_SESSION['message'] = $msg;
    } else {
        $msg = "";
    }
}

//display function
function display_message(){
    if(isset($_SESSION['message'])){
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
}
//redirect function
function redirect($location){
    header("Location: $location");
}

//query function
function query($sql){
    global $connection;
    return mysqli_query($connection,$sql);
}

//confirm function
function confirm($result){
    global $connection;
     if(!$result) {
         die("QUERY FAILED ".mysqli_error($connection));
     }
}

//escape string function
function escape_string($string){
    global $connection;
    return mysqli_real_escape_string($connection,$string);
}

//fetch array function
function fetch_array($result){
    return mysqli_fetch_array($result);
}
//mysqli insert id function
/*to get the last id of orders inserted in orders table*/
function last_id(){
    global $connection;
   return mysqli_insert_id($connection);
}
/************************ helper functions ********************/

/************************ front end functions ********************/
//get products
function get_products(){
   $query =  query("SELECT * FROM products");
   confirm($query);
   while($row = fetch_array($query)) {
       $product_id = $row['product_id'];
       $product_title = $row['product_title'];
       $product_price = $row['product_price'];
       $product_desc = $row['product_description'];
       $product_image = $row ['product_image'];

       $product_image = display_image($product_image);

       $product = <<<DELIMETER
<div class="col-sm-4 col-lg-4 col-md-4">
    <div class="thumbnail">
        <a href="item.php?id={$product_id}"><img src="../resources/{$product_image}" alt=""></a>
        <div class="caption">
            <h4 class="pull-right">&#36;{$product_price}</h4>
            <h4><a href="item.php?id={$product_id}">{$product_title}</a>
            </h4>
            <p>{$product_desc} <a target="_blank" href="http://www.bootsnipp.com">Bootsnipp - http://bootsnipp.com</a>.</p>
            <a class="btn btn-primary" target="_blank" href="../resources/cart.php?add={$product_id}">Add To Cart</a>
        </div>
    </div>
</div>
DELIMETER;
       echo $product;

   }
}

//get categories
function get_categories(){
        $query = query("SELECT * FROM categories");
        confirm($query);
        while ($row = fetch_array($query)) {
            $cat_id = $row['cat_id'];
            $cat_title = $row['cat_title'];
            $category_links = <<<DELIMETER
        <a href='category.php?id={$cat_id}' class='list-group-item'>{$cat_title}</a>
DELIMETER;
            echo $category_links;
        }
}
//get products in category page
function get_products_in_cat_page(){
    if (isset($_GET['id'])) {
        $the_id = $_GET['id'];
        $query = query("SELECT * FROM products WHERE product_id = ". escape_string($the_id). "");
        confirm($query);
        while ($row = fetch_array($query)) {
            $product_id = $row['product_id'];
            $product_title = $row['product_title'];
            $product_price = $row['product_price'];
            $product_desc = $row['product_description'];
            $product_image = $row ['product_image'];
            $product_image = display_image($product_image);
            $product = <<<DELIMETER
        <div class="col-md-3 col-sm-6 hero-feature">
            <div class="thumbnail">
                <img src="../resources/{$product_image}" alt="">
                <div class="caption">
                    <h4 class="pull-right">&#36;{$product_price}</h4>
                    <h3> {$product_title}</h3>
                    <p> {$product_desc}</p>
                    <p>
                        <a href="../resources/cart.php?add=$product_id" class="btn btn-primary">Buy Now!</a> <a href="item.php?id={$product_id}" class="btn btn-default">More Info</a>
                    </p>
                </div>
            </div>
        </div>
DELIMETER;
            echo $product;

        }
    }
}

//get products in shop page
function get_products_in_shop_page(){
        $query = query("SELECT * FROM products");
        confirm($query);
        while ($row = fetch_array($query)) {
            $product_id = $row['product_id'];
            $product_title = $row['product_title'];
            $product_price = $row['product_price'];
            $product_desc = $row['product_description'];
            $product_image = $row ['product_image'];
            $product_image = display_image($product_image);
            $product = <<<DELIMETER
        <div class="col-md-3 col-sm-6 hero-feature">
            <div class="thumbnail">
                <img src="../resources/{$product_image}" alt="">
                <div class="caption">
                    <h4 class="pull-right">&#36;{$product_price}</h4>
                    <h3> {$product_title}</h3>
                    <p> {$product_desc}</p>
                    <p>
                        <a href="../resources/cart.php?add=$product_id" class="btn btn-primary">Buy Now!</a> <a href="item.php?id={$product_id}" class="btn btn-default">More Info</a>
                    </p>
                </div>
            </div>
        </div>
DELIMETER;
            echo $product;

        }
}

//function login user
function login_user(){
    if(isset($_POST['submit'])){
        $username = escape_string($_POST['username']);
        $password = escape_string($_POST['password']);
        $query = query("SELECT * FROM users WHERE username = '{$username}' AND password = '{$password}'");
        confirm($query);

        if(mysqli_num_rows($query) == 0){
            set_message("Your Password or Usename are Wrong");
            redirect("login.php");
        } else {
            $_SESSION['username'] = $username;
            set_message("Welcome to Admin {$username}");
            redirect("admin");
        }
    }
}

//send message function
function send_message(){
    if(isset($_POST['submit'])) {
        $to = "fadynabil123@outlook.com";
        $from_name = $_POST['name'];
        $subject = $_POST['subject'];
        $email = $_POST['email'];
        $message = $_POST['message'];

        $headers = "From: {$from_name} {$email}";

        $result = mail($to, $subject, $message, $headers);

        if(!$result) {
            set_message("sorry we couldn't send your Message");
            redirect("contact.php");
        } else {
            set_message("Your Message has been sent");
        }
    }

}

/************************ front end functions ********************/

/************************ back end functions ********************/
//function to display orders in admin
function display_orders()
{
    $query = query("SELECT * FROM orders");
    confirm($query);
    while ($row = fetch_array($query)) {
        $order_id          = $row['order_id'];
        $order_amount      = $row['order_amount'];
        $order_transaction = $row['order_transaction'];
        $order_status      = $row['order_status'];
        $order_currency    = $row['order_currency'];
        $orders = <<<DELIMETER
        <tr>
            <td>{$order_id}</td>
            <td>{$order_amount}</td>
            <td>{$order_transaction}</td>
            <td>{$order_status}</td>
            <td>{$order_currency}</td>
            <td><a href="../../resources/templates/back/delete_order.php?id=$order_id" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a></td>
        </tr>
DELIMETER;
    echo $orders;
    }//end while
}//end function

//function to display products in admin
function get_products_in_admin()
{
    $query = query("SELECT * FROM products");
    confirm($query);
    while ($row = fetch_array($query)) {
        $product_id           = $row['product_id'];
        $product_title        = $row['product_title'];
        $product_category_id  = $row['product_category_id'];
        $category = show_product_category_title($product_category_id);
        $product_price        = $row['product_price'];
        $product_quantity     = $row['product_quantity'];
        $product_description  = $row['product_description'];
        $product_image        = $row['product_image'];
        $short_desc           = $row['short_desc'];

        $product_image = display_image($product_image);

        $products = <<<DELIMETER
        <tr>
            <td>{$product_id}</td>
            <td>{$product_title}<br>
               <a href="index.php?edit_product&id={$product_id}">
                    <img width="100" src="../../resources/{$product_image}" alt="">
               </a>
            </td>
            <td>{$category}</td>
            <td>{$product_price}</td>
            <td>{$product_quantity}</td>
            <td>{$product_description}</td>
            <td>{$short_desc}</td>
            <td><a href="../../resources/templates/back/delete_product.php?id=$product_id" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a></td>
        </tr>
DELIMETER;
        echo $products;
    }//end while
}//end function

//function to add products in admin
function add_product(){
    if(isset($_POST['publish'])){
        $product_title       = escape_string($_POST['product_title']);
        $product_category_id = escape_string($_POST['product_category_id']);
        $product_price       = escape_string($_POST['product_price']);
        $product_description = escape_string($_POST['product_description']);
        $product_quantity    = escape_string($_POST['product_quantity']);
        $product_short_desc  = escape_string($_POST['short_desc']);

        $product_image       = $_FILES['file']['name'];
        $image_temp_location = $_FILES['file']['tmp_name'];

        move_uploaded_file($image_temp_location ,UPLOAD_DIRECTORY . DS . $product_image);
        $query = query("INSERT INTO products(product_title,product_category_id,product_price,product_quantity,product_description,product_image,short_desc)VALUES ('{$product_title}','{$product_category_id}','{$product_price}','{$product_quantity}','{$product_description}','{$product_image}','{$product_short_desc}')");
        $last_id = last_id();
        confirm($query);
        set_message("New Product with id {$last_id} Was Added");
        redirect("index.php?products");
    }
}
//get categories and display it in product page
function get_categories_add_product_page(){
    $query = query("SELECT * FROM categories");
    confirm($query);
    while ($row = fetch_array($query)) {
        $cat_id = $row['cat_id'];
        $cat_title = $row['cat_title'];
        $category_options = <<<DELIMETER
         <option value="{$cat_id}">{$cat_title}</option>
DELIMETER;
        echo $category_options;
    }
}

//function to display category title in products page
function show_product_category_title($product_category_id){
    $category_query = query("SELECT * FROM categories WHERE cat_id = '{$product_category_id}' ");
    confirm($category_query);
    while ($row = fetch_array($category_query)){
            return $row['cat_title'];
    }//end while
}//end function

//function to display image
function display_image($picture){
    global $uploads;
    return $uploads . DS . $picture;
}

//function to update products in admin
function update_product(){
    if(isset($_POST['update'])){
        $product_title       = escape_string($_POST['product_title']);
        $product_category_id = escape_string($_POST['product_category_id']);
        $product_price       = escape_string($_POST['product_price']);
        $product_description = escape_string($_POST['product_description']);
        $product_quantity    = escape_string($_POST['product_quantity']);
        $product_short_desc  = escape_string($_POST['short_desc']);

        $product_image       = $_FILES['file']['name'];
        $image_temp_location = $_FILES['file']['tmp_name'];

        if(empty($product_image)){
            $get_pic = query("SELECT product_image FROM products WHERE product_id=".escape_string($_GET['id']). " ");
            confirm($get_pic);
           while ($row = fetch_array($get_pic)){
               $product_image = $row['product_image'];
           }
        }

        move_uploaded_file($image_temp_location ,UPLOAD_DIRECTORY . DS . $product_image);
        $query = " UPDATE products SET ";
        $query .="product_title       = '{$product_title }     ',";
        $query .="product_category_id = '{$product_category_id}',";
        $query .="product_price       = '{$product_price}      ',";
        $query .="product_description = '{$product_description}',";
        $query .="product_quantity    = '{$product_quantity}   ',";
        $query .="short_desc          = '{$product_short_desc} ',";
        $query .="product_image       = '{$product_image}      ' ";
        $query .="WHERE product_id= ".escape_string($_GET['id']);
        $send_update_query = query($query);
        confirm($send_update_query);
        set_message("Product has been Updated");
        redirect("index.php?products");
    }
}

//function to display categories in admin
function show_categories_in_admin(){
    $query ="SELECT * FROM categories";
    $category_query = query($query);
    confirm($category_query);
    while ($row = fetch_array($category_query)){
        $cat_id = $row['cat_id'];
        $cat_title = $row['cat_title'];
        $categories = <<<DELIMETER
         <tr>
            <td>{$cat_id}</td>
            <td>{$cat_title}</td>
            <td><a href="../../resources/templates/back/delete_category.php?id=$cat_id" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a></td>
         </tr>
DELIMETER;
    echo  $categories;
    }
}

//function to add categories in admin
function add_category(){
    if(isset($_POST['add_category'])){
        $cat_title = escape_string($_POST['cat_title']);
        if(empty($cat_title) || $cat_title == " "){
            set_message("This can't be empty");
        } else {
            $query = query("INSERT INTO categories(cat_title) Values('{$cat_title}')");
            confirm($query);
            set_message("Category Created");
            redirect("index.php?categories");
        }
    }
}

//function to display users in admin
function display_users(){
    $query = query("SELECT * FROM users");
    confirm($query);
    while ($row = fetch_array($query)){
        $user_id  = $row['user_id'];
        $username = $row['username'];
        $email    = $row['email'];
        $password = $row['password'];
        set_message("User Created");
        $users = <<<DELIMETER
    <tr>
        <td>{$user_id}</td>
        <td>{$username}</td>
        <td>{$email}</td>
        <td><a href="../../resources/templates/back/delete_user.php?id=$user_id" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a></td>
    </tr>
DELIMETER;
    echo $users;
    }
}

//function to add users in admin
function add_user(){
    if(isset($_POST['add_user'])){
     $username  =  escape_string($_POST['username']);
     $email     =  escape_string($_POST['email']);
      $password = escape_string($_POST['password']);

      $query = query("INSERT INTO users (username,email,password) VALUES ('{$username}','{$email}','{$password}')");
      confirm($query);
      set_message("User Created");
      redirect("index.php?users");
    }
}

//function to display products in admin
function get_reports()
{
    $query = query("SELECT * FROM reports");
    confirm($query);
    while ($row = fetch_array($query)) {
        $report_id            = $row['report_id'];
        $product_id           = $row['product_id'];
        $order_id             = $row['order_id'];
        $order_title        = $row['product_title'];
        $order_price        = $row['product_price'];
        $order_quantity     = $row['product_quantity'];

        $reports = <<<DELIMETER
        <tr>
            <td>{$report_id}</td>
            <td>{$product_id}</td>
            <td>{$order_id}</td>
            <td>{$order_title}</td>
            <td>{$order_price}</td>
            <td>{$order_quantity}</td>
            <td><a href="../../resources/templates/back/delete_report.php?id=$report_id" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a></td>
        </tr>
DELIMETER;
        echo $reports;
    }//end while
}//end function
/************************ back end functions ********************/