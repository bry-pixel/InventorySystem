<?php
  require_once('includes/load.php');

/*--------------------------------------------------------------*/
/* Function for find all database table rows by table name
/*--------------------------------------------------------------*/
function find_all($table) {
   global $db;
   if(tableExists($table))
   {
     return find_by_sql("SELECT * FROM ".$db->escape($table));
   }
}
/*--------------------------------------------------------------*/
/* Function for Perform queries
/*--------------------------------------------------------------*/
function find_by_sql($sql)
{
  global $db;
  $result = $db->query($sql);
  $result_set = $db->while_loop($result);
 return $result_set;
}
/*--------------------------------------------------------------*/
/*  Function for Find data from table by id
/*--------------------------------------------------------------*/
function find_by_id($table,$id)
{
  global $db;
  $id = (int)$id;
    if(tableExists($table)){
          $sql = $db->query("SELECT * FROM {$db->escape($table)} WHERE id='{$db->escape($id)}' LIMIT 1");
          if($result = $db->fetch_assoc($sql))
            return $result;
          else
            return null;
     }
}
/*--------------------------------------------------------------*/
/* Function for Delete data from table by id
/*--------------------------------------------------------------*/
function delete_by_id($table,$id)
{
  global $db;
  if(tableExists($table))
   {
    $sql = "DELETE FROM ".$db->escape($table);
    $sql .= " WHERE id=". $db->escape($id);
    $sql .= " LIMIT 1";
    $db->query($sql);
    return ($db->affected_rows() === 1) ? true : false;
   }
}
/*--------------------------------------------------------------*/
/* Function for Count id  By table name
/*--------------------------------------------------------------*/

function count_by_id($table){
  global $db;
  if(tableExists($table))
  {
    $sql    = "SELECT COUNT(id) AS total FROM ".$db->escape($table);
    $result = $db->query($sql);
     return($db->fetch_assoc($result));
  }
}
/*--------------------------------------------------------------*/
/* Determine if database table exists
/*--------------------------------------------------------------*/
function tableExists($table){
  global $db;
  $table_exit = $db->query('SHOW TABLES FROM '.DB_NAME.' LIKE "'.$db->escape($table).'"');
      if($table_exit) {
        if($db->num_rows($table_exit) > 0)
              return true;
         else
              return false;
      }
  }
 /*--------------------------------------------------------------*/
 /* Login with the data provided in $_POST,
 /* coming from the login form.
/*--------------------------------------------------------------*/
  function authenticate($username='', $password='') {
    global $db;
    $username = $db->escape($username);
    $password = $db->escape($password);
    $sql  = sprintf("SELECT id,username,password,user_level,status FROM users WHERE username ='%s' LIMIT 1", $username);
    $result = $db->query($sql);
    if($db->num_rows($result)){
      $user = $db->fetch_assoc($result);
      $password_request = sha1($password);
      if($password_request === $user['password'] ){
        return $user['id'];
      }
    }
   return false;
  }
  /*--------------------------------------------------------------*/
  /* Login with the data provided in $_POST,
  /* coming from the login_v2.php form.
  /* If you used this method then remove authenticate function.
 /*--------------------------------------------------------------*/
   function authenticate_v2($username='', $password='') {
     global $db;
     $username = $db->escape($username);
     $password = $db->escape($password);
     $sql  = sprintf("SELECT id,username,password,user_level,status FROM users WHERE username ='%s' LIMIT 1", $username);
     $result = $db->query($sql);
     if($db->num_rows($result)){
       $user = $db->fetch_assoc($result);
       $password_request = sha1($password);
       if($password_request === $user['password'] ){
         return $user;
       }
     }
    return false;
   }

  /*--------------------------------------------------------------*/
  /* Find current log in user by session id
  /*--------------------------------------------------------------*/
  function current_user(){
      static $current_user;
      global $db;
      if(!$current_user){
         if(isset($_SESSION['user_id'])):
             $user_id = intval($_SESSION['user_id']);
             $current_user = find_by_id('users',$user_id);
        endif;
      }
    return $current_user;
  }
  /*--------------------------------------------------------------*/
  /* Find all user by
  /* Joining users table and user gropus table
  /*--------------------------------------------------------------*/
  function find_all_user(){
      global $db;
      $results = array();
      $sql = "SELECT u.id,u.name,u.username,u.user_level,u.status,u.last_login,";
      $sql .="g.group_name ";
      $sql .="FROM users u ";
      $sql .="LEFT JOIN user_groups g ";
      $sql .="ON g.group_level=u.user_level ORDER BY u.name ASC";
      $result = find_by_sql($sql);
      return $result;
  }
  /*--------------------------------------------------------------*/
  /* Function to update the last log in of a user
  /*--------------------------------------------------------------*/

 function updateLastLogIn($user_id)
	{
		global $db;
    $date = make_date();
    $sql = "UPDATE users SET last_login='{$date}' WHERE id ='{$user_id}' LIMIT 1";
    $result = $db->query($sql);
    return ($result && $db->affected_rows() === 1 ? true : false);
	}

  /*--------------------------------------------------------------*/
  /* Find all Group name
  /*--------------------------------------------------------------*/
  function find_by_groupName($val)
  {
    global $db;
    $sql = "SELECT group_name FROM user_groups WHERE group_name = '{$db->escape($val)}' LIMIT 1 ";
    $result = $db->query($sql);
    return($db->num_rows($result) === 0 ? true : false);
  }
  /*--------------------------------------------------------------*/
  /* Find group level
  /*--------------------------------------------------------------*/
  function find_by_groupLevel($level)
  {
    global $db;
    $sql = "SELECT group_level FROM user_groups WHERE group_level = '{$db->escape($level)}' LIMIT 1 ";
    $result = $db->query($sql);
    return($db->num_rows($result) === 0 ? true : false);
  }
  /*--------------------------------------------------------------*/
  /* Function for cheaking which user level has access to page
  /*--------------------------------------------------------------*/
   function page_require_level($require_level){
     global $session;
     $current_user = current_user();
     $login_level = find_by_groupLevel($current_user['user_level']);
     if (!$session->isUserLoggedIn(true)):
            redirect('index.php', false);
  
     elseif(isset($current_user['status']) && $current_user['status'] === '0'):
            $session->logout();
            $session->msg('d','Your account has been deactivated.');
            redirect('index.php', false);
 
     elseif($current_user['user_level'] <= (int)$require_level):
              return true;
      else:
            $session->msg("d", "Sorry! you dont have permission to view the page.");
            redirect('home.php', false);
        endif;

     }
   /*--------------------------------------------------------------*/
   /* Function for Finding all product name
   /* JOIN with categorie  and media database table
   /*--------------------------------------------------------------*/
  function join_product_table(){
     global $db;
     $sql  =" SELECT p.id,p.name,p.quantity,unit,p.buy_price,p.sale_price,p.media_id,p.date,c.name";
    $sql  .=" AS categorie,m.file_name AS image";
    $sql  .=" FROM products p";
    $sql  .=" LEFT JOIN categories c ON c.id = p.categorie_id";
    $sql  .=" LEFT JOIN media m ON m.id = p.media_id";
    $sql  .=" ORDER BY p.name ASC";
    return find_by_sql($sql);

   }
  /*--------------------------------------------------------------*/
  /* Function for Finding all product name
  /* Request coming from ajax.php for auto suggest
  /*--------------------------------------------------------------*/

   function find_product_by_title($product_name){
     global $db;
     $p_name = remove_junk($db->escape($product_name));
     $sql = "SELECT name FROM products WHERE name like '%$p_name%' LIMIT 5";
     $result = find_by_sql($sql);
     return $result;
   }

  /*--------------------------------------------------------------*/
  /* Function for Finding all product info by product title
  /* Request coming from ajax.php
  /*--------------------------------------------------------------*/
  function find_all_product_info_by_title($title){
    global $db;
    $sql  = "SELECT * FROM products ";
    $sql .= " WHERE name ='{$title}'";
    $sql .=" LIMIT 1";
    return find_by_sql($sql);
  }

  /*--------------------------------------------------------------*/
  /* Function for Update product quantity
  /*--------------------------------------------------------------*/
  function update_product_qty($qty,$p_id){
    global $db;
    $qty = (int) $qty;
    $id  = (int)$p_id;
    $sql = "UPDATE products SET quantity=quantity -'{$qty}' WHERE id = '{$id}'";
    $result = $db->query($sql);
    return($db->affected_rows() === 1 ? true : false);

  }
  /*--------------------------------------------------------------*/
  /* Function for Display Recent product Added
  /*--------------------------------------------------------------*/
 function find_recent_product_added($limit){
   global $db;
   $sql   = " SELECT p.id,p.name,p.sale_price,p.media_id,c.name AS categorie,";
   $sql  .= "m.file_name AS image FROM products p";
   $sql  .= " LEFT JOIN categories c ON c.id = p.categorie_id";
   $sql  .= " LEFT JOIN media m ON m.id = p.media_id";
   $sql  .= " ORDER BY p.id DESC LIMIT ".$db->escape((int)$limit);
   return find_by_sql($sql);
 }
 /*--------------------------------------------------------------*/
 /* Function for Find Highest saleing Product
 /*--------------------------------------------------------------*/
 function find_higest_saleing_product($limit){
  global $db;
  $sql  = "SELECT p.name, p.unit, COUNT(s.product_id) AS totalSold, SUM(s.qty) AS totalQty ";
  $sql .= "FROM sales s ";
  $sql .= "LEFT JOIN products p ON p.id = s.product_id ";
  $sql .= "GROUP BY s.product_id ";
  $sql .= "ORDER BY SUM(s.qty) DESC LIMIT ".$db->escape((int)$limit);
  return $db->query($sql);
}

 /*--------------------------------------------------------------*/
 /* Function for find all sales
 /*--------------------------------------------------------------*/
 function find_all_sale(){
  global $db;
  $sql  = "SELECT s.id, 
                  s.qty, 
                  s.price, 
                  s.date, 
                  p.name, 
                  p.unit
           FROM sales s
           LEFT JOIN products p ON s.product_id = p.id
           ORDER BY s.date DESC";
  return find_by_sql($sql);
}

 /*--------------------------------------------------------------*/
 /* Function for Display Recent sale
 /*--------------------------------------------------------------*/
function find_recent_sale_added($limit){
  global $db;
  $sql  = "SELECT s.id,s.qty,s.price,s.date,p.name";
  $sql .= " FROM sales s";
  $sql .= " LEFT JOIN products p ON s.product_id = p.id";
  $sql .= " ORDER BY s.date DESC LIMIT ".$db->escape((int)$limit);
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
/* Function for Generate sales report by two dates
/*--------------------------------------------------------------*/
function find_sale_by_dates($start_date,$end_date){
  global $db;
  $start_date  = date("Y-m-d", strtotime($start_date));
  $end_date    = date("Y-m-d", strtotime($end_date));

  $sql  = "SELECT 
              DATE(s.date) AS sale_date, 
              p.name, 
              p.unit, 
              p.buy_price,
              p.sale_price,
               SUM(s.qty) AS total_sales, 
                  SUM(p.sale_price * s.qty) AS total_saleing_price, 
                  SUM(p.buy_price * s.qty) AS total_buying_price
           FROM sales s 
           LEFT JOIN products p ON s.product_id = p.id 
           WHERE DATE(s.date) BETWEEN '{$start_date}' AND '{$end_date}' 
           GROUP BY DATE(s.date), s.product_id, p.unit 
           ORDER BY sale_date DESC";
  return find_by_sql($sql);
}



/*--------------------------------------------------------------*/
/*  Function for Generate Today's Sales Report */
/*--------------------------------------------------------------*/
function dailySales() {
  global $db;
  $today = date('Y-m-d');
  $sql = "SELECT 
              p.name, 
              p.unit, 
              SUM(s.qty) AS total_qty, 
              SUM(p.sale_price * s.qty) AS total_saleing_price, 
              DATE_FORMAT(s.date, '%Y-%m-%d') AS date
          FROM sales s
          LEFT JOIN products p ON s.product_id = p.id
          WHERE DATE(s.date) = '{$today}'
          GROUP BY s.product_id, p.unit, DATE(s.date)
          ORDER BY p.name ASC";
          
  return find_by_sql($sql);
}



/*--------------------------------------------------------------*/
/*  Function for Generate Monthly Sales Report (with Unit)
/*--------------------------------------------------------------*/
function monthlySales($year) {
  global $db;
  $current_month = date('m');

  $sql  = "SELECT s.qty, ";
  $sql .= "p.unit, ";
  $sql .= "DATE_FORMAT(s.date, '%Y-%m') AS date, "; 
  $sql .= "p.name, ";
  $sql .= "SUM(s.qty) AS total_qty, ";
  $sql .= "SUM(p.sale_price * s.qty) AS total_saleing_price ";
  $sql .= "FROM sales s ";
  $sql .= "LEFT JOIN products p ON s.product_id = p.id ";
  $sql .= "WHERE DATE_FORMAT(s.date, '%Y') = '{$year}' ";
  $sql .= "AND DATE_FORMAT(s.date, '%m') = '{$current_month}' "; 
  $sql .= "GROUP BY s.product_id, p.unit ";
  $sql .= "ORDER BY p.name ASC";

  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function to fetch all products safely with UOM fallback */
/*--------------------------------------------------------------*/
function find_all_products_safe() {
  global $db;
  $sql = "SELECT p.id, p.name, p.quantity, COALESCE(p.unit,'pcs') AS unit,
                 p.buy_price, p.sale_price, p.date
          FROM products p ORDER BY p.id ASC";
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
/* Function of product search */
/*--------------------------------------------------------------*/
function search_product($search_term) {
  global $db;
  $search_term = $db->escape($search_term);
  $sql = "SELECT p.id, p.name, p.quantity, COALESCE(p.unit, 'pcs') AS unit,
                 p.buy_price, p.sale_price, p.date
          FROM products p
          WHERE p.name LIKE '%{$search_term}%'
          OR p.id = '{$search_term}'
          ORDER BY p.id ASC";
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
/* Function of months chart  reflect accurate total sales for each month
that from sales table*/
/*--------------------------------------------------------------*/
function get_months_chart_data() {
  global $db;
  $current_year = date('Y');
  $sql = "SELECT 
            DATE_FORMAT(s.date, '%M') AS month, 
            SUM(s.qty * s.price) AS total_sales
          FROM sales s
          WHERE DATE_FORMAT(s.date, '%Y') = '{$current_year}'
          GROUP BY DATE_FORMAT(s.date, '%m'), DATE_FORMAT(s.date, '%M')
          ORDER BY DATE_FORMAT(s.date, '%m') ASC";

  $result = find_by_sql($sql);

  $months = [];
  $sales  = [];

  foreach ($result as $row) {
      $months[] = $row['month'];
      $sales[]  = (float)$row['total_sales'];
  }

  return ['months' => $months, 'sales' => $sales];
}
/*--------------------------------------------------------------*/
/* Function to get lowest selling products with unit
/*--------------------------------------------------------------*/
function get_lowest_selling_products($limit = 5) {
  global $db;
  $sql = "
      SELECT p.name, p.unit, SUM(s.qty) AS total_sold,SUM(s.qty * p.sale_price) AS total_sales
      FROM sales s
      JOIN products p ON p.id = s.product_id
      GROUP BY s.product_id
      ORDER BY total_sold ASC
      LIMIT " . (int)$limit;
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
/* Function to get top products with unit
/*--------------------------------------------------------------*/
function get_top_products($limit = 5) {
  global $db;
  $sql = "
      SELECT p.name, p.unit, SUM(s.qty) AS total_sold,
             SUM(s.qty * p.sale_price) AS total_sales
      FROM sales s
      JOIN products p ON p.id = s.product_id
      GROUP BY s.product_id
      ORDER BY total_sold DESC
      LIMIT " . (int)$limit;
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Transaction helpers: group sales by transaction timestamp    */
/*--------------------------------------------------------------*/
function find_transaction_items($key) {
  global $db;
  if (is_numeric($key)) {
    $id = (int)$key;
    $sql = "SELECT 
              s.id,
              s.product_id,
              s.qty,
              s.price,
              s.date,
              p.name,
              p.unit AS unit
            FROM sales s
            LEFT JOIN products p ON p.id = s.product_id
            WHERE s.transaction_id = '{$id}'
            ORDER BY s.id ASC";
    return find_by_sql($sql);
  }


  $time = $db->escape($key);
  $sql = "SELECT 
            s.id,
            s.product_id,
            s.qty,
            s.price,
            s.date,
            p.name,
            p.unit AS unit
          FROM sales s
          LEFT JOIN products p ON p.id = s.product_id
          LEFT JOIN transactions t ON t.id = s.transaction_id
          WHERE (s.date = '{$time}' OR t.txn_time = '{$time}')
          ORDER BY s.id ASC";
  return find_by_sql($sql);
}

function find_transactions($limit = null) {
  global $db;
  $sql = "SELECT 
            t.id AS txn_id,
            t.txn_time AS txn_time,
            COALESCE(COUNT(s.id), 0) AS item_count,
            COALESCE(SUM(s.qty), 0) AS total_qty,
            COALESCE(SUM(s.qty * s.price), 0) AS total_amount
          FROM transactions t
          LEFT JOIN sales s ON s.transaction_id = t.id
          GROUP BY t.id
          ORDER BY t.txn_time DESC";
  if ($limit !== null) {
    $sql .= " LIMIT ".(int)$limit;
  }
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
/* Function to get inventory valuation report
/*--------------------------------------------------------------*/
function get_inventory_valuation() {
  global $db;
  $sql = "SELECT 
            p.id,
            p.name,
            p.unit,
            p.quantity,
            p.buy_price,
            p.sale_price
          FROM products p
          WHERE p.quantity > 0
          ORDER BY p.name ASC";
  return find_by_sql($sql);
}



/*--------------------------------------------------*/
/* Functions for Sales Report by two dates*/ 
/*--------------------------------------------------*/

function se_get_sales_summary($start,$end){
  global $db;
  $sql = "SELECT 
            COALESCE(SUM(s.qty*s.price),0) AS total_sales,
            COALESCE(SUM(s.qty),0) AS items_sold,
            COUNT(*) AS transactions,
            COALESCE(AVG(s.qty*s.price),0) AS avg_sale
          FROM sales s 
          LEFT JOIN products p ON p.id=s.product_id
          WHERE DATE(s.date) BETWEEN '{$db->escape($start)}' AND '{$db->escape($end)}'";
  $res = find_by_sql($sql);
  return $res ? $res[0] : ['total_sales'=>0,'items_sold'=>0,'transactions'=>0,'avg_sale'=>0];
}

function se_get_daily_totals($start,$end){
  global $db;
  $sql = "SELECT DATE(s.date) AS d, COALESCE(SUM(s.qty*s.price),0) AS total
          FROM sales s
          LEFT JOIN products p ON p.id=s.product_id
          WHERE DATE(s.date) BETWEEN '{$db->escape($start)}' AND '{$db->escape($end)}'
          GROUP BY DATE(s.date)
          ORDER BY d";
  return find_by_sql($sql);
}

function se_get_top_products($start,$end,$limit=5){
  global $db;
  $sql = "SELECT p.name AS product_name, COALESCE(SUM(s.qty*s.price),0) AS total,
                 COALESCE(SUM(s.qty),0) AS total_sold, p.unit AS unit
          FROM sales s
          LEFT JOIN products p ON p.id=s.product_id
          WHERE DATE(s.date) BETWEEN '{$db->escape($start)}' AND '{$db->escape($end)}'
          GROUP BY p.id
          ORDER BY total DESC
          LIMIT ".(int)$limit;
  return find_by_sql($sql);
}

function se_get_sales_rows($start,$end){
  global $db;
  $sql = "SELECT s.id, DATE(s.date) AS date, s.qty, s.price,
                 (s.qty*s.price) AS total,
                 p.name AS product_name
          FROM sales s
          LEFT JOIN products p ON p.id=s.product_id
          WHERE DATE(s.date) BETWEEN '{$db->escape($start)}' AND '{$db->escape($end)}'
          ORDER BY s.date DESC";
  return find_by_sql($sql);
}
/*--------------------------------------------------*/
/* Function to activity log
/*--------------------------------------------------*/
function log_activity($action, $description = '', $page = '') {
  global $db;
  if (!isset($_SESSION['user_id'])) return;

  $user_id = (int)$_SESSION['user_id'];
  $action = $db->escape($action);
  $description = $db->escape($description);
  $page = $db->escape($page);

  $sql  = "INSERT INTO activity_logs (user_id, action, description, page) ";
  $sql .= "VALUES ('{$user_id}', '{$action}', '{$description}', '{$page}')";
  $db->query($sql);
}






?>
