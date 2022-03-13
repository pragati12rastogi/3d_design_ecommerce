<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

	if(empty($_POST['file_title'])) {
        $valid = 0;
        $error_message .= "File title can not be empty<br>";
    }

    $path = $_FILES['file']['name'];
    $path_tmp = $_FILES['file']['tmp_name'];

    if($path != '') {
    	$ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='JPG' && $ext!='jpeg' && $ext!='JPEG' && $ext!='png' && $ext!='PNG' && $ext!='gif' && $ext!='GIF' && $ext!='pdf' && $ext!='PDF' && $ext!='doc' && $ext!='DOC' && $ext!='docx' && $ext!='DOCX' && $ext!='ppt' && $ext!='PPT' && $ext!='pptx' && $ext!='PPTX' && $ext!='xls' && $ext!='XLS' && $ext!='xlsx' && $ext!='XLSX' ) {
            $valid = 0;
            $error_message .= 'Only these files are allowed: jpg, jpeg, gif, png, pdf, doc, docx, ppt, pptx, xls, xlsx<br>';
        }
    }

    if($valid == 1) {

    	if($path == '') {
    		// updating into the database
			$statement = $pdo->prepare("UPDATE tbl_file SET file_title=? WHERE file_id=?");
			$statement->execute(array($_POST['file_title'],$_REQUEST['id']));
    	} else {
    		unlink('../public_files/uploads/'.$_POST['previous_file']);

    		$final_name = 'file-'.$_REQUEST['id'].'.'.$ext;
        	move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );

        	// updating into the database
			$statement = $pdo->prepare("UPDATE tbl_file SET file_title=?, file_name=? WHERE file_id=?");
			$statement->execute(array($_POST['file_name'],$final_name,$_REQUEST['id']));
    	}
    	
    	$success_message = 'File is updated successfully.';
    }
}
?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM tbl_file WHERE file_id=?");
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
		<h1>Edit File</h1>
	</div>
	<div class="content-header-right">
		<a href="file.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>

<?php							
foreach ($result as $row) {
	$file_title = $row['file_title'];
	$file_name = $row['file_name'];
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
							<label for="" class="col-sm-2 control-label">File Title <span>*</span></label>
							<div class="col-sm-4">
								<input type="text" class="form-control" name="file_title" value="<?php echo $file_title; ?>">
							</div>
						</div>
						<div class="form-group">
				            <label for="" class="col-sm-2 control-label">Existing File Name</label>
				            <div class="col-sm-6" style="padding-top:6px;">
				            	<?php
		                    		echo $file_name;
		                    	?>
				            </div>
				        </div>
						<div class="form-group">
				            <label for="" class="col-sm-2 control-label">Existing File Preview</label>
				            <div class="col-sm-6" style="padding-top:6px;">
				            	<?php
    	                    		$t = explode('.',$file_name);
                                    if( $t[1] == 'jpg' || $t[1] == 'JPG' || $t[1] == 'jpeg' || $t[1] == 'JPEG' || $t[1] == 'png' || $t[1] == 'PNG' || $t[1] == 'gif' || $t[1] == 'GIF' )
                                    {
                                        ?>
                                        <img src="../public_files/uploads/<?php echo $row['file_name']; ?>" alt="" style="width:200px;">
                                        <?php
                                    }

                                    elseif( $t[1] == 'doc' || $t[1] == 'DOC' || $t[1] == 'docx' || $t[1] == 'DOCX' )
                                    {
                                        ?>
                                        <img src="../public_files/img/doc.png" alt="" style="width:80px;">
                                        <?php   
                                    }

                                    elseif( $t[1] == 'ppt' || $t[1] == 'PPT' || $t[1] == 'pptx' || $t[1] == 'PPTX' )
                                    {
                                        ?>
                                        <img src="../public_files/img/ppt.png" alt="" style="width:80px;">
                                        <?php   
                                    }

                                    elseif( $t[1] == 'xls' || $t[1] == 'XLS' || $t[1] == 'xlsx' || $t[1] == 'XLSX' )
                                    {
                                        ?>
                                        <img src="../public_files/img/xls.png" alt="" style="width:80px;">
                                        <?php   
                                    }

                                    elseif( $t[1] == 'pdf' || $t[1] == 'PDF' )
                                    {
                                        ?>
                                        <img src="../public_files/img/pdf.png" alt="" style="width:80px;">
                                        <?php   
                                    }
    	                    	?>
				                <input type="hidden" name="previous_file" value="<?php echo $file_name; ?>">
				            </div>
				        </div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Upload New File <span>*</span></label>
							<div class="col-sm-4" style="padding-top:6px;">
								<input type="file" name="file">
								(Only these files are allowed: jpg, jpeg, gif, png, pdf, doc, docx, ppt, pptx, xls, xlsx)
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label"></label>
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