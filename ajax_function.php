<?php
ob_start();
session_start();
include("admin/inc/config.php");
include("admin/inc/functions.php");
include("admin/inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();



if(isset($_POST['curr-dd'])){
	$_SESSION['setCurrency'] = $_POST['curr-dd'];
}


if(isset($_POST['wishlist_products'])){
	
	if(isset($_SESSION['customer'])) {
		$user_id = $_SESSION['customer']['cust_id'];
		$user_type = 'Customer';
		$get_wishlist= $pdo->prepare("SELECT * FROM tbl_wishlist WHERE product_id=".$_POST['wishlist_products']." and customer_id=".$user_id." and user_type= '".$user_type."'");
		$get_wishlist->execute();
		$get_wishlist = $get_wishlist->fetch(PDO::FETCH_ASSOC);
		
		if(!empty($get_wishlist)){
			// exist
			$upd_wishlist = $pdo->prepare('DELETE FROM tbl_wishlist where id='.$get_wishlist['id']);
			$upd_wishlist->execute();

			$response_arr = array("status"=>"removed");
			echo json_encode($response_arr);
			exit();
			
		}else{
			// insert
			$ins_wishlist = $pdo->prepare('INSERT INTO tbl_wishlist (customer_id,user_type,product_id) VALUES (?,?,?)');
			$ins_wishlist->execute(array($user_id,$user_type,$_POST['wishlist_products']));

			$response_arr = array("status"=>"added");
			
			echo json_encode($response_arr);
			exit();
		}

	}else{
		setcookie("notlogin-alert-danger", "Please login first to use this feature.", time()+ 5,'/');
		$response_arr = array("status"=>"notlogin");
		echo json_encode($response_arr);
		exit();
		
	}
	
}


if(isset($_POST['post_tid']))
{
	$id = $_POST['post_tid'];
	
	$statement = $pdo->prepare("SELECT * FROM tbl_mid_category WHERE tcat_id=?");
	$statement->execute(array($id));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	$str = '<option value="">Select Mid Level Category</option>';						
	foreach ($result as $row) {
		
		$str .='<option value="'.$row['mcat_id'].'">'.$row['mcat_name'].'</option>';
		
	}
	echo $str;
}

if(isset($_POST['tagitems'])){
    $get_items_arr = explode(',',$_POST['tagitems']);
    
    $store_all_tags =[];
    foreach($get_items_arr as $it => $item){
        $statement = $pdo->prepare("SELECT p_tags FROM tbl_product WHERE p_tags LIKE '%".$item."%'");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        $store_all_tags[] = implode(',',array_column($result,'p_tags'));
        
    }
    
    $store_all_tags = implode(',',$store_all_tags);
    $store_all_tags = explode(',',$store_all_tags);
    $store_all_tags = array_unique($store_all_tags);

    $diff_tag = array_diff($store_all_tags,$get_items_arr);
    echo json_encode($diff_tag);
}

if(isset($_POST['sale_time_discount_btn'])){
    
    if(!isset($_SESSION['customer'])) {
        header('location: '.BASE_URL.'logout.php');
        exit;
    }

    $pdo->begintransaction();
    $check_exist_setting = $pdo->prepare('Select * from tbl_customer_discount_setting where customer_id =?');
    $check_exist_setting->execute(array($_SESSION['customer']['cust_id']));
    $setting_first_row = $check_exist_setting->fetch(PDO::FETCH_ASSOC);
    
    
    if(empty($setting_first_row)){
        // insert

        $insert_sale_setting = $pdo->prepare('INSERT into tbl_customer_discount_setting (customer_id,discount_rate_sale_period) VALUES (?,?)');
        $insert_sale_setting->execute(array(
            $_SESSION['customer']['cust_id'],
            $_POST['sale_time_discount_percentage']
        ));

        if($pdo->lastInsertId()){
            $discount_success_mesg = 'Sale Setting Set successfully';
            setcookie('sale_setting_success',$discount_success_mesg, time() + 5 );
            $pdo->commit();
            header('location:my-sales.php#discounts');
            exit();

        }else{
            $discount_error_mesg = 'Some Error Occurred. Try Again. ';
            setcookie('sale_setting_error',$discount_error_mesg, time() + 5 );
            
            $pdo->rollback();
            header('location:my-sales.php#discounts');
            exit();
        }

    }else{
        // update

        $update_sale_setting = $pdo->prepare('UPDATE  tbl_customer_discount_setting SET discount_rate_sale_period =? Where cds_id=?');
        $update_sale_setting->execute(array(
            $_POST['sale_time_discount_percentage'],
            $setting_first_row['cds_id']
        ));

        if($update_sale_setting){
            $discount_success_mesg = 'Sale Setting Set successfully';
            setcookie('sale_setting_success',$discount_success_mesg, time() + 5 );
            $pdo->commit();
            header('location:my-sales.php#discounts');
            exit();
        }else{
            $discount_success_mesg = 'Nothing changed in sale setting';
            setcookie('sale_setting_success',$discount_success_mesg, time() + 5 );
            $pdo->rollback();
            header('location:my-sales.php#discounts');
            exit();
        }
    }

}


if(isset($_POST['super_sale_discount_btn'])){
	
    if(!isset($_SESSION['customer'])) {
        header('location: '.BASE_URL.'logout.php');
        exit;
    }

    $pdo->begintransaction();
    $check_exist_setting = $pdo->prepare('Select * from tbl_customer_discount_setting where customer_id =?');
    $check_exist_setting->execute(array($_SESSION['customer']['cust_id']));
    $setting_first_row = $check_exist_setting->fetch(PDO::FETCH_ASSOC);
    
  
    if(empty($setting_first_row)){
        // insert

        $insert_sale_setting = $pdo->prepare('INSERT into tbl_customer_discount_setting (customer_id,discount_rate_supersale_period) VALUES (?,?)');
        $insert_sale_setting->execute(array(
            $_SESSION['customer']['cust_id'],
            $_POST['super_sale_discount_percentage']
        ));

        if($pdo->lastInsertId()){
            $discount_success_mesg = 'Super Sale Setting Set successfully';
            setcookie('sale_setting_success',$discount_success_mesg, time() + 5 );
            $pdo->commit();
            header('location:my-sales.php#discounts');
            exit();

        }else{
            $discount_error_mesg = 'Some Error Occurred. Try Again. ';
            setcookie('sale_setting_error',$discount_error_mesg, time() + 5 );
            
            $pdo->rollback();
            header('location:my-sales.php#discounts');
            exit();
        }

    }else{
        // update
		
        $update_sale_setting = $pdo->prepare('UPDATE  tbl_customer_discount_setting SET discount_rate_supersale_period =? Where cds_id=?');
        $update_sale_setting->execute(array(
            $_POST['super_sale_discount_percentage'],
            $setting_first_row['cds_id']
        ));
		
		$discount_success_mesg = 'Super Sale Setting Set successfully';
		setcookie('sale_setting_success',$discount_success_mesg, time() + 5 );
		$pdo->commit();
		header('location:my-sales.php#discounts');
        exit();
    }

}

if(isset($_POST['sale_participation_bit'])){
    
    if(!isset($_SESSION['customer'])) {
        header('location: '.BASE_URL.'logout.php');
        exit;
    }

    $pdo->begintransaction();

    $check_exist_setting = $pdo->prepare('Select * from tbl_customer_discount_setting where customer_id =?');
    $check_exist_setting->execute(array($_SESSION['customer']['cust_id']));
    $setting_first_row = $check_exist_setting->fetch(PDO::FETCH_ASSOC);
    
  
    if(empty($setting_first_row)){

        $insert_sale_setting = $pdo->prepare('INSERT into tbl_customer_discount_setting (customer_id,participation_bit) VALUES (?,?)');
        $insert_sale_setting->execute(array(
            $_SESSION['customer']['cust_id'],
            $_POST['sale_participation_bit']
        ));

        if($pdo->lastInsertId()){
            $discount_success_mesg = 'You selected to participate in the Sale successfully';
            $response_array = ['status'=>'success','msg'=>$discount_success_mesg];
		
            $pdo->commit();
           

        }else{
            $discount_error_mesg = 'Some Error Occurred. Try Again. ';
            $response_array = ['status'=>'error','msg'=>$discount_error_mesg];
		
            $pdo->rollback();
            

        }
    }else{

        // update
		
        $update_sale_setting = $pdo->prepare('UPDATE  tbl_customer_discount_setting SET participation_bit =? Where cds_id=?');
        $update_sale_setting->execute(array(
            $_POST['sale_participation_bit'],
            $setting_first_row['cds_id']
        ));
		
		$response_array = ['status'=>'success','msg'=>'Sale Setting Set successfully'];
		$pdo->commit();
		
    }

    echo json_encode($response_array);
    exit();
}


if(isset($_REQUEST['agree_proof_id'])){
    
    // Getting photo ID to unlink from folder
	$statement = $pdo->prepare("SELECT * FROM tbl_agreement_proof WHERE id=?");
	$statement->execute(array($_REQUEST['agree_proof_id']));
	$result = $statement->fetch(PDO::FETCH_ASSOC);							
	
    $proof_file = $result['proof_file'];
	

	// Unlink the proof_file
	if($proof_file!='') {
		unlink('public_files/uploads/agreement_photo/'.$proof_file);	
	}

	// Delete from tbl_testimonial
	$statement = $pdo->prepare("DELETE FROM tbl_agreement_proof WHERE id=?");
	$statement->execute(array($_REQUEST['agree_proof_id']));

	header('location: payment-agreement.php');
}

if(isset($_REQUEST['publishing_model'])){
    
    
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time',1000);

    $pdo->begintransaction();
    $no_zip =0;
    $archive_file_ext = [];
	$valid = 1;
    $model_path ='';
    $error_message ='';
    $product_model_name ='';
    
    if(empty($_POST['tcat_id'])) {
        $valid = 0;
        $error_message .= "You must have to select a category<br>";
        echo json_encode(['status'=>'error','msg'=>$error_message]);
        exit;
    }

    if(empty($_POST['p_name'])) {
        $valid = 0;
        $error_message .= "Product name can not be empty<br>";
        echo json_encode(['status'=>'error','msg'=>$error_message]);
        exit;
    }

	if(isset($_POST['is_free'])){
		$is_free = $_POST['is_free'];
	}else{
		$is_free = 0;
		if(empty($_POST['p_current_price'])) {
			$valid = 0;
			$error_message .= "Current Price can not be empty<br>";
            echo json_encode(['status'=>'error','msg'=>$error_message]);
            exit;
		}
	}
    
    if(empty($_POST['p_description'])) {
        $valid = 0;
        $error_message .= "Description can not be empty<br>";
        echo json_encode(['status'=>'error','msg'=>$error_message]);
        exit;
    }

	if(empty($_POST['p_tags'])) {
        $valid = 0;
        $error_message .= "Tags can not be empty<br>";
        echo json_encode(['status'=>'error','msg'=>$error_message]);
        exit;
    }

	if(empty($_POST['p_license'])) {
        $valid = 0;
        $error_message .= "License can not be empty<br>";
        echo json_encode(['status'=>'error','msg'=>$error_message]);
        exit;

    }else if($_POST['p_license'] == 'custom'){
		if(empty($_POST['p_custom_license'])){
			$valid = 0;
        	$error_message .= "Custom License can not be empty<br>";
            echo json_encode(['status'=>'error','msg'=>$error_message]);
            exit;
		}
	}

    // featured photo uploaded validation
    $path = $_FILES['p_featured_photo']['name'];
    $path_tmp = $_FILES['p_featured_photo']['tmp_name'];
    
    if($path!='') {
        
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
		$file_size = $_FILES['p_featured_photo']['size'];

        if( $ext!='jpg' && $ext!='jpeg' && $ext!='png' && $ext!='bmp' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, png or bmp file<br>';
            echo json_encode(['status'=>'error','msg'=>$error_message]);
            exit;
        }

        if($file_size > 5242880){
            $valid = 0;
            $error_message .= 'Featured Image Size can be only till 5MB <br>';
            echo json_encode(['status'=>'error','msg'=>$error_message]);
            exit;
        }

    } else {
    	$valid = 0;
        $error_message .= 'You must have to select a featured photo<br>';
        echo json_encode(['status'=>'error','msg'=>$error_message]);
        exit;
    }
    if(isset($_FILES['temp_prod_model_file'])){
        $product_model_name = $_FILES['temp_prod_model_file']['name'];
    }

	// validating uploaded model files
	if(!empty($product_model_name)){

        foreach($product_model_name as $name_ind => $name_detail){

            $product_model_ext = pathinfo($name_detail,PATHINFO_EXTENSION); // return file extention
            $product_model_size = $_FILES['temp_prod_model_file']['size'][$name_ind];

            if($product_model_ext != '3dm' && $product_model_ext != 'jcd' && $product_model_ext != 'stl'){
                $valid = 0;
                $error_message .= 'Please upload model file : '.$name_detail.' format in 3dm, jcd or stl .<br>'; 
                $no_zip ++;
                echo json_encode(['status'=>'error','msg'=>$error_message]);
                exit;

            }else{
                $archive_file_ext[] = $product_model_ext;
            }

            if($product_model_size > 5368706371){
                $valid = 0;
                $error_message .= 'Uploaded Model: '.$name_detail.' size can not be greater than 5GB <br>';
                $no_zip ++;
                echo json_encode(['status'=>'error','msg'=>$error_message]);
                exit;
            }

            $archive_file_ext = array_unique(array_filter($archive_file_ext));

        }
		
	}else{
		$valid = 0;
        $error_message .= 'You must select product model file.<br>';
        $no_zip ++;
        echo json_encode(['status'=>'error','msg'=>$error_message]);
        exit;
	}
    
    if( isset($_FILES['photos']["name"]) && isset($_FILES['photos']["tmp_name"]) )
	{
		$photo_validation = array();
		$photo_validation = $_FILES['photos']["name"];
		$photo_validation = array_values(array_filter($photo_validation));

        if(count($photo_validation)>0){
            for($i=0;$i<count($photo_validation);$i++)
            {
                $my_ext1_validation = pathinfo( $photo_validation[$i], PATHINFO_EXTENSION );
                $my_photo_size       = $_FILES['photos']['size'][$i];
                if( $my_ext1_validation !='jpg' && $my_ext1_validation !='png' && $my_ext1_validation !='jpeg' && $my_ext1_validation !='bmp' ) {
                    $valid = 0;
                    $error_message .= 'You must have to upload jpg, jpeg, png or bmp file.<br>';
                    echo json_encode(['status'=>'error','msg'=>$error_message]);
                    exit;
                }
                if($my_photo_size > 5242880){
                    $valid = 0;
                    $error_message .= 'Preview Photos Size can be only till 5MB <br>';
                    echo json_encode(['status'=>'error','msg'=>$error_message]);
                    exit;
                }
            }
        }
		

	}

    if($valid == 1){
        $statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_product'");
		$statement->execute();
		$result = $statement->fetchAll();
		foreach($result as $row) {
			$ai_id=$row[10];
		}
    }

    $prod_array=[];
    if($valid == 1 && $no_zip == 0){

        $product_model_path_tmp = $_FILES['temp_prod_model_file']['tmp_name'];
        $model_path = 'public_files/uploads/temp_model_files/'.$_POST['p_name'].time();
        if(!is_dir($model_path)){
			mkdir($model_path,0777,true);
		}
        foreach($product_model_name as $name_ind => $name_detail){

            $product_model_ext = pathinfo($name_detail,PATHINFO_EXTENSION); // return file extention
            $product_model_filename = basename($name_detail,'.'.$product_model_ext); // this will remove ext and return name
            
            $product_model_filename = $product_model_filename.$name_ind.'_'.time().'.'.$product_model_ext;
            $prod_array[] = $product_model_filename;
            $prod_uploaded = move_uploaded_file( $product_model_path_tmp[$name_ind], $model_path.'/'.$product_model_filename );

            if(!$prod_uploaded){
                $valid = 0;
                $error_message .="Error Occurred while uploading file try again.";
                $pdo->rollback();
                echo json_encode(['status'=>'error','msg'=>$error_message]);

                array_map('unlink', array_filter(
                    (array) array_merge(glob($model_path."/*"))));
                rmdir($model_path);
                exit();
            }
            $statement = $pdo->prepare("INSERT INTO temp_modal_files_upload (`prod_id`,`file_path`,`filename`) VALUES (?,?,?)");
            $statement->execute(array($ai_id,$model_path,$product_model_filename));

        }
    }

	
    if($valid == 1) {

		$model_name = $_POST['p_name'];

    	if( isset($_FILES['photos']["name"]) && isset($_FILES['photos']["tmp_name"]) )
        {
        	$photo = array();
            $photo = $_FILES['photos']["name"];
            $photo = array_values(array_filter($photo));

            $photo_temp = $_FILES['photos']["tmp_name"];

            // get last id from table
        	$statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_product_photo'");
			$statement->execute();
			$result = $statement->fetchAll();
			foreach($result as $row) {
				$next_id1=$row[10];
			}
			$z = $next_id1;

            foreach($photo as $i => $p_name)
            {
                $my_ext1 = pathinfo( $p_name, PATHINFO_EXTENSION );
                $photo_first_name = basename( $p_name, '.'.$my_ext1 );
                $final_name1 = $photo_first_name.$z.'.'.$my_ext1;

                move_uploaded_file($photo_temp[$i],"public_files/uploads/product_photos/".$final_name1);
                $z++;

                $statement = $pdo->prepare("INSERT INTO tbl_product_photo (photo,p_id) VALUES (?,?)");
                $statement->execute(array($final_name1,$ai_id));
		        
            }

                      
        }

        // featured image
		$final_name = 'product-featured-'.$ai_id.'.'.$ext;
        $feature_uploaded = move_uploaded_file( $path_tmp, 'public_files/uploads/'.$final_name );
		if(!$feature_uploaded){
			$error_message .="Error Occurred while uploading image try again.";
			$pdo->rollback();
            
            echo json_encode(['status'=>'error','msg'=>$error_message]);
                
            array_map('unlink', array_filter(
                (array) array_merge(glob($model_path."/*"))));
            rmdir($model_path);
            exit();
		}

        if(empty($_POST['mcat_id'])){
            $_POST['mcat_id'] = 0;
        }

		//Saving data into the main table tbl_product
		$statement = $pdo->prepare("INSERT INTO tbl_product(
										p_name,
										p_sku,
										p_old_price,
										p_current_price,
										cat_id,
										subcat_id,
										prod_model_file,
										file_extension,
										p_featured_photo,
										youtube_prev,
										vimeo_prev,
										p_description,
										p_tags,
										
										p_license,
										p_custom_license,
										p_total_view,
										p_is_featured,
										p_is_active,
										user_type,
										`user_id`,
										is_free) 
										VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$statement->execute(array(
										$_POST['p_name'],
										$_POST['p_sku'],
										$_POST['p_old_price'],
										$_POST['p_current_price'],
										$_POST['tcat_id'],
										$_POST['mcat_id'],
                                        '',
										implode(',',$archive_file_ext),
										$final_name,
										$_POST['youtube_prev'],
										$_POST['vimeo_prev'],
										$_POST['p_description'],
										$_POST['p_tags'],
										
										$_POST['p_license'],
										$_POST['p_custom_license'],
										0,
										$_POST['p_is_featured'],
										$_POST['p_is_active'],
										'Customer',
										$_SESSION['customer']['cust_id'],
										$is_free
									));
		
		if(empty($error_message) && $pdo->lastInsertId()>0){
			$pdo->commit();
			$success_message = 'Product is added successfully.';
            echo json_encode(['status'=>'success','msg'=>$success_message]);
            setcookie('publishing_success',$success_message, time() + 5 );
            exit();

		}else{

            $error_message .="Some Error Occured While saving file please try again";
			$pdo->rollback();
            
            echo json_encode(['status'=>'error','msg'=>$error_message]);
                
            array_map('unlink', array_filter(
                (array) array_merge(glob($model_path."/*"))));
            rmdir($model_path);
            exit();

        }
    	
    }
}


if(isset($_GET['model_file_del'])){
    
    $select_query = $pdo->prepare("SELECT prod_id, REPLACE(file_path, '../', '') as file_path,`filename` FROM temp_modal_files_upload WHERE id=?");
    $select_query->execute(array($_GET['model_file_id']));
    $select = $select_query->fetch(PDO::FETCH_ASSOC);
    
    $delete_temp_files = $pdo->prepare("DELETE FROM temp_modal_files_upload WHERE id=?");
    $delete_temp_files->execute(array($_GET['model_file_id']));
    
    if(file_exists($select['file_path'].'/'.$select['filename'])){
        unlink($select['file_path'].'/'.$select['filename']);
    }
    
    $dir = $select['file_path'];
    
    if(count(glob("$dir/*")) === 0){
        rmdir($dir);
    }
    echo json_encode(['status'=>'success']);
}

if(isset($_REQUEST['my-model-edit'])) {

    ini_set('memory_limit', '-1');
    ini_set('max_execution_time',1000);

    $pdo->begintransaction();
    $no_zip =0;
	$valid = 1;
    $archive_file_ext = [];
    $model_path ='';
    $error_message ='';
    $product_model_name = '';

    if(empty($_POST['tcat_id'])) {
        $valid = 0;
        $error_message .= "You must have to select a category<br>";
        echo json_encode(['status'=>'error','msg'=>$error_message]);
        exit;
    }

    if(empty($_POST['p_name'])) {
        $valid = 0;
        $error_message .= "Product name can not be empty<br>";
        echo json_encode(['status'=>'error','msg'=>$error_message]);
        exit;
    }

	if(isset($_POST['is_free'])){
		$is_free = $_POST['is_free'];
	}else{
		$is_free = 0;
		if(empty($_POST['p_current_price'])) {
			$valid = 0;
			$error_message .= "Current Price can not be empty<br>";
            echo json_encode(['status'=>'error','msg'=>$error_message]);
            exit;
		}
	}

	if(empty($_POST['p_description'])) {
        $valid = 0;
        $error_message .= "Description can not be empty<br>";
        echo json_encode(['status'=>'error','msg'=>$error_message]);
        exit;
    }

	if(empty($_POST['p_tags'])) {
        $valid = 0;
        $error_message .= "Tags can not be empty<br>";
        echo json_encode(['status'=>'error','msg'=>$error_message]);
        exit;
    }

	if(empty($_POST['p_license'])) {

        $valid = 0;
        $error_message .= "License can not be empty<br>";
        echo json_encode(['status'=>'error','msg'=>$error_message]);
        exit;

    }else if($_POST['p_license'] == 'custom'){

		if(empty($_POST['p_custom_license'])){
			$valid = 0;
        	$error_message .= "Custom License can not be empty<br>";
            echo json_encode(['status'=>'error','msg'=>$error_message]);
            exit;
		}
	}

    $path = $_FILES['p_featured_photo']['name'];
    $path_tmp = $_FILES['p_featured_photo']['tmp_name'];

    if($path!='') {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );

        $file_size = $_FILES['p_featured_photo']['size'];

        if( $ext!='jpg' && $ext!='jpeg' && $ext!='png' && $ext!='bmp' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, png or bmp file<br>';
            echo json_encode(['status'=>'error','msg'=>$error_message]);
            exit;
        }

        if($file_size > 5242880){
            $valid = 0;
            $error_message .= 'Featured Image Size can be only till 5MB <br>';
            echo json_encode(['status'=>'error','msg'=>$error_message]);
            exit;
        }
    }

    $present_model_files_query = $pdo->prepare('Select * from temp_modal_files_upload where prod_id = ?');
    $present_model_files_query->execute([ $_POST['product_id'] ]);
    $present_model_files = $present_model_files_query->fetchAll(PDO::FETCH_ASSOC);

    // if temp_prod_model_file present count and validate file
    if(isset($_FILES['temp_prod_model_file'])){
        $product_model_name = $_FILES['temp_prod_model_file']['name'];
    }    
    // if no model file present
    if(count($present_model_files) <= 0){
        // and no model files submitted
        if(empty($product_model_name)){
            $valid = 0;
            $error_message .= 'You must select product model file.<br>';
            $no_zip ++;
            echo json_encode(['status'=>'error','msg'=>$error_message]);
            exit;
        }
    }
    

	if(!empty($product_model_name)){

        foreach($product_model_name as $name_ind => $name_detail){

            $product_model_ext = pathinfo($name_detail,PATHINFO_EXTENSION);

            $product_model_size = $_FILES['temp_prod_model_file']['size'][$name_ind];

            
            if($product_model_ext != '3dm' && $product_model_ext != 'jcd' && $product_model_ext != 'stl'){
                $valid = 0;
                $error_message .= 'Please upload model file : '.$name_detail.' format in 3dm, jcd or stl .<br>'; 
                $no_zip ++;
                echo json_encode(['status'=>'error','msg'=>$error_message]);
                exit;

            }

            if($product_model_size > 5368706371){
                $valid = 0;
                $error_message .= 'Uploaded Model: '.$name_detail.' size can not be greater than 5GB <br>';
                $no_zip ++;
                echo json_encode(['status'=>'error','msg'=>$error_message]);
                exit;
            }

            

        }
	}
    
    if( isset($_FILES['photos']["name"]) && isset($_FILES['photos']["tmp_name"]) )
	{
		$photo_validation = array();
		$photo_validation = $_FILES['photos']["name"];
		$photo_validation = array_values(array_filter($photo_validation));

        if(count($photo_validation)>0){
            for($i=0;$i<count($photo_validation);$i++)
            {
                // cheking for not deleted photos
                if(in_Array($i,$_POST['old'])){

                    $my_ext1_validation = pathinfo( $photo_validation[$i], PATHINFO_EXTENSION );
                    $my_photo_size       = $_FILES['photos']['size'][$i];
                    if( $my_ext1_validation !='jpg' && $my_ext1_validation !='png' && $my_ext1_validation !='jpeg' && $my_ext1_validation !='bmp' ) {
                       
                        $valid = 0;
                        $error_message .= 'You must have to upload jpg, jpeg, png or bmp file.<br>';
                        echo json_encode(['status'=>'error','msg'=>$error_message]);
                        exit;
                    }
                    if($my_photo_size > 5242880){
                       
                        $valid = 0;
                        $error_message .= 'Preview Photos Size can be only till 5MB <br>';
                        echo json_encode(['status'=>'error','msg'=>$error_message]);
                        exit;
                    }

                }

            }
        }
		

	}
	
    $ai_id = $_POST['product_id'];

    if($valid == 1 && $no_zip == 0){
        if(!empty($product_model_name)){
            $product_model_path_tmp = $_FILES['temp_prod_model_file']['tmp_name'];

            // if folder already present then path taken from table
            if(count($present_model_files)>0){
                $model_path = $present_model_files[0]['file_path'];
            }else{

                // new path created
                $model_path = 'public_files/uploads/temp_model_files/'.$_POST['p_name'].time();

                // directory creation
                if(!is_dir($model_path)){
                    mkdir($model_path,0777,true);
                }
            }

            foreach($product_model_name as $name_ind => $name_detail){
                
                $product_model_ext = pathinfo($name_detail,PATHINFO_EXTENSION); // return file extention

                $product_model_filename = basename($name_detail,'.'.$product_model_ext); // this will remove ext and return name
                
                $product_model_filename = $product_model_filename.$name_ind.'_'.time().'.'.$product_model_ext;

                $prod_uploaded = move_uploaded_file($product_model_path_tmp[$name_ind], $model_path.'/'.$product_model_filename );

                if(!$prod_uploaded){
                    $valid = 0;
                    $error_message .="Error Occurred while uploading file try again.";
                    $pdo->rollback();
                    echo json_encode(['status'=>'error','msg'=>$error_message]);

                    array_map('unlink', array_filter(
                        (array) array_merge(glob($model_path."/*"))));
                    rmdir($model_path);
                    exit();
                }

                $statement = $pdo->prepare("INSERT INTO temp_modal_files_upload (`prod_id`,`file_path`,`filename`) VALUES (?,?,?)");
                $statement->execute(array($ai_id,$model_path,$product_model_filename));
            }

        }
    }

    if($valid == 1) {

    	if( isset($_FILES['photos']["name"]) && isset($_FILES['photos']["tmp_name"]) )
        {

        	$photo = array();
            $photo = $_FILES['photos']["name"];
            $photo = array_values(array_filter($photo));

            $photo_temp = $_FILES['photos']["tmp_name"];

        	$statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_product_photo'");
			$statement->execute();
			$result = $statement->fetchAll();
			foreach($result as $row) {
				$next_id1=$row[10];
			}
			$z = $next_id1;

            foreach($photo as $i => $p_name)
            {
                // moving only not deleted photos
                if(in_Array($i,$_POST['old'])){
                    $my_ext1 = pathinfo( $p_name, PATHINFO_EXTENSION );
                    $photo_first_name = basename( $p_name, '.'.$my_ext1 );
                    $final_name1 = $photo_first_name.$z.'.'.$my_ext1;

                    move_uploaded_file($photo_temp[$i],"public_files/uploads/product_photos/".$final_name1);
                    $z++;

                    $statement = $pdo->prepare("INSERT INTO tbl_product_photo (photo,p_id) VALUES (?,?)");
                    $statement->execute(array($final_name1,$ai_id));
                }
		        
            }          
        }

        // checking featured image
        if(!empty($path)){
            if(file_exists('public_files/uploads/'.$_POST['current_photo'])){
                unlink('public_files/uploads/'.$_POST['current_photo']);
            }
            
            $final_name = 'product-featured-'.$ai_id.'.'.$ext;
            move_uploaded_file( $path_tmp, 'public_files/uploads/'.$final_name );
        }else{
            $final_name = $_POST['current_photo'];
        }
        
        /**
        ** model upload
        ** if no model is uploaded  
         */ 
        
        if(!empty($product_model_name)){
            
            // get files stored fron table 
            $temp_files_table_query = $pdo->prepare("SELECT prod_id, REPLACE(file_path, '../', '') as file_path,`filename`  from temp_modal_files_upload WHERE prod_id=?");
            $temp_files_table_query->execute([$ai_id]);
            $temp_files_table = $temp_files_table_query->fetchAll(PDO::FETCH_ASSOC);

            foreach($temp_files_table as $ind => $files_detail){

                $product_model_ext = pathinfo($files_detail['filename'],PATHINFO_EXTENSION);
                $archive_file_ext[]= $product_model_ext;

            }
                
            $archive_file_ext = array_unique(array_filter($archive_file_ext));
            $archive_file_ext = implode(',',$archive_file_ext);

        }else{
            
            $archive_file_ext= $_POST['old_file_extension'];
        }

        if(empty($_POST['mcat_id'])){
            $_POST['mcat_id'] = 0;
        }

        $statement = $pdo->prepare("UPDATE tbl_product SET 
                                p_name=?, 
                                p_sku=?,
                                p_old_price=?, 
                                p_current_price=?, 
                                cat_id=?,
                                subcat_id=?,
                                prod_model_file=?,
                                p_featured_photo=?,
                                youtube_prev=?,
                                vimeo_prev=?,
                                p_description=?,
                                p_tags=?,
                                
                                p_license=?,
                                p_custom_license=?,
                                p_is_featured=?,
                                p_is_active=?,
                                is_free=?,
                                file_extension = ?

                                WHERE p_id=?");
        $statement->execute(array(
                                $_POST['p_name'],
                                $_POST['p_sku'],
                                $_POST['p_old_price'],
                                $_POST['p_current_price'],
                                $_POST['tcat_id'],
                                $_POST['mcat_id'],
                                // $zip_file_name,
                                '',
                                $final_name,
                                $_POST['youtube_prev'],
                                $_POST['vimeo_prev'],
                                $_POST['p_description'],
                                $_POST['p_tags'],
                                $_POST['p_license'],
                                $_POST['p_custom_license'],
                                $_POST['p_is_featured'],
                                $_POST['p_is_active'],
                                $is_free,
                                $archive_file_ext,
                                $ai_id
                            ));
        
        if(empty($error_message)){
            $success_message = 'Product is updated successfully.';

            $pdo->commit();
            echo json_encode(['status'=>'success','msg'=>$success_message]);
            setcookie('publishing_success',$success_message, time() + 5 );
            exit();
        }
		
    }
}

?>