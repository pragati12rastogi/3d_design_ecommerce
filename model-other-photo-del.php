<?php 
	ob_start();
	session_start();
	include("admin/inc/config.php");
	include("admin/inc/functions.php");
	include("admin/inc/CSRF_Protect.php");
	$csrf = new CSRF_Protect();
		
?>

<?php
if( !isset($_REQUEST['id']) || !isset($_REQUEST['id1']) ) {
	
	$result = ['status'=>'error','msg'=>'No Photo ID\'s found','redirect'=>'logout.php'];
	echo json_encode($result);

	exit;

} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM tbl_product_photo WHERE pp_id=? and p_id=?");
	$statement->execute(array($_REQUEST['id'],$_REQUEST['id1']));
	$total = $statement->rowCount();
	if( $total == 0 ) {

		$result = ['status'=>'error','msg'=>'Wrong photo id found. Kindly reload.','redirect'=>'my-model-edit.php?id='.$_REQUEST["id1"]];
		echo json_encode($result);
		
		exit;
	}
}
?>

<?php

	// Getting photo ID to unlink from folder
	$statement = $pdo->prepare("SELECT * FROM tbl_product_photo WHERE pp_id=? and p_id=?");
	$statement->execute(array($_REQUEST['id'],$_REQUEST['id1']));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) {
		$photo = $row['photo'];
	}

	// Unlink the photo
	if($photo!='') {
		unlink('public_files/uploads/product_photos/'.$photo);	
	}

	// Delete from tbl_testimonial
	$statement = $pdo->prepare("DELETE FROM tbl_product_photo WHERE pp_id=? and p_id=?");
	$statement->execute(array($_REQUEST['id'],$_REQUEST['id1']));

	$result = ['status'=>'success','msg'=>'Photo Deleted'];
	echo json_encode($result);
	exit();
?>