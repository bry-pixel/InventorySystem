<?php
 $errors = array();

 /*--------------------------------------------------------------*/
 /* Function for Remove escapes special
 /* characters in a string for use in an SQL statement
 /*--------------------------------------------------------------*/
function real_escape($str){
  global $con;
  $escape = mysqli_real_escape_string($con,$str);
  return $escape;
}
/*--------------------------------------------------------------*/
/* Function for Remove html characters
/*--------------------------------------------------------------*/
function remove_junk($str){
  $str = nl2br($str);
  $str = htmlspecialchars(strip_tags($str), ENT_QUOTES);
  return $str;
}
/*--------------------------------------------------------------*/
/* Function for Uppercase first character
/*--------------------------------------------------------------*/
function first_character($str) {
  if (!is_string($str)) {
      return ''; } elseif (mb_strlen($str) == 0) {
      return ''; } elseif (mb_strlen($str) == 1) {
      return mb_strtoupper($str, 'UTF-8');
      
  }
  $str = trim(str_replace('-', ' ', $str));
  return mb_strtoupper(mb_substr($str, 0, 1, 'UTF-8')) .
         mb_substr($str, 1, null, 'UTF-8');
}

/*--------------------------------------------------------------*/
/* Function for Checking input fields not empty
/*--------------------------------------------------------------*/
function validate_fields($var){
  global $errors;
  foreach ($var as $field) {
    $val = remove_junk($_POST[$field]);
    if(isset($val) && $val==''){
      $errors[] = $field . " can't be blank.";
    }
  }
}
/*--------------------------------------------------------------*/
/* Function for Display Session Message
   Ex echo displayt_msg($message);
/*--------------------------------------------------------------*/
function display_msg($msg = []) {
  $output = '';
  if (!empty($msg)) {
      foreach ($msg as $type => $value) {
          if (is_array($value)) {
              $value = implode(', ', $value);
          }
          $output .= "<div class=\"alert alert-{$type}\">";
          $output .= "<a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>";
          $output .= remove_junk(first_character($value));
          $output .= "</div>";
      }
  }
  return $output;
}

/*--------------------------------------------------------------*/
/* Function for redirect
/*--------------------------------------------------------------*/
function redirect($url, $permanent = false)
{
    if (headers_sent() === false)
    {
      header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
    }

    exit();
}
/*--------------------------------------------------------------*/
/* Function for find out total saleing price, buying price and profit
/*--------------------------------------------------------------*/
function total_price($totals){
   $sum = 0;
   $sub = 0;
   foreach($totals as $total ){
     $sum += $total['total_saleing_price'];
     $sub += $total['total_buying_price'];
     $profit = $sum - $sub;
   }
   return array($sum,$profit);
}
/*--------------------------------------------------------------*/
/* Function for Readable date time
/*--------------------------------------------------------------*/
function read_date($str){
     if($str)
      return date('F j, Y, g:i:s a', strtotime($str));
     else
      return null;
  }
/*--------------------------------------------------------------*/
/* Function for  Readable Make date time
/*--------------------------------------------------------------*/
function make_date(){
  return date('Y-m-d H:i:s');
}
/*--------------------------------------------------------------*/
/* Function for  Readable date time
/*--------------------------------------------------------------*/
function count_id(){
  static $count = 1;
  return $count++;
}
/*--------------------------------------------------------------*/
/* Function for Creting random string
/*--------------------------------------------------------------*/
function randString($length = 5)
{
  $str='';
  $cha = "0123456789abcdefghijklmnopqrstuvwxyz";

  for($x=0; $x<$length; $x++)
   $str .= $cha[mt_rand(0,strlen($cha))];
  return $str;
}
/*--------------------------------------------------------------*/
/* unit of measurement
/*--------------------------------------------------------------*/
function find_all_products() {
  global $db;
  $sql  = "SELECT id, name, quantity, price, unit FROM products";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function to fetch all products with unit of measurement
/*--------------------------------------------------------------*/
$sql = "SELECT id, name, quantity, price, 
        COALESCE(unit, 'pcs') AS unit  
        FROM products";



?>

