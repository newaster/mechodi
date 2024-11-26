<?php

require_once('load.php');

/* -------------------------------------------------------------- */
/* Function for find all database table rows by table name
  /*-------------------------------------------------------------- */

function find_all($table) {
    global $db;
    if (tableExists($table)) {
        return find_by_sql("SELECT * FROM " . $db->escape($table). " order by 2 ASC");
    }
}


function print_errors() {
    global $db;
    return $db->p_errors();
}

/* -------------------------------------------------------------- */
/* Function for Perform queries
  /*-------------------------------------------------------------- */

function find_by_sql($sql) {
    global $db;
    $result = $db->query($sql);
    $result_set = $db->while_loop($result);
    return $result_set;
}

/* -------------------------------------------------------------- */
/*  Function for Find data from table by id
  /*-------------------------------------------------------------- */

function find_by_id($table, $id) {
    global $db;
    $id = (int) $id;
    if (tableExists($table)) {
        $sql = $db->query("SELECT * FROM {$db->escape($table)} WHERE id='{$db->escape($id)}' LIMIT 1");
        if ($result = $db->fetch_assoc($sql))
            return $result;
        else
            return null;
    }
}

/* -------------------------------------------------------------- */
/* Function for Delete data from table by id
  /*-------------------------------------------------------------- */

function delete_by_id($table, $id) {
    global $db;
    if (tableExists($table)) {
        
        // $selectData = "Select * from ".$db->escape($table)."  where id = ".$id;
        // $users = find_by_sql($selectData);        
        // $insert = "Insert into deleted ( tablename, data ) values ( '".$db->escape($table)."' , '".  json_encode($users)."' ) ";
        // $db->query($insert);
        
        $sql = "DELETE FROM " . $db->escape($table);
        $sql .= " WHERE id=" . $db->escape($id);
        $sql .= " LIMIT 1";
        $db->query($sql);
        
        return ($db->affected_rows() === 1) ? true : false;
    }
}

/* -------------------------------------------------------------- */
/* Function for Count id  By table name
  /*-------------------------------------------------------------- */

function count_by_id($table) {
    global $db;
    if (tableExists($table)) {
        $sql = "SELECT COUNT(id) AS total FROM " . $db->escape($table);
        $result = $db->query($sql);
        return($db->fetch_assoc($result));
    }
}

/* -------------------------------------------------------------- */
/* Determine if database table exists
  /*-------------------------------------------------------------- */

function tableExists($table) {
    global $db;
    $table_exit = $db->query('SHOW TABLES FROM ' . DB_NAME . ' LIKE "' . $db->escape($table) . '"');
    if ($table_exit) {
        if ($db->num_rows($table_exit) > 0)
            return true;
        else
            return false;
    }
}

/* -------------------------------------------------------------- */
/* Login with the data provided in $_POST,
  /* coming from the login form.
  /*-------------------------------------------------------------- */

function authenticate($username = '', $password = '') {
    global $db;
    $username = $db->escape($username);
    $password = $db->escape($password);
    $sql = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
    $result = $db->query($sql);
    if ($db->num_rows($result)) {
        $user = $db->fetch_assoc($result);
        $password_request = sha1($password);
        if ($password_request === $user['password']) {
            
            return $user['id'];
        }
    }
    return false;
}

/* -------------------------------------------------------------- */
/* Login with the data provided in $_POST,
  /* coming from the login_v2.php form.
  /* If you used this method then remove authenticate function.
  /*-------------------------------------------------------------- */

function authenticate_v2($username = '', $password = '') {
    global $db;
    $username = $db->escape($username);
    $password = $db->escape($password);
    $sql = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' and status = '1' LIMIT 1", $username);
    $result = $db->query($sql);
    if ($db->num_rows($result)) {
        $user = $db->fetch_assoc($result);
        $password_request = sha1($password);
        if ($password_request === $user['password']) {
            return $user;
        }
    }
    return false;
}

/* -------------------------------------------------------------- */
/* Find current log in user by session id
  /*-------------------------------------------------------------- */

function current_user() {
    static $current_user;
    global $db;
    if (!$current_user) {
        if (isset($_SESSION['user_id'])):
            $user_id = intval($_SESSION['user_id']);
            
            $current_user = find_by_id('users', $user_id);
            $level = $current_user['user_level'] ;
            if( $level == 4 ){
                $selectUser = "Select * from garage inner join ";
            }
        endif;
    }
    return $current_user;
}

/* -------------------------------------------------------------- */
/* Find all user by
  /* Joining users table and user gropus table
  /*-------------------------------------------------------------- */

function find_all_user() {
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

/* -------------------------------------------------------------- */
/* Find all media 
  /* Not in maintenance and expenses
  /*-------------------------------------------------------------- */

function find_all_media() {
    global $db;
    $results = array();
    $sql = "SELECT  m.* ";
    $sql .="FROM media m ";
    $sql .="where m.id NOT IN  ( SELECT ma.bill_image as bill_image from maintenance ma   ) and m.id NOT IN  ( SELECT e.bill_image as bill_image from expenses e   ) ";
    $result = find_by_sql($sql);
    return $result;
}

/* -------------------------------------------------------------- */
/* Find all user by Level
  /* Joining users table and user gropus table
  /*-------------------------------------------------------------- */

function find_all_userbylevel($id) {
    global $db;
    $results = array();
    $sql = "SELECT u.id as id,u.name as name ,u.username as username,u.user_level,u.status,u.last_login,";
    $sql .="g.group_name ";
    $sql .="FROM users u ";
    $sql .="LEFT JOIN user_groups g ";

    $sql .="ON g.group_level=u.user_level ";
    $sql .= "Where u.user_level = " . $id . " ";
    $sql .="ORDER BY u.name ASC";
    $result = find_by_sql($sql);
    return $result;
}

/* -------------------------------------------------------------- */
/* Function to update the last log in of a user
  /*-------------------------------------------------------------- */

function updateLastLogIn($user_id) {
    global $db;
    $date = make_date();
    $sql = "UPDATE users SET last_login='{$date}' WHERE id ='{$user_id}' LIMIT 1";
    $result = $db->query($sql);
    return ($result && $db->affected_rows() === 1 ? true : false);
}

/* -------------------------------------------------------------- */
/* Find all Group name
  /*-------------------------------------------------------------- */

function find_by_groupName($val) {
    global $db;
    $sql = "SELECT group_name FROM user_groups WHERE group_name = '{$db->escape($val)}' LIMIT 1 ";
    $result = $db->query($sql);
    return($db->num_rows($result) === 0 ? true : false);
}

/* -------------------------------------------------------------- */
/* Find group level
  /*-------------------------------------------------------------- */

function find_by_groupLevel($level) {
    global $db;
    $sql = "SELECT group_level FROM user_groups WHERE group_level = '{$db->escape($level)}' LIMIT 1 ";
    $result = $db->query($sql);
    return($db->num_rows($result) === 0 ? true : false);
}



/* -------------------------------------------------------------- */
/* Function for cheaking which user level has access to page
  /*-------------------------------------------------------------- */

function page_require_level($require_level) {
    global $session;
    $current_user = current_user();
    $login_level = find_by_groupLevel($current_user['user_level']);
    //if user not login
    if (!$session->isUserLoggedIn(true)):
        $session->msg('d', 'Please login...');
        redirect('index.php', false);
    //if Group status Deactive
    elseif ($login_level['group_status'] === '0'):
        $session->msg('d', 'This level user has been band!');
        redirect('home.php', false);
    //cheackin log in User level and Require level is Less than or equal to
    elseif ($current_user['user_level'] <= (int) $require_level):
        return true;
    else:
        $session->msg("d", "Sorry! you dont have permission to view the page.");
        redirect('home.php', false);
    endif;
}

/* -------------------------------------------------------------- */
/* Function for Finding all product name
  /* JOIN with categorie  and media database table
  /*-------------------------------------------------------------- */

function join_product_table() {
    global $db;
    $sql = " SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.media_id,p.date,c.name";
    $sql .=" AS categorie,m.file_name AS image";
    $sql .=" FROM products p";
    $sql .=" LEFT JOIN categories c ON c.id = p.categorie_id";
    $sql .=" LEFT JOIN media m ON m.id = p.media_id";
    $sql .=" ORDER BY p.id ASC";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for Finding all product name
  /* Request coming from ajax.php for auto suggest
  /*-------------------------------------------------------------- */

function find_product_by_title($product_name) {
    global $db;
    $p_name = remove_junk($db->escape($product_name));
    $sql = "SELECT name FROM products WHERE name like '%$p_name%' LIMIT 5";
    $result = find_by_sql($sql);
    return $result;
}

/* -------------------------------------------------------------- */
/* Function for Finding all product info by product title
  /* Request coming from ajax.php
  /*-------------------------------------------------------------- */

function find_all_product_info_by_title($title) {
    global $db;
    $sql = "SELECT * FROM products ";
    $sql .= " WHERE name ='{$title}'";
    $sql .=" LIMIT 1";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for Update product quantity
  /*-------------------------------------------------------------- */

function update_product_qty($balance_qty, $p_id) {
    global $db;
    $balance_qty = (int) $balance_qty;
    $id = (int) $p_id;
    $sql = "UPDATE products SET quantity=quantity -'{$balance_qty}' WHERE id = '{$id}'";
    $result = $db->query($sql);
    return($db->affected_rows() === 1 ? true : false);
}

/* -------------------------------------------------------------- */
/* Function for Display Recent product Added
  /*-------------------------------------------------------------- */

function find_recent_product_added($limit) {
    global $db;
    $sql = " SELECT p.id,p.name,p.sale_price,p.media_id,c.name AS categorie,";
    $sql .= "m.file_name AS image FROM products p";
    $sql .= " LEFT JOIN categories c ON c.id = p.categorie_id";
    $sql .= " LEFT JOIN media m ON m.id = p.media_id";
    $sql .= " ORDER BY p.id DESC LIMIT " . $db->escape((int) $limit);
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for Find Highest saleing Product
  /*-------------------------------------------------------------- */

function find_higest_saleing_product($limit) {
    global $db;
    $sql = "SELECT p.name, COUNT(s.product_id) AS totalSold, SUM(s.balance_qty) AS totalQty";
    $sql .= " FROM sales s";
    $sql .= " LEFT JOIN products p ON p.id = s.product_id ";
    $sql .= " GROUP BY s.product_id";
    $sql .= " ORDER BY SUM(s.balance_qty) DESC LIMIT " . $db->escape((int) $limit);
    return $db->query($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all sales
  /*-------------------------------------------------------------- */

function find_all_sale() {
    global $db;
    $sql = "SELECT s.id,s.balance_qty,s.empty_qty, s.delivered_qty,s.received_qty,s.price, s.total_price ,s.date,p.name, u.name as uname, s.type , v.vehicle_no , r.routename as  routeinfo ";
    $sql .= " FROM sales s";
    $sql .= " LEFT JOIN products p ON s.product_id = p.id";
    $sql .= " LEFT JOIN users u ON s.user_id = u.id";
    $sql .= " LEFT JOIN vehicle v ON v.id = s.vehicle";
    $sql .= " LEFT JOIN route_info r ON r.id = s.routeinfo";
    $sql .= " ORDER BY s.date DESC";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all Expenses
  /*-------------------------------------------------------------- */

function find_all_expenses() {
    global $db;
    $sql = "SELECT s.id,s.amount,s.datetime, GROUP_CONCAT(u.name) as name , e.name source,m.file_name as filename";
    $sql .= " FROM expenses s";
    $sql .= " LEFT JOIN users u ON s.user_id = u.id or s.admin_id = u.id ";
    $sql .= " LEFT JOIN source e ON e.id = s.source ";
    $sql .= " LEFT JOIN media m ON m.id = s.bill_image ";

    $sql .= "GROUP BY s.id ORDER BY s.datetime DESC ";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all Expenses
  /*-------------------------------------------------------------- */

function find_all_reports() {
    global $db;
    $sql = "SELECT dr.id, dr.coupons_qty,dr.vehicle_no,dr.inspection_count,dr.claims_count,dr.road_show_count,dr.record_date,dr.created_date,dr.`status`,dr.updated_by,u.name as uname";
    $sql .= " FROM daily_report AS dr ";
    $sql .= " LEFT JOIN users u ON dr.updated_by = u.id ";
    $sql .= "  ORDER BY dr.created_date DESC ";
    return find_by_sql($sql);
}

function find_reports_by_dates($start_date, $end_date) {
    global $db;
    $start_date = date("Y-m-d", strtotime($start_date));
    $end_date = date("Y-m-d", strtotime($end_date));
    $sql = "SELECT dr.id, dr.coupons_qty,dr.vehicle_no,dr.inspection_count,dr.claims_count,dr.road_show_count,dr.record_date,dr.created_date,dr.`status`,dr.updated_by,u.name as uname";
    $sql .= " FROM daily_report AS dr ";
    $sql .= " LEFT JOIN users u ON dr.updated_by = u.id ";

    $sql .= " WHERE DATE(dr.record_date) BETWEEN '{$start_date}' AND '{$end_date}'";
//    $sql .= " GROUP BY DATE(s.date),p.name";
    $sql .= "  ORDER BY dr.created_date DESC ";

    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all Expenses
  /*-------------------------------------------------------------- */

function find_all_events() {
    global $db;
    
     $sql = "SELECT s.id,s.name,s.description, s.status, s.created_date, s.worktype , u.name as uname , e.name as worktypename ";
    $sql .= " FROM events s";
    $sql .= " LEFT JOIN users u ON s.updated_by = u.id ";
     $sql .= " LEFT JOIN source e ON s.worktype = e.id ";
    $sql .= "  ORDER BY s.created_date DESC ";
    
    
    return find_by_sql($sql);
}


/* -------------------------------------------------------------- */
/* Function for find all Expenses
  /*-------------------------------------------------------------- */

function find_all_events_by_id($id) {
    global $db;
    
     $sql = "SELECT s.id,s.name,s.description, s.status, s.created_date, s.worktype , u.name as uname , e.name as worktypename ";
    $sql .= " FROM events s";
    $sql .= " LEFT JOIN users u ON s.updated_by = u.id ";
     $sql .= " LEFT JOIN source e ON s.worktype = e.id ";
     $sql .= " WHERE u.id = $id ";
    $sql .= "  ORDER BY s.created_date DESC ";
    
    
    return find_by_sql($sql);
}

//--------------------------------------------//

function find_all_estimate_by_user($user) {
    global $db;
    
     $sql = "SELECT s.id,s.service, s.description, s.labour_denting, s.labour_rr, s.price, s.amount,sum(s.labour_paint) as tax, s.discount, s.subtotal, s.excise, s.vat, s.invoice,s.gst, s.excise_price, s.vat_price, s.gst_price, sum( s.total ) as total,  s.created_date, s.lead_id, s.claim_number, s.package_name, u.name as username ";
    $sql .= " FROM estimate s";
    $sql .= " LEFT JOIN users u ON s.created_by = u.id ";
    $sql .= " WHERE s.created_by = $user ";
    $sql .= "   ORDER BY s.created_date DESC ";
    
    return find_by_sql($sql);
}

function find_all_estimates() {
    global $db;
    
     $sql = "SELECT s.id,s.service, s.description, s.labour_denting, s.labour_rr, s.labour_paint, s.amount, s.discount,  ROUND( s.subtotal - s.taxamount ) as subtotal, s.excise, s.vat, s.invoice,s.gst, s.excise_price, s.vat_price, s.gst_price,  s.total  as total,  s.created_date, s.lead_id, s.claim_number, s.package_name ";
     $sql .= " FROM estimate s";
    // $sql .= " LEFT JOIN users u ON s.created_by = u.id ";
    $sql .= " ORDER BY s.created_date DESC ";
    //$sql .= " GROUP BY s.claim_number  ORDER BY s.created_date DESC ";
    
    return find_by_sql($sql);
}

function find_all_estimates_by_package($packagenum) {
    global $db;
    
    $sql = "SELECT s.id,s.service, s.description, s.labour_denting, s.labour_rr, s.price, s.amount, s.labour_paint as labour_paint,  s.taxamount  as taxamount , s.discount, ( s.subtotal - s.taxamount) as subtotal, s.excise, s.vat, s.invoice,s.gst, s.excise_price, s.vat_price, s.gst_price,  s.total  as total,  s.created_date, s.lead_id, s.claim_number, s.package_name, u.name as username ";
    $sql .= " FROM estimate s";
    $sql .= " LEFT JOIN users u ON s.created_by = u.id ";
    $sql .= " WHERE s.claim_number = '{$packagenum}' ";
    $sql .= "   ORDER BY s.created_date DESC ";
    
    return find_by_sql($sql);
}


function find_all_estimate_by_id($id) {
    global $db;
    
     $sql = "SELECT s.id,s.service, s.description, s.quantity, s.rate_unit, s.price, s.amount,sum(s.tax) as tax, s.discount, s.subtotal, s.excise, s.vat, s.invoice,s.gst, s.excise_price, s.vat_price, s.gst_price, sum( s.total ) as total, s.created_date, s.lead_id, s.package_number, s.package_name, u.name as username ";
    $sql .= " FROM estimate s ";
    $sql .= " s.package_number = '$id' ";
    $sql .= " LEFT JOIN users u ON s.created_by = u.id ";
    $sql .= " ORDER BY s.created_date DESC ";
    
    
    return find_by_sql($sql);
}



//-------------------------------------------------//

function find_all_invoices_by_user($user) {
    global $db;
    
     $sql = "SELECT s.id,s.service, s.description, s.labour_denting, s.labour_rr, s.price, s.amount,sum(s.labour_paint) as tax, s.discount, s.subtotal, s.excise, s.vat, s.invoice,s.gst, s.excise_price, s.vat_price, s.gst_price, sum( s.total ) as total,  s.created_date, s.lead_id, s.claim_number, s.package_name, u.name as username ";
    $sql .= " FROM invoice s";
    $sql .= " LEFT JOIN users u ON s.created_by = u.id ";
    $sql .= " WHERE s.created_by = $user ";
    $sql .= "   ORDER BY s.created_date DESC ";
    
    return find_by_sql($sql);
}

function find_all_invoices() {
    global $db;
    
     $sql = "SELECT s.id,s.service, s.description, s.labour_denting, s.labour_rr, s.labour_paint, s.amount, s.discount,  ROUND( s.subtotal - s.taxamount ) as subtotal, s.excise, s.vat, s.invoice,s.gst, s.excise_price, s.vat_price, s.gst_price,  s.total  as total,  s.created_date, s.lead_id, s.claim_number, s.package_name ";
     $sql .= " FROM invoice s";
    // $sql .= " LEFT JOIN users u ON s.created_by = u.id ";
    $sql .= " ORDER BY s.created_date DESC ";
    //$sql .= " GROUP BY s.claim_number  ORDER BY s.created_date DESC ";
    
    return find_by_sql($sql);
}

function find_all_invoices_by_package($packagenum) {
    global $db;
    
    $sql = "SELECT s.id,s.service, s.description, s.labour_denting, s.labour_rr, s.price, s.amount, s.labour_paint as labour_paint,  s.taxamount  as taxamount , s.discount, ( s.subtotal - s.taxamount) as subtotal, s.excise, s.vat, s.invoice,s.gst, s.excise_price, s.vat_price, s.gst_price,  s.total  as total,  s.created_date, s.lead_id, s.claim_number, s.package_name, u.name as username ";
    $sql .= " FROM invoice s";
    $sql .= " LEFT JOIN users u ON s.created_by = u.id ";
    $sql .= " WHERE s.claim_number = '{$packagenum}' ";
    $sql .= "   ORDER BY s.created_date DESC ";
    
    return find_by_sql($sql);
}


function find_all_invoices_by_id($id) {
    global $db;
    
     $sql = "SELECT s.id,s.service, s.description, s.quantity, s.rate_unit, s.price, s.amount,sum(s.tax) as tax, s.discount, s.subtotal, s.excise, s.vat, s.invoice,s.gst, s.excise_price, s.vat_price, s.gst_price, sum( s.total ) as total, s.created_date, s.lead_id, s.package_number, s.package_name, u.name as username ";
    $sql .= " FROM invoice s ";
    $sql .= " s.package_number = '$id' ";
    $sql .= " LEFT JOIN users u ON s.created_by = u.id ";
    $sql .= " ORDER BY s.created_date DESC ";
    
    
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all Expenses
  /*-------------------------------------------------------------- */

function find_all_leads() {
    global $db;
    $sql = "SELECT
			s.id AS id,
			s.caller_name AS cname,
			s.caller_number AS cnum,
                        s.caller_address AS cadd,
			s.eqry AS eqry,
                        s.caller_email as cemail ,
                        s.vehicle_number as vnum,
			s.source as source,                        
                        s.assigned_name AS aname,
                        s.created_date as cdate,
                        s.status
		FROM
			`leads` AS s
		ORDER BY
			s.created_date DESC ";
    return find_by_sql($sql);
}


function find_all_leads_by_garage($garage) {
    global $db;
    $sql = "SELECT
			s.id AS id,
			s.caller_name AS cname,
			s.caller_number AS cnum,
                        s.caller_address AS cadd,
			s.eqry AS eqry,
                        s.caller_email as cemail ,
                        s.vehicle_number as vnum,
			s.source as source,                        
                        s.assigned_name AS aname,
                        s.created_date as cdate,
                        s.status
		FROM
			`leads` AS s ";
                $sql .= " INNER JOIN garage as g on s.garage = g.id " ;
                $sql .= " where " ;
                $sql .= " g.email = '".$garage."'" ;
		$sql .= "ORDER BY
			s.created_date DESC ";
    return find_by_sql($sql);
}


function find_all_leads_by_insurance($insurance) {
    global $db;
    $sql = "SELECT
			s.id AS id,
			s.caller_name AS cname,
			s.caller_number AS cnum,
                        s.caller_address AS cadd,
			s.eqry AS eqry,
                        s.caller_email as cemail ,
                        s.vehicle_number as vnum,
			s.source as source,                        
                        s.assigned_name AS aname,
                        s.created_date as cdate,
                        s.status
		FROM
			`leads` AS s ";
                $sql .= " INNER JOIN insurance_company as g on s.insurance = g.id " ;
                $sql .= " where " ;
                $sql .= " g.email = '".$insurance."' " ;
		$sql .= "ORDER BY
			s.created_date DESC ";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all Expenses
  /*-------------------------------------------------------------- */

function find_all_leads_by_id($id) {
    global $db;
    $sql = "SELECT
			s.id as id,
			s.caller_name,
			s.caller_number,
                        s.caller_address,
                        s.pincode,
			s.eqry as eqry,
                        s.caller_email,  
			s.pickup_time as pickup_time,
                        s.pickup_date as pickup_date, 
                        s.mfg_year as mfg_year, 
			s.source as source,
                        s.vehicle_number,
                        s.car_model,
                        s.car_make,
			s.`status` as status,
			s.created_date as cdate,
			s.updated_date as udate,
			s.updated_by as uid,
			s.updator_name as uname,
                        s.assigned_to ,
                        s.assigned_name as aname,
                        s.city,
                        s.area,
                        p.file_name,
                        p.file_type,
                        s.policy_attach
		FROM
			`leads` AS s
		left join policy p on p.id = s.policy_attach
                  WHERE s.id = $id
		
		ORDER BY
			s.created_date DESC
	";
   
    return find_by_sql($sql);
}



/* -------------------------------------------------------------- */
/* Function for find all garage by id
  /*-------------------------------------------------------------- */

function find_all_garage_by_id($id) {
    global $db;
    
    $sql = " Select * from garage where id = '{$id}'";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all garage by username
  /*-------------------------------------------------------------- */

function find_all_garage_by_username($username) {
    global $db;
    
    $sql = " Select * from garage where email = '{$username}' Limit 1";
    return find_by_sql($sql);
}


/* -------------------------------------------------------------- */
/* Function for find all garage by username 
  /*-------------------------------------------------------------- */

function find_all_insurance_by_username($username) {
    global $db;
    
    $sql = " Select * from insurance_company where email = '{$username}' Limit 1";
    return find_by_sql($sql);
 }


/* -------------------------------------------------------------- */
/* Function for find all garage by id
  /*-------------------------------------------------------------- */

function find_all_advisor_by_id($id) {
    global $db;
    $sql = " Select * from advisor where id = '{$id}'";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all area by id
  /*-------------------------------------------------------------- */
  
function find_all_areas_by_id($id) {
    global $db;
    $sql = " Select * from area where city_id = '{$id}' order by area ASC";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all garages by id
  /*-------------------------------------------------------------- */
  
function find_all_garages_by_city($id) {
    global $db;
    $sql = " Select * from garage where city = '{$id}' order by garage_name";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all garage 
  /*-------------------------------------------------------------- */

function find_all_garages() {
    global $db;
    $sql = " Select g.*, c.city as cityname, a.area as areaname from garage as g inner join cities as c on c.id = g.city inner join area as a on a.id = g.area order by g.garage_name ASC";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all advisors 
  /*-------------------------------------------------------------- */

function find_all_advisors() {
    global $db;
    $sql = " Select a.*, c.city as cityname, g.garage_name as garagename from advisor as a inner join garage as g on g.id = a.garage  inner join cities as c on c.id = g.city  order by a.name ASC";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all advisors by garage 
  /*-------------------------------------------------------------- */

function find_all_advisors_by_garage($garage) {
    global $db;
    $sql = " Select a.*, c.city as cityname, g.garage_name as garagename from advisor as a inner join garage as g on g.id = a.garage  inner join cities as c on c.id = g.city where updated_by = '".$garage."'  order by a.name ASC";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all insurance company by id
  /*-------------------------------------------------------------- */

function find_all_insurance_company_by_id($id) {
    global $db;
    $sql = " Select * from insurance_company where id = '{$id}'";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all Expenses
  /*-------------------------------------------------------------- */

function find_all_leads_by_user($id) {
    global $db;
    $sql = "SELECT
			s.id AS id,
			s.caller_name AS cname,
			s.caller_number AS cnum,
                        s.caller_address AS cadd,
			s.enquiry AS eqry,
                        s.caller_email as cemail ,
			s.order_package as orderpck,
                          s.type as type,
			s.source as source,
                        s.vehicle_number,
			s.`status` as status,
			s.created_date as cdate,
			s.updated_date as udate,
			s.updated_by AS uid,
			s.updator_name AS uname,
                        s.assigned_to AS aid,
                        s.assigned_name AS aname
			
		FROM
			`leads` AS s
		
                  WHERE 
                  s.assigned_to = $id
		
		ORDER BY
			s.created_date DESC";
    return find_by_sql($sql);
}


/* -------------------------------------------------------------- */
/* Function for find all Expenses
  /*-------------------------------------------------------------- */

function find_all_source_type() {
    global $db;
    $sql = "SELECT s.id,s.name,s.description, s.status, s.created_date ";
    $sql .= " FROM source s";
    $sql .= "  ORDER BY s.created_date DESC ";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all Vehicle
  /*-------------------------------------------------------------- */

function find_all_vehicles() {
    global $db;
    $sql = "SELECT s.id,s.vehicle_name,s.vehicle_no, u.name as name,s.datetime, s.fitness_date , s.permit , s.tax, i.name as insuarancename , i.cost as inscost ";
    $sql .= " FROM vehicle s";
    $sql .= " LEFT JOIN users u ON s.user_id = u.id ";
    $sql .= " LEFT JOIN insuarance i ON i.vehicle_id = s.id ";
    $sql .= " GROUP BY s.id ORDER BY s.datetime DESC ";
    return find_by_sql($sql);
}


/* -------------------------------------------------------------- */
/* Function for find all Vehicle
  /*-------------------------------------------------------------- */

function find_all_messages() {
    global $db;
    $sql = "SELECT * ";
    $sql .= " FROM messages s";
    $sql .= "  ORDER BY s.created_date DESC ";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all Vehicle
  /*-------------------------------------------------------------- */

function find_all_jobcards() {
    global $db;
    $sql = "SELECT s.*, u.name as uname ";
    $sql .= " FROM open_job_card  as s " ;
    $sql .= " INNER JOIN users as u on s.userid = u.id " ;
    $sql .= "  ORDER BY  s.id DESC , s.created_date DESC ";
    return find_by_sql($sql);
}

function find_all_closed_jobcards() {
    global $db;
    $sql = "SELECT s.*, u.name as uname,g.garage_name as gname, l.url, e.invoice as estimate ";
    $sql .= " FROM open_job_card  as s " ;
    $sql .= " INNER JOIN users as u on s.userid = u.id " ;
    $sql .= " INNER JOIN leads as l on l.id = s.leadid " ;
    $sql .= " INNER JOIN garage as g on l.garage = g.id " ;
     $sql .= " LEFT JOIN estimate as e on e.lead_id = s.leadid ";
    $sql .= " where s.status = 'close' " ;
    $sql .= "  ORDER BY  s.id DESC , s.created_date DESC ";
    return find_by_sql($sql);
}



function find_all_open_jobcards_by_garage($garage) {
    global $db;
    $sql = "SELECT s.*, g.garage_name as uname, l.url ";
    $sql .= " FROM open_job_card  as s " ;    
    $sql .= " INNER JOIN leads as l on l.id = s.leadid " ;
    $sql .= " INNER JOIN garage as g on l.garage = g.id " ;
    $sql .= " where s.status = 'open'  and " ;
    $sql .= " g.email = '".$garage."'" ;
    $sql .= "  ORDER BY  s.id DESC , s.created_date DESC "; 
    return find_by_sql($sql);
    
}


function find_all_open_jobcards_by_insurance($insurance) {
    global $db;
    $sql = "SELECT s.*, g.name as uname, l.url ";
    $sql .= " FROM open_job_card  as s " ;    
    $sql .= " INNER JOIN leads as l on l.id = s.leadid " ;
    $sql .= " INNER JOIN insurance_company as g on l.insurance = g.id " ;
    $sql .= " where s.status = 'open'  and " ;
    $sql .= " g.email = '".$insurance."'" ;
    $sql .= "  ORDER BY  s.id DESC , s.created_date DESC "; 
    return find_by_sql($sql);
    
}

function find_all_closed_jobcards_by_garage($garage) {
    global $db;
    $sql = "SELECT s.*, g.garage_name as uname, l.url,e.invoice as estimate ";
    $sql .= " FROM open_job_card  as s " ;    
    $sql .= " INNER JOIN leads as l on l.id = s.leadid " ;
    $sql .= " INNER JOIN garage as g on l.garage = g.id " ;
    $sql .= " LEFT JOIN estimate as e on e.lead_id = s.leadid ";
    $sql .= " where s.status = 'close'  and " ;
    $sql .= " g.email = '".$garage."'" ;
    $sql .= " GROUP BY s.id ORDER BY  s.id DESC , s.created_date DESC "; 
    return find_by_sql($sql);
    
} 


function find_all_close_jobcards_by_insurance($insurance) {
    global $db;
    $sql = "SELECT s.*, g.name as uname, l.url ";
    $sql .= " FROM open_job_card  as s " ;    
    $sql .= " INNER JOIN leads as l on l.id = s.leadid " ;
    $sql .= " INNER JOIN insurance_company as g on l.insurance = g.id " ;
    $sql .= " where s.status = 'close'  and " ;
    $sql .= " g.email = '".$insurance."'" ;
    $sql .= "  ORDER BY  s.id DESC , s.created_date DESC "; 
    return find_by_sql($sql);
    
}

function find_all_open_jobcards() {
    global $db;
    $sql = "SELECT s.*, u.name as uname,g.garage_name as gname ";
    $sql .= " FROM open_job_card  as s " ;
    $sql .= " INNER JOIN users as u on s.userid = u.id " ;
    $sql .= " INNER JOIN leads as l on l.id = s.leadid " ;
    $sql .= " INNER JOIN garage as g on l.garage = g.id " ;
    $sql .= " where s.status = 'open' " ;
    $sql .= "  ORDER BY  s.id DESC , s.created_date DESC ";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all Vehicle
  /*-------------------------------------------------------------- */

function find_all_jobcards_by_user($user) {
    global $db;
    $sql = "SELECT * ";
    $sql .= " FROM jobcard s";
    $sql .= " WHERE s.updated_by = $user ";
    $sql .= "  ORDER BY s.created_date DESC ";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all Vehicle
  /*-------------------------------------------------------------- */

function find_all_insurance() {
    global $db;
    $sql = "SELECT s.id,s.receipt,s.name,u.vehicle_no, s.purchase_date as pdate, s.expiry_date, s.cost ";
    $sql .= " FROM insuarance s";
    $sql .= " LEFT JOIN vehicle u ON s.vehicle_id = u.id ";
    $sql .= " GROUP BY s.id ORDER BY s.expiry_date Asc ";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all Vehicle
  /*-------------------------------------------------------------- */

function find_all_maintenance() {
    global $db;
    $sql = "SELECT s.id,s.type,us.name as person_name,u.vehicle_no, s.created_date as cdate, s.cost ,m.file_name as filename, s.garage_name, s.address, s.km_reading, p.receipt , p.expiry_date";
    $sql .= " FROM maintenance s";
    $sql .= " LEFT JOIN vehicle u ON s.vehicle_id = u.id ";
    $sql .= " LEFT JOIN users us ON s.person_name = us.id ";
    $sql .= " LEFT JOIN media m ON m.id = s.bill_image ";
    $sql .= " LEFT JOIN pucs p ON p.vehicle_id = s.vehicle_id ";
    $sql .= " GROUP BY s.id ORDER BY s.created_date DESC ";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for find all Vehicle
  /*-------------------------------------------------------------- */

function find_all_puc() {
    global $db;
    $sql = "SELECT s.id,s.receipt,u.vehicle_no, s.purchase_date as pdate, s.expiry_date ";
    $sql .= " FROM pucs s";
    $sql .= " LEFT JOIN vehicle u ON s.vehicle_id = u.id ";
    $sql .= " GROUP BY s.id ORDER BY s.expiry_date Asc ";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for Generate sales report by two dates
  /*-------------------------------------------------------------- */

function find_expenses_by_dates($start_date, $end_date, $userid) {
    global $db;
    $start_date = date("Y-m-d", strtotime($start_date));
    $end_date = date("Y-m-d", strtotime($end_date));


    $sql = "SELECT s.id,s.amount,s.datetime, GROUP_CONCAT(u.name) as name,e.name source,m.file_name as filename";
    $sql .= " FROM expenses s";
    $sql .= " LEFT JOIN users u ON s.user_id = u.id or s.admin_id = u.id ";
    $sql .= " LEFT JOIN source e ON e.id = s.source ";
    $sql .= " LEFT JOIN media m ON m.id = s.bill_image ";
    $sql .= " WHERE DATE(s.datetime) BETWEEN '{$start_date}' AND '{$end_date}'";

    if ("" != $userid) {
        $sql .= " and s.user_id = " . $userid;
    }

    $sql .= " GROUP BY s.id ORDER BY s.datetime DESC ";

    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for Generate sales report by two dates
  /*-------------------------------------------------------------- */

function find_vehicle_by_dates($start_date, $end_date, $userid) {
    global $db;

    $start_date = date("Y-m-d", strtotime($start_date));
    $end_date = date("Y-m-d", strtotime($end_date));


    $sql = "SELECT s.id,s.vehicle_name,s.vehicle_no, u.name as name,s.datetime, s.fitness_date , s.permit , s.tax, i.name as insname , i.cost as inscost ";
    $sql .= " FROM vehicle s";
    $sql .= " LEFT JOIN users u ON s.user_id = u.id ";
    $sql .= " LEFT JOIN insuarance i ON i.vehicle_id = s.id ";
    $sql .= " WHERE DATE(s.datetime) BETWEEN '{$start_date}' AND '{$end_date}'";

    if ("" != $userid) {
        $sql .= " and s.vehicle_id = " . $userid;
    }

    $sql .= " GROUP BY s.id ORDER BY s.datetime DESC ";

    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for Generate sales report by two dates
  /*-------------------------------------------------------------- */

function find_maintenance_by_dates($start_date, $end_date, $userid) {
    global $db;

    $start_date = date("Y-m-d", strtotime($start_date));
    $end_date = date("Y-m-d", strtotime($end_date));


    $sql = "SELECT s.id,s.type,us.name as person_name,u.vehicle_no, s.created_date as cdate, s.cost,m.file_name as filename, p.receipt , p.expiry_date ";
    $sql .= " FROM maintenance s";
    $sql .= " LEFT JOIN vehicle u ON s.vehicle_id = u.id ";
    $sql .= " LEFT JOIN users us ON s.person_name = us.id ";
    $sql .= " LEFT JOIN media m ON m.id = s.bill_image ";
    $sql .= " LEFT JOIN pucs p ON p.vehicle_id = s.vehicle_id ";
    $sql .= " WHERE DATE(s.created_date) BETWEEN '{$start_date}' AND '{$end_date}'";

    if ("" != $userid) {
        $sql .= " and s.vehicle_id = " . $userid;
    }

    $sql .= " GROUP BY s.id ORDER BY s.created_date DESC ";

    return find_by_sql($sql);
}

function find_insurance_by_dates($start_date, $end_date, $userid) {
    global $db;
    $start_date = date("Y-m-d", strtotime($start_date));
    $end_date = date("Y-m-d", strtotime($end_date));


    $sql = "SELECT s.id,s.vehicle_name, s.vehicle_no, s.datetime,(u.name) as name";
    $sql .= " FROM vehicle s";
    $sql .= " LEFT JOIN users u ON s.user_id = u.id ";
    $sql .= " WHERE DATE(s.datetime) BETWEEN '{$start_date}' AND '{$end_date}'";

    if ("" != $userid) {
        $sql .= " and s.user_id = " . $userid;
    }

    $sql .= " GROUP BY s.id ORDER BY s.datetime DESC ";

    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for Display Recent sale
  /*-------------------------------------------------------------- */

function find_recent_sale_added($limit) {
    global $db;
    $sql = "SELECT s.id,s.balance_qty,s.price,s.date,p.name";
    $sql .= " FROM sales s";
    $sql .= " LEFT JOIN products p ON s.product_id = p.id";
    $sql .= " ORDER BY s.date DESC LIMIT " . $db->escape((int) $limit);
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for Generate sales report by two dates
  /*-------------------------------------------------------------- */

function find_sale_by_dates($start_date, $end_date) {
    global $db;
    $start_date = date("Y-m-d", strtotime($start_date));
    $end_date = date("Y-m-d", strtotime($end_date));
    $sql = "SELECT s.id,s.balance_qty,s.empty_qty, s.delivered_qty, s.received_qty, s.price, s.total_price ,s.date,p.name, u.name as uname, s.type, (s.price * s.delivered_qty) AS total_saleing_price, (s.price * s.delivered_qty) AS total_buying_price, v.vehicle_no , r.routename as routeinfo ";
    $sql .= " FROM sales s";
    $sql .= " LEFT JOIN products p ON s.product_id = p.id";
    $sql .= " LEFT JOIN users u ON s.user_id = u.id";
    $sql .= " LEFT JOIN vehicle v ON v.id = s.vehicle";
    $sql .= " LEFT JOIN route_info r ON r.id = s.routeinfo";
    $sql .= " WHERE DATE(s.date) BETWEEN '{$start_date}' AND '{$end_date}'";
//    $sql .= " GROUP BY DATE(s.date),p.name";
    $sql .= " ORDER BY DATE(s.date) DESC";

    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for Generate Daily sales report
  /*-------------------------------------------------------------- */

function dailySales($year, $month) {
    global $db;
    $sql = "SELECT s.id,s.balance_qty,s.price,s.empty_qty,s.delivered_qty,s.received_qty, s.total_price ,s.date,p.name, u.name as uname, s.type, v.vehicle_no, s.routeinfo ";
    $sql .= " FROM sales s";
    $sql .= " LEFT JOIN products p ON s.product_id = p.id";
    $sql .= " LEFT JOIN users u ON s.user_id = u.id";
    $sql .= " LEFT JOIN vehicle v ON v.id = s.vehicle";
    $sql .= " WHERE DATE_FORMAT(s.date, '%Y-%m' ) = '{$year}-{$month}'";
    $sql .= " ORDER BY s.date DESC";

//  $sql  = "SELECT s.balance_qty,";
//  $sql .= " DATE_FORMAT(s.date, '%Y-%m-%e') AS date,p.name,";
//  $sql .= "SUM(p.sale_price * s.balance_qty) AS total_saleing_price";
//  $sql .= " FROM sales s";
//  $sql .= " LEFT JOIN products p ON s.product_id = p.id";
//  $sql .= " WHERE DATE_FORMAT(s.date, '%Y-%m' ) = '{$year}-{$month}'";
//  $sql .= " GROUP BY DATE_FORMAT( s.date,  '%e' ),s.product_id";
    return find_by_sql($sql);
}

/* -------------------------------------------------------------- */
/* Function for Generate Monthly sales report
  /*-------------------------------------------------------------- */

function monthlySales($year) {
    global $db;
//  $sql  = "SELECT s.balance_qty,";
//  $sql .= " DATE_FORMAT(s.date, '%Y-%m-%e') AS date,p.name,";
//  $sql .= "SUM(p.sale_price * s.balance_qty) AS total_saleing_price";
//  $sql .= " FROM sales s";
//  $sql .= " LEFT JOIN products p ON s.product_id = p.id";
//  $sql .= " WHERE DATE_FORMAT(s.date, '%Y' ) = '{$year}'";
//  $sql .= " GROUP BY DATE_FORMAT( s.date,  '%c' ),s.product_id";
//  $sql .= " ORDER BY date_format(s.date, '%c' ) ASC";

    $sql = "SELECT s.id,s.balance_qty,s.empty_qty,s.delivered_qty,s.price, s.total_price ,s.date,p.name, u.name as uname, s.type, v.vehicle_no , r.routename as routeinfo ";
    $sql .= " FROM sales s";
    $sql .= " LEFT JOIN products p ON s.product_id = p.id";
    $sql .= " LEFT JOIN users u ON s.user_id = u.id";
    $sql .= " LEFT JOIN vehicle v ON v.id = s.vehicle";
    $sql .= " LEFT JOIN route_info r ON r.id = s.routeinfo";
    $sql .= " WHERE DATE_FORMAT(s.date, '%Y' ) = '{$year}'";
    $sql .= " ORDER BY s.date DESC";

    return find_by_sql($sql);
}

?>
