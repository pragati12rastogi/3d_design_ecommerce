<?php

ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();

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


if(isset($_POST['payout_submit'])){
    // insert data in payout table

    $pdo->begintransaction();

    if(empty($_POST['transaction_method'])){
        $pdo->rollback();
        setcookie('admin_payout_error','Transaction method cannot be left blank',time()+5);
        header('location:pending-payout.php');
    }
    if(empty($_POST['transaction_id'])){
        $pdo->rollback();
        setcookie('admin_payout_error','Transaction Id cannot be left blank',time()+5);
        header('location:pending-payout.php');
        
    }

    $check_payout_sql = $pdo->prepare('SELECT * from tbl_payout where `vendor_id` =? and  `payout_month`=? and  `payout_year`=?');
    $check_payout_sql->execute([$_POST['vendor_id'],$_POST['payout_month'],$_POST['payout_year']]);
    $check_payout = $check_payout_sql->fetch(PDO::FETCH_ASSOC);

    if(empty($check_payout)){

        $insert_payout_sql = $pdo->prepare('INSERT INTO `tbl_payout`( `vendor_id`,order_ids, `payout_month`, `payout_year`, `payout_amt`, `transaction_method`, `transaction_id`, `created_at`) VALUES (?,?,?,?,?,?,?,?)');
        $insert_payout_sql->execute([$_POST['vendor_id'],$_POST['order_ids'],$_POST['payout_month'],$_POST['payout_year'],$_POST['payout_amt'],$_POST['transaction_method'],$_POST['transaction_id'],date('Y-m-d H:i:s')]);

        if($pdo->lastInsertId() > 0){
            $pdo->commit();
            setcookie('admin_payout_success','Payout is update successfully',time()+5);
            header('location:pending-payout.php');

        }else{
            $pdo->rollback();
            setcookie('admin_payout_error','Payout is not update try again',time()+5);
            header('location:pending-payout.php');

        }

    }else{
        $pdo->rollback();
        setcookie('admin_payout_error','This month payout already given to this vendor',time()+5);
        header('location:pending-payout.php');
    }
    
    
}

if(isset($_GET['model_file_del'])){
    
    $select_query = $pdo->prepare("SELECT prod_id, REPLACE(file_path, '../', '') as file_path,`filename` FROM temp_modal_files_upload WHERE id=?");
    $select_query->execute(array($_GET['model_file_id']));
    $select = $select_query->fetch(PDO::FETCH_ASSOC);
    
    $delete_temp_files = $pdo->prepare("DELETE FROM temp_modal_files_upload WHERE id=?");
    $delete_temp_files->execute(array($_GET['model_file_id']));
    
    if(file_exists('../'.$select['file_path'].'/'.$select['filename'])){
        unlink('../'.$select['file_path'].'/'.$select['filename']);
    }
    
    $dir = '../'.$select['file_path'];
    
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

    $present_model_files_query = $pdo->prepare('Select prod_id, REPLACE(file_path, "../", "") as file_path,`filename` from temp_modal_files_upload where prod_id = ?');
    $present_model_files_query->execute([ $_POST['product_id'] ]);
    $present_model_files = $present_model_files_query->fetchAll(PDO::FETCH_ASSOC);

    // if temp_prod_model_file present 
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
                if(!is_dir('../'.$model_path)){
                    mkdir('../'.$model_path,0777,true);
                }
            }

            foreach($product_model_name as $name_ind => $name_detail){
                
                $product_model_ext = pathinfo($name_detail,PATHINFO_EXTENSION); // return file extention

                $product_model_filename = basename($name_detail,'.'.$product_model_ext); // this will remove ext and return name
                
                $product_model_filename = $product_model_filename.$name_ind.'_'.time().'.'.$product_model_ext;

                $prod_uploaded = move_uploaded_file( $product_model_path_tmp[$name_ind], '../'.$model_path.'/'.$product_model_filename );

                if(!$prod_uploaded){
                    $valid = 0;
                    $error_message .="Error Occurred while uploading file try again.";
                    $pdo->rollback();
                    echo json_encode(['status'=>'error','msg'=>$error_message]);

                    array_map('unlink', array_filter(
                        (array) array_merge(glob('../'.$model_path."/*"))));
                    rmdir('../'.$model_path);
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

                    move_uploaded_file($photo_temp[$i],"../public_files/uploads/product_photos/".$final_name1);
                    $z++;

                    $statement = $pdo->prepare("INSERT INTO tbl_product_photo (photo,p_id) VALUES (?,?)");
                    $statement->execute(array($final_name1,$ai_id));
                }
		        
            }          
        }

        // checking featured image
        if(!empty($path)){
            if(file_exists('../public_files/uploads/'.$_POST['current_photo'])){
                unlink('../public_files/uploads/'.$_POST['current_photo']);
            }
            
            $final_name = 'product-featured-'.$ai_id.'.'.$ext;
            move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );
        }else{
            $final_name = $_POST['current_photo'];
        }
        
        /**
        ** model upload
        ** if no model is uploaded  
         */ 
        if(!empty($product_model_name)){
            
            // get files stored fron table 
            $temp_files_table_query = $pdo->prepare("SELECT * from temp_modal_files_upload WHERE prod_id=?");
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

if(isset($_REQUEST['publishing_model'])){
    
    
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time',1000);

    $pdo->begintransaction();
    $no_zip =0;
    $archive_file_ext = [];
	$valid = 1;
    $model_path ='';
    $admin_model_path = '';
    $error_message ='';

    if(empty($_POST['mcat_id'])){
        $_POST['mcat_id'] = 0;
    }

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

    $product_model_name = $_FILES['temp_prod_model_file']['name'];
    

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
        $admin_model_path = '../'.$model_path;
        if(!is_dir($admin_model_path)){
			mkdir($admin_model_path,0777,true);
		}
        foreach($product_model_name as $name_ind => $name_detail){

            $product_model_ext = pathinfo($name_detail,PATHINFO_EXTENSION); // return file extention
            $product_model_filename = basename($name_detail,'.'.$product_model_ext); // this will remove ext and return name
            
            $product_model_filename = $product_model_filename.$name_ind.'_'.time().'.'.$product_model_ext;
            $prod_array[] = $product_model_filename;
            $prod_uploaded = move_uploaded_file( $product_model_path_tmp[$name_ind], $admin_model_path.'/'.$product_model_filename );

            if(!$prod_uploaded){
                $valid = 0;
                $error_message .="Error Occurred while uploading file try again.";
                $pdo->rollback();
                echo json_encode(['status'=>'error','msg'=>$error_message]);

                array_map('unlink', array_filter(
                    (array) array_merge(glob($admin_model_path."/*"))));
                rmdir($admin_model_path);
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

                move_uploaded_file($photo_temp[$i],"../public_files/uploads/product_photos/".$final_name1);
                $z++;

                $statement = $pdo->prepare("INSERT INTO tbl_product_photo (photo,p_id) VALUES (?,?)");
                $statement->execute(array($final_name1,$ai_id));
		        
            }

                      
        }

        // featured image
		$final_name = 'product-featured-'.$ai_id.'.'.$ext;
        $feature_uploaded = move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );
		if(!$feature_uploaded){
			$error_message .="Error Occurred while uploading image try again.";
			$pdo->rollback();
            
            echo json_encode(['status'=>'error','msg'=>$error_message]);
                
            array_map('unlink', array_filter(
                (array) array_merge(glob($admin_model_path."/*"))));
            rmdir($admin_model_path);
            exit();
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
										'Admin',
										$_SESSION['user']['id'],
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

?>