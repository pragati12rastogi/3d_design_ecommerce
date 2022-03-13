<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;
    
    $path = $_FILES['mcat_photo']['name'];
    $path_tmp = $_FILES['mcat_photo']['tmp_name'];

    if($path != '') {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
            
        }
    } 
    
    

    if(empty($_POST['tcat_id'])) {
        $valid = 0;
        $error_message .= "You must have to select a top level category<br>";
        
    }

    if(empty($_POST['mcat_name'])) {
        $valid = 0;
        $error_message .= "Sub Category Name can not be empty<br>";
        
    }else{
        $check_exist = $pdo->prepare("SELECT * FROM tbl_mid_category where mcat_name = '".$_POST['mcat_name']."' and mcat_id <>".$_REQUEST['id']." and tcat_id =".$_POST['tcat_id']);
        $check_exist->execute();
        $check_exist = $check_exist->fetchAll(PDO::FETCH_ASSOC);
        if(count($check_exist)>0){
            $valid = 0;
            $error_message .= "Sub Category Name already present for same category<br>";
           
        }else{

            if($path != '') {
                if(isset($_POST['mcat_photo_old']) && file_exists('../public_files/uploads/subcategory_photo/'.$_POST['mcat_photo_old'])) {
                    unlink('../public_files/uploads/subcategory_photo/'.$_POST['mcat_photo_old']);    
                }
                $final_name = $_POST['mcat_name'].'_'.time().'.'.$ext;
                move_uploaded_file( $path_tmp, '../public_files/uploads/subcategory_photo/'.$final_name );
            }else{
                $final_name = $_POST['mcat_photo_old'];
            }
        }


    }

    

    if($valid == 1) {    	
		// updating into the database
		$statement = $pdo->prepare("UPDATE tbl_mid_category SET mcat_name=?,tcat_id=?,mcat_photo=? WHERE mcat_id=?");
		$statement->execute(array($_POST['mcat_name'],$_POST['tcat_id'],$final_name,$_REQUEST['id']));

    	$success_message = 'Mid Level Category is updated successfully.';
    }
}
?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM tbl_mid_category WHERE mcat_id=?");
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
		<h1>Edit Sub Category</h1>
	</div>
	<div class="content-header-right">
		<a href="mid-category.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>


<?php							
foreach ($result as $row) {
	$mcat_name = $row['mcat_name'];
	$mcat_photo = $row['mcat_photo'];
    $tcat_id = $row['tcat_id'];
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
                    <label for="" class="col-sm-3 control-label">Category Name <span>*</span></label>
                    <div class="col-sm-4">
                        <select name="tcat_id" class="form-control select2">
                            <option value="">Select Category</option>
                            <?php
                            $statement = $pdo->prepare("SELECT * FROM tbl_top_category ORDER BY tcat_name ASC");
                            $statement->execute();
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);   
                            foreach ($result as $row) {
                                ?>
                                <option value="<?php echo $row['tcat_id']; ?>" <?php if($row['tcat_id'] == $tcat_id){echo 'selected';} ?>><?php echo $row['tcat_name']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Sub Category Name <span>*</span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="mcat_name" value="<?php echo $mcat_name; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Sub Category Photo </label>
                    <div class="col-sm-4">
                        <input type="file" accept="image/*" name="mcat_photo" >
                        <input type="hidden" name="mcat_photo_old" value="<?php echo $mcat_photo; ?>" >
                    </div>
                </div>
                <?php if(!empty($mcat_photo) && file_exists('../public_files/uploads/subcategory_photo/'.$mcat_photo)){ ?>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Uploaded Image: </label>
                    <div class="col-sm-4">
                        <img src='../public_files/uploads/subcategory_photo/<?php echo $mcat_photo; ?>' alt="sub category image" width="50%">
                    </div>
                </div>
                <?php } ?>
                <div class="form-group">
                	<label for="" class="col-sm-3 control-label"></label>
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