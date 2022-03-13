<?php require_once('header.php'); ?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	} else {
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
		foreach ($result as $row) {
			$payment_id = $row['payment_id'];
			$payment_status = $row['payment_status'];
			
		}
	}
}
?>

<?php
	
	// Delete from tbl_order
	$statement = $pdo->prepare("DELETE FROM tbl_order WHERE payment_id=?");
	$statement->execute(array($payment_id));

	// Delete from tbl_payment
	$statement = $pdo->prepare("DELETE FROM tbl_payment WHERE id=?");
	$statement->execute(array($_REQUEST['id']));

	header('location: order.php');
?>