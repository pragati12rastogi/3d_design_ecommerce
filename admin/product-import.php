<?php require_once('header.php'); ?>

<?php 
	
	if(isset($_POST['sub_cat']))
	{
    	$csv = array();

		// check there are no errors
		if($_FILES['csv']['error'] == 0){
			
			$name = $_FILES['csv']['name'];
			
			$exp = explode('.',$name);
			$get_ext = end($exp);
			$ext = strtolower($get_ext);
			
			$type = $_FILES['csv']['type'];
			$tmpName = $_FILES['csv']['tmp_name'];
			$error ='';
			$err = 1;
			
			// check the file is a csv
			if($ext === 'csv'){
				if(($handle = fopen($tmpName, 'r')) !== FALSE) {
					// necessary if a large csv file
					set_time_limit(0);

					ini_set('memory_limit', '-1');
					ini_set('max_execution_time',1000);

					$row = 0;
					
					fgets($handle);

					$pdo->begintransaction();
					
					while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
						// number of fields in the csv
						$col_count = count($data);
				
						date_default_timezone_set('Asia/Kolkata');
						$timestamp = date("Y-m-d");
						// get the values from the csv
						
						$model_name = $data[0]; //required

						$isfree = $data[1]; //optional
						$sku = $data[2]; 	//optional
						$old_price = $data[3]; //optional
						$actual_price = $data[4]; //required

						$cat_name = $data[5]; //required
						$sub_name = $data[6]; //optional
						$youtube = $data[7]; //optional
						$vimeo = $data[8];   //optional
						$description = $data[9]; //required
						$tags = $data[10]; //required
						
						$license = $data[11]; //required
						$custom_license = $data[12];	//optional
						$featured_photo = trim($data[13],' ');    //required
						$product_model_file = trim($data[14],' '); //required
						$other_images = trim($data[15],' ');   // optional

						$archive_file_ext = [];

						$err++;
						$cat_sql = $pdo->prepare('select * from tbl_top_category where tcat_name="'.$cat_name.'"');
						$cat_sql->execute();
						$cat = $cat_sql->fetch(PDO::FETCH_ASSOC);
						
						if(empty($cat)){
							$error .=" Wrong Category at row ".$err.",";
						}
						$subcat =[];
						if(!empty(trim($sub_name))){
							$subcat_sql = $pdo->prepare('select * from tbl_mid_category where mcat_name="'.$sub_name.'" and tcat_id='.$cat['tcat_id']);
							$subcat_sql->execute();
							$subcat = $subcat_sql->fetch(PDO::FETCH_ASSOC);
						}
						if(!empty($subcat)){
							$subcat['mcat_id'] = $subcat['mcat_id'];
						}else{
							$subcat['mcat_id'] = 0;
						}

						if(empty($actual_price)){
							$error .="Actual Price field is empty at row ".$err.",";
						}

						if(empty($description)){
							$error .="Description field is empty at row ".$err.",";
						}

						if(empty($tags)){
							$error .="Tags field is empty at row ".$err.",";
						}

						if(empty($license)){
							$error .="License field is empty at row ".$err.",";
						}

						// $local_path = 'C:\xampp\htdocs\core-php-projects\3d-design-sale';
						$local_path = 'C:\xampp\htdocs';
						if(empty($featured_photo)){
							$error .="Feature photo field is empty at row ".$err.",";
						}
						else{
							$featured_photoPath = trim($featured_photo);
							$extention = pathinfo($featured_photoPath, PATHINFO_EXTENSION);
							
							if( $extention!='JPG' && $extention!='jpg' && $extention!='jpeg' && $extention!='png' && $extention!='bmp' ) {
								$error .="Feature Photo extention not correct at row ".$err.'-'.$extention.",";
							}
						}

						if(empty($product_model_file)){
							$error .="Product model field is empty at row ".$err.",";
						}
						else{
							$explode_product_model_file = explode("&;",$product_model_file);
							
							foreach($explode_product_model_file as $ind => $model_file){
								
								$model_filePath = $model_file;
								$extention = pathinfo($model_filePath, PATHINFO_EXTENSION);
								

								if( $extention!='3dm' && $extention!='jcd' && $extention!='stl' ) {
									$error .="Product Model extention not correct at row ".$err .'-'.$extention.",";
								}else{
									$archive_file_ext[] = $extention;
								}

							}
						}
						
						if(empty(trim($other_images))){
							$error .="Product other image field is empty at row ".$err.",";
						}else{
							$explode_other_image = explode("&;",trim($other_images));

							foreach($explode_other_image as $ind => $other_image){
								
								$other_imagePath = $other_image;
								$extention = pathinfo($other_imagePath, PATHINFO_EXTENSION);
								
								if( $extention!='JPG' && $extention!='jpg' && $extention!='jpeg' && $extention!='png' && $extention!='bmp' ) {
									$error .="Other Image extention not correct at row ".$err.' - '.$extention .",";
								}

							}
						} 
						

						if($error == ''){
							
							$statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_product'");
							$statement->execute();
							$result = $statement->fetchAll();
							foreach($result as $row) {
								$ai_id=$row[10];
							}

							$model_path = 'public_files/uploads/temp_model_files/'.$model_name.time();
							$admin_model_path = '../'.$model_path;
							if(!is_dir($admin_model_path)){
								mkdir($admin_model_path,0777,true);
							}

							$explode_product_model_file = explode("&;",trim($product_model_file));
							
							// product model upload
							foreach($explode_product_model_file as $ind => $model_file){

								$filePath = $local_path.$model_file;
								$time = time();

								$extention = pathinfo($model_file, PATHINFO_EXTENSION);
								$prodmodelfile_name = basename( $model_file, '.'.$extention );

								$modelfilename = $prodmodelfile_name.'_'.$time.'.'.$extention;

								$prod_array[] = $modelfilename;

								$destinationFilePath = $admin_model_path.'/'.$modelfilename;

								
								if( !copy($filePath, $destinationFilePath) ) {  
									$error .= "File can't be copied!".$err; 

									array_map('unlink', array_filter(
										(array) array_merge(glob($admin_model_path."/*"))));
									rmdir($admin_model_path);
									exit(); 
								}

								$statement = $pdo->prepare("INSERT INTO temp_modal_files_upload (`prod_id`,`file_path`,`filename`) VALUES (?,?,?)");
                				$statement->execute(array($ai_id,$model_path,$modelfilename));
								
							}

							if(!empty(trim($other_images))){
								$statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_product_photo'");
								$statement->execute();
								$result = $statement->fetchAll();
								foreach($result as $row) {
									$next_id1=$row[10];
								}
								$z = $next_id1;

								$explode_other_image = explode("&;",trim($other_images));

								foreach($explode_other_image as $ind => $other_image){
									
									$other_imagePath = $local_path.$other_image;
									$extention = pathinfo($other_image, PATHINFO_EXTENSION);
									$prodmodelfile_name = basename( $other_image, '.'.$extention );

									$final_name1 = $prodmodelfile_name.$z.'.'.$extention;
									$other_img_destination_path = '../public_files/uploads/product_photos/'.$final_name1;
                					
									
									if( !copy($other_imagePath, $other_img_destination_path) ) {  
										$error .= "Others Photos can't be copied!".$err."-".$other_image; 

										array_map('unlink', array_filter(
											(array) array_merge(glob($admin_model_path."/*"))));
										rmdir($admin_model_path);
										  
									}

									$z++;

									$statement = $pdo->prepare("INSERT INTO tbl_product_photo (photo,p_id) VALUES (?,?)");
                					$statement->execute(array($final_name1,$ai_id));
								}

							}
							
							// featured image
							$featured_photoPath = $local_path.$featured_photo;
							$featured_ext = pathinfo($featured_photoPath, PATHINFO_EXTENSION);
							$featuredfile_name = basename( $featured_photo, '.'.$featured_ext );

							$feature_photo_name = 'product-featured-'.$ai_id.'.'.$featured_ext;
							$feature_photo_destination_path = '../public_files/uploads/feature-photo/'.$feature_photo_name;
                					
							if( !copy($featured_photoPath, $feature_photo_destination_path) ) {  
								$error .= "Featured Photos can't be copied!".$err; 
								array_map('unlink', array_filter(
									(array) array_merge(glob($admin_model_path."/*"))));
								rmdir($admin_model_path);
								
							}
							
							if($error == ''){
								
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
									$model_name,
									$sku,
									$old_price,
									$actual_price,
									$cat['tcat_id'],
									$subcat['mcat_id'],
									'',
									implode(',',$archive_file_ext),
									$feature_photo_name,
									$youtube,
									$vimeo,
									$description,
									$tags,
									
									$license,
									$custom_license,
									0,
									0,
									1,
									'Admin',
									$_SESSION['user']['id'],
									$isfree
								));
							}

							
						}
						// inc the row
						$row++;
					}
					
					if($error == ''){
						$pdo->commit();
					}
					fclose($handle);
				}
				?>
					<script type="text/javascript">

						var error = <?php echo json_encode($error); ?>;
						$(document).ready(function() {

							$("#alert-success").empty();
							$("#alert-danger").empty();
							if(error == ''){

								$msg = '<p>Product Imported Successfully</p>';

								$("#alert-success").append($msg).show();
            
            					setTimeout(function(){ $("#alert-success").hide(); }, 10000);

							}else{
								$("#alert-danger").append(error).show();
								
								
							}

						});
					</script>
				<?php 
			}

		}
  	}

?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Import Products</h1>
	</div>
	<div class="content-header-right">
		<a href="product.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>

<div id="preloader" style="display:none" >
	<div id="status" style="display:none" ></div>
</div>
<section class="content">
	<div class="alert alert-danger" id="alert-danger" style="display:none">
					
	</div>
	
	<div class="alert alert-success" id="alert-success" style="display:none">
		
	</div>

	<form class="form" method="post" enctype="multipart/form-data">
		<div class="box box-info">
			<div class="box-body">
				<div class="col-md-12">
					<div class="form-body">
						
						<div class="form-group">
							<label>Select a Csv :</label>
							<input type="file" name="csv" class="form-control-file" id="prod_file">
						</div>

					</div>

					<div class="form-actions">
						<button type="submit" name="sub_cat" class="btn btn-raised btn-raised btn-primary">
							<i class="fa fa-check-square-o"></i> Upload Csv
						</button>
						
						<a href="import/3DdesignProductExcelFormat.csv" target="_blank" class="btn btn-raised btn-raised btn-info" id="download" >Demo Csv</a>
					</div>
				</div>
			</div>
		</div>
	</form>

</section>

<?php require_once('footer.php'); ?>