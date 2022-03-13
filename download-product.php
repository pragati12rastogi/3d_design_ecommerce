<?php
ob_start();
session_start();
include("admin/inc/config.php");
include("admin/inc/functions.php");
include("admin/inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();


if(isset($_REQUEST['zip_product'])){

    if(!isset($_SESSION['customer'])){
        setcookie("notlogin-alert-danger", "Please login first for free download.", time()+ 5,'/');
        header('location: login.php');
        exit();
    }

	$prod_id= $_REQUEST['zip_product'];
	
	$get_prod = $pdo->prepare("SELECT * FROM tbl_product where p_id = ? and is_delete = 0") ;
	$get_prod->execute([$prod_id]);
	$prod_assoc = $get_prod->fetch(PDO::FETCH_ASSOC);

	if(!empty($prod_assoc) && count($prod_assoc)> 0){

		$get_prod_photo = $pdo->prepare("SELECT * FROM tbl_product_photo where p_id = ?") ;
		$get_prod_photo->execute([$prod_id]);
		$prod_photo_assoc = $get_prod_photo->fetchAll(PDO::FETCH_ASSOC);
		
		$temp_files_table_query = $pdo->prepare("SELECT prod_id, REPLACE(file_path, '../', '') as file_path,`filename`  from temp_modal_files_upload WHERE prod_id=?");
		$temp_files_table_query->execute([$prod_assoc['p_id']]);
		$temp_files_table = $temp_files_table_query->fetchAll(PDO::FETCH_ASSOC);
		
		$zip_obj = new ZipArchive();
		
		$archive_file_name = $prod_assoc['p_name'].'_'.time().'.zip';

		if ($zip_obj->open($archive_file_name, ZipArchive::CREATE)!==TRUE) {

			alert("cannot open <$model_zip>");
			header('location:'.$_SERVER['HTTP_REFERER']);
			exit();

		}else{

			foreach($temp_files_table as $ind => $files_detail){

				$zip_obj->addFile($files_detail['file_path'].'/'.$files_detail['filename'],$files_detail['filename']);

			}
			$featured_pic = "public_files/uploads/".$prod_assoc['p_featured_photo'];

			$zip_obj->addFile($model_zip,$zip_file_name);

			$get_featuredext = pathinfo($prod_assoc['p_featured_photo'],PATHINFO_EXTENSION);
			$featuredform_name = 'PreviewImage_1.'.$get_featuredext;
			$zip_obj->addFile($featured_pic,$featuredform_name);
			

			foreach($prod_photo_assoc as $in => $photo_assoc){
				$get_ext = pathinfo($photo_assoc['photo'],PATHINFO_EXTENSION);
				$form_name = 'PreviewImage_'.($in+2).'.'.$get_ext;
				$zip_obj->addFile("public_files/uploads/product_photos/".$photo_assoc['photo'],$form_name);
			}

			$zip_obj->close();

			header("Content-type: application/zip"); 
			header('Content-Disposition: attachment; filename="'.$archive_file_name.'"'); 
			header("Pragma: no-cache"); 
			header("Expires: 0"); 
			readfile("$archive_file_name");

			unlink("$archive_file_name");
			

			// insert on table download count for customer not for admin
			
			$upd_download_count = $pdo->prepare('Insert into tbl_download_count (`prod_id`, `customer_id`, `sale_type`, `created_at`) VALUES (?,?,?,?)');
			$upd_download_count->execute([$prod_id,$_SESSION['customer']['cust_id'],'Free',date('Y-m-d H:i:s')]);
			
			
			exit;
		}

	}else{
		alert('Product not exist');
		header('location:'.$_SERVER['HTTP_REFERER']);
		exit();
	}
	

}

?>