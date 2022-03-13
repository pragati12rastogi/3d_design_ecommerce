<?php require_once('header.php'); ?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	}
}
?>

<?php
	// Getting photo ID to unlink from folder
	$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
	$statement->execute(array($_REQUEST['id']));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) {
		$p_featured_photo = $row['p_featured_photo'];
		unlink('../public_files/uploads/'.$p_featured_photo);

		if(file_exists('../public_files/uploads/'.$row['prod_model_file'])){
            unlink('../public_files/uploads/'.$row['prod_model_file']);
        }
	}

	// Getting other photo ID to unlink from folder
	$statement = $pdo->prepare("SELECT * FROM tbl_product_photo WHERE p_id=?");
	$statement->execute(array($_REQUEST['id']));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) {
		$photo = $row['photo'];
		unlink('../public_files/uploads/product_photos/'.$photo);
	}

	$select_model_files = $pdo->prepare("SELECT prod_id, REPLACE(file_path, '../', '') as file_path,`filename` FROM temp_modal_files_upload WHERE prod_id=?");
    $select_model_files->execute(array($_REQUEST['id']));
    $model_files = $select_model_files->fetchAll(PDO::FETCH_ASSOC);
    foreach($model_files as $m_id => $md){
		
		$dir = '../'.$md['file_path'];

		if(file_exists($dir.'/'.$md['filename'])){
			unlink($dir.'/'.$md['filename']);
		}

		if(count(glob("$dir/*")) === 0){
			rmdir($dir);
		}
	}
    
    $delete_temp_files = $pdo->prepare("DELETE FROM temp_modal_files_upload WHERE prod_id=?");
	$delete_temp_files->execute(array($_REQUEST['id']));


	// Delete from tbl_photo
	$statement = $pdo->prepare("UPDATE tbl_product SET is_delete = 1 WHERE p_id=?");
	$statement->execute(array($_REQUEST['id']));

	// Delete from tbl_product_photo
	$statement = $pdo->prepare("DELETE FROM tbl_product_photo WHERE p_id=?");
	$statement->execute(array($_REQUEST['id']));


	// Delete from tbl_rating
	$statement = $pdo->prepare("DELETE FROM tbl_rating WHERE p_id=?");
	$statement->execute(array($_REQUEST['id']));

	
	header('location: product.php');
?>