<?php require_once('header.php'); ?>

<section class="content-header">
	<div class="content-header-left">
		<h1>View Products</h1>
	</div>
	<div class="content-header-right">
		<a href="product-add.php" class="btn btn-primary btn-sm">Add Product</a>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-info">
				<div class="box-body table-responsive">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th width="30">SL</th>
								<th>Photo</th>
								<th>Category</th>
								<th width="200">Product Name</th>
								<th width="60">Old Price</th>
								<th width="60">Current Price</th>
								<th width="60">Tags</th>
								<th >Created By</th>
								<th>Is Featured?</th>
								<th>Is Active?</th>
								<th width="80">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i=0;
							$statement = $pdo->prepare("SELECT
														
														t1.p_id,
														t1.p_name,
														t1.p_old_price,
														t1.p_current_price,
														t1.p_featured_photo,
														t1.p_is_featured,
														t1.p_is_active,
														t1.p_tags,
														t1.is_free,
														t1.cat_id,
														t1.subcat_id,
														
														t2.tcat_name,
														t2.tcat_id,
														
														t3.mcat_id,
														t3.mcat_name,

														t4.cust_email

							                           	FROM tbl_product t1

							                           	left JOIN tbl_top_category t2
							                           	ON t1.cat_id = t2.tcat_id

							                           	left JOIN tbl_mid_category t3
							                           	ON t1.subcat_id = t3.mcat_id
														
														left join tbl_customer t4 
														on t1.user_type ='Customer' and t1.user_id = t4.cust_id
														
														where t1.is_delete =0
							                           	ORDER BY t1.p_id DESC
							                           	");
							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);
							foreach ($result as $row) {
								$i++;
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td style="width:130px;"><img src="../public_files/uploads/<?php echo $row['p_featured_photo']; ?>" alt="<?php echo $row['p_name']; ?>" style="width:100px;"></td>
									<td><?php echo $row['tcat_name']; ?><br><?php echo $row['mcat_name']; ?></td>
									
									<td><?php echo $row['p_name']; ?></td>
									<td><?php echo $row['p_old_price']; ?></td>
									<td><?php echo ($row['is_free']== 0)?($row['p_current_price']):'Free'; ?></td>
									<td><?php echo $row['p_tags']; ?></td>
									<td><?php echo empty($row['cust_email'])?'Admin':$row['cust_email']; ?></td>
									<td>
										<?php if($row['p_is_featured'] == 1) {echo 'Yes';} else {echo 'No';} ?>
									</td>
									<td>
										<?php if($row['p_is_active'] == 1) {echo 'Yes';} else {echo 'No';} ?>
									</td>
									<td>										
										<a href="product-edit.php?id=<?php echo $row['p_id']; ?>" class="btn btn-primary btn-xs">Edit</a>
										<a href="#" class="btn btn-danger btn-xs" data-href="product-delete.php?id=<?php echo $row['p_id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>  
									</td>
								</tr>
								<?php
							}
							?>							
						</tbody>
					</table>
				</div>
			</div>
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
                <p>Are you sure want to delete this item?</p>
                <p style="color:red;">Be careful! This product will be deleted from the order table, payment table and rating table also.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>