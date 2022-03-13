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
        $error_message .= "Mid Level Category Name can not be empty<br>";
    }else{
		$check_exist = $pdo->prepare("SELECT * FROM tbl_mid_category where mcat_name = '".$_POST['mcat_name']."' and tcat_id =".$_POST['tcat_id']);
        $check_exist->execute();
        $check_exist= $check_exist->fetchAll(PDO::FETCH_ASSOC);
		
        if(count($check_exist)>0){
            $valid = 0;
            $error_message .= "Sub Category Name already present for same category<br>";
            
        }else{
			$final_name ='';
            if($path != '') {
                
                $final_name = $_POST['mcat_name'].'_'.time().'.'.$ext;
                move_uploaded_file( $path_tmp, '../public_files/uploads/subcategory_photo/'.$final_name );
            }
        }
	}

    if($valid == 1) {

		// Saving data into the main table tbl_mid_category
		$statement = $pdo->prepare("INSERT INTO tbl_mid_category (mcat_name,tcat_id,mcat_photo) VALUES (?,?,?)");
		$statement->execute(array($_POST['mcat_name'],$_POST['tcat_id'],$final_name));
	
    	$success_message = 'Mid Level Category is added successfully.';
    }
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Add Mid Level Category</h1>
	</div>
	<div class="content-header-right">
		<a href="mid-category.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>


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
							<label for="" class="col-sm-3 control-label">Top Level Category Name <span>*</span></label>
							<div class="col-sm-4">
								<select name="tcat_id" class="form-control select2">
									<option value="">Select Top Level Category</option>
									<?php
									$statement = $pdo->prepare("SELECT * FROM tbl_top_category ORDER BY tcat_name ASC");
									$statement->execute();
									$result = $statement->fetchAll(PDO::FETCH_ASSOC);	
									foreach ($result as $row) {
										?>
										<option value="<?php echo $row['tcat_id']; ?>"><?php echo $row['tcat_name']; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Sub Category Name <span>*</span></label>
							<div class="col-sm-4">
								<input type="text" class="form-control" name="mcat_name">
							</div>
						</div>
						
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Sub Category Photo </label>
							<div class="col-sm-4">
								<input type="file" accept="image/*" name="mcat_photo" >
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Submit</button>
							</div>
						</div>
					</div>
				</div>

			</form>


		</div>
	</div>

</section>

<?php require_once('footer.php'); ?>