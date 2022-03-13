<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

	if(empty($_POST['sale_type'])) {
		$valid = 0;
		$error_message .= 'Sale Type can not be empty<br>';
	}else{
		if($_POST['sale_type'] == 'special_sale'){
			if(empty($_POST['sale_name'])){
				$valid = 0;
				$error_message .= 'If sale type is super sale then sale name can not be empty<br>';
			}
		}
	}

	if(empty($_POST['startDate'])){
		$valid = 0;
		$error_message .= 'Start date can not be empty<br>';
	}

	if(empty($_POST['endDate'])){
		$valid = 0;
		$error_message .= 'End date can not be empty<br>';
	}

	if($valid == 1) {

		
	
		$statement = $pdo->prepare("INSERT INTO tbl_sale_period (sale_name,sale_type,sale_start_time,sale_end_time) VALUES (?,?,?,?)");
		$statement->execute(array($_POST['sale_name'],$_POST['sale_type'],$_POST['startDate'],$_POST['endDate']));
			
		$success_message = 'Sale is added successfully!';

		
	}
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Add Sale</h1>
	</div>
	<div class="content-header-right">
		<a href="sale.php" class="btn btn-primary btn-sm">View All</a>
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
						<div class="form-group ">
							<label for="" class="col-sm-2 control-label">Sale Type <span>*</span></label>
							<div class="col-sm-6">
								<select name="sale_type" class="form-control" id="salepage-saletype">
                                    <option value="">Select sale type</option>
                                    <option value="sale">Sale</option>
                                    <option value="special_sale">Special Sale</option>
                                </select>
							</div>
						</div>
                        <div class="form-group " id="sale_name_div" style="display:none;">
							<label for="" class="col-sm-2 control-label">Sale Name <span>*</span></label>
							<div class="col-sm-6">
								<input type="text" maxlength="250" name="sale_name" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Duration <span>*</span></label>
							<div class="col-sm-6">
								<div class="input-group input-daterange ">
									<input autocomplete="off" name="startDate" onblur="date_range_filter()" type="text" id="min-date" class="form-control datetimepicker date-range-filter"  placeholder="From:">
									<div class="input-group-addon">to</div>
									<input autocomplete="off" name="endDate"  type="text" id="max-date" class="form-control datetimepicker date-range-filter" placeholder="To:">
								</div>
							</div>
						</div>
						<br>
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