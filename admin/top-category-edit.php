<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

    $path = $_FILES['tcat_photo']['name'];
    $path_tmp = $_FILES['tcat_photo']['tmp_name'];

    if($path != '') {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
            
        }
    }

    if(empty($_POST['tcat_name'])) {
        $valid = 0;
        $error_message .= "Top Category Name can not be empty<br>";
        
    } else {
		// Duplicate Top Category checking
    	// current Top Category name that is in the database
    	$statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE tcat_id=?");
		$statement->execute(array($_REQUEST['id']));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach($result as $row) {
			$current_tcat_name = $row['tcat_name'];
            $current_tcat_photo = $row['tcat_photo'];
		}
        
		$statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE tcat_name=? and tcat_name!=?");
    	$statement->execute(array($_POST['tcat_name'],$current_tcat_name));
    	$total = $statement->rowCount();							
    	if($total) {
    		$valid = 0;
        	$error_message .= 'Top Category name already exists<br>';
            
    	}else{
            if($path != '') {
                if(isset($current_tcat_photo) && file_exists('../public_files/uploads/category_photo/'.$current_tcat_photo)) {
                    unlink('../public_files/uploads/category_photo/'.$current_tcat_photo);    
                }
                $final_name = $_POST['tcat_name'].'_'.time().'.'.$ext;
                move_uploaded_file( $path_tmp, '../public_files/uploads/category_photo/'.$final_name );
            }else{
                $final_name = $current_tcat_photo;
            }
        }

        
         
    }

    if($valid == 1) {    	
		// updating into the database
		$statement = $pdo->prepare("UPDATE tbl_top_category SET tcat_name=?,show_on_menu=?,tcat_photo=? WHERE tcat_id=?");
		$statement->execute(array($_POST['tcat_name'],$_POST['show_on_menu'],$final_name,$_REQUEST['id']));

    	$success_message = 'Top Category is updated successfully.';
    }
}
?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE tcat_id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	}
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Edit Category</h1>
	</div>
	<div class="content-header-right">
		<a href="top-category.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>


<?php							
foreach ($result as $row) {
	$tcat_name = $row['tcat_name'];
    $tcat_photo = $row['tcat_photo'];
    $show_on_menu = $row['show_on_menu'];
}
?>

<section class="content">

  <div class="row">
    <div class="col-md-12">

		<?php if($error_message): ?>
		<div class="callout callout-danger">

		<p>
		<?php echo $error_message; ?>
		</p>
		</div>
		<?php endif; ?>

		<?php if($success_message): ?>
		<div class="callout callout-success">
		
		<p><?php echo $success_message; ?></p>
		</div>
		<?php endif; ?>

        <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">

        <div class="box box-info">

            <div class="box-body">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Category Name <span>*</span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="tcat_name" value="<?php echo $tcat_name; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Category photo </label>
                    <div class="col-sm-4">
                        <input type="file" accept="image/*" name="tcat_photo" >
                    </div>
                </div>
                <?php if(!empty($tcat_photo) && file_exists('../public_files/uploads/category_photo/'.$tcat_photo)){ ?>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Uploaded Image: </label>
                    <div class="col-sm-4">
                        <img src='../public_files/uploads/category_photo/<?php echo $tcat_photo; ?>' alt="category image" width="50%">
                    </div>
                </div>
                <?php } ?>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Show on Menu? <span>*</span></label>
                    <div class="col-sm-4">
                        <select name="show_on_menu" class="form-control" style="width:auto;">
                            <option value="0" <?php if($show_on_menu == 0) {echo 'selected';} ?>>No</option>
                            <option value="1" <?php if($show_on_menu == 1) {echo 'selected';} ?>>Yes</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                	<label for="" class="col-sm-2 control-label"></label>
                    <div class="col-sm-6">
                      <button type="submit" class="btn btn-success pull-left" name="form1">Update</button>
                    </div>
                </div>

            </div>

        </div>

        </form>



    </div>
  </div>

</section>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                Are you sure want to delete this item?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>