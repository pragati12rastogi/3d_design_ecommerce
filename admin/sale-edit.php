<?php require_once('header.php'); ?>

<?php

if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM tbl_sale_period WHERE sp_id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	if( empty($total) ) {
		header('location: logout.php');
		exit;
	}
}

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

		
		$statement = $pdo->prepare("UPDATE tbl_sale_period SET sale_name =?, sale_type=? ,sale_start_time=?,sale_end_time=? where sp_id = ?");
		$statement->execute(array($_POST['sale_name'],$_POST['sale_type'],$_POST['startDate'],$_POST['endDate'],$_REQUEST['id']));
			
		$success_message = 'Sale is Updated successfully!';

		$statement = $pdo->prepare("SELECT * FROM tbl_sale_period WHERE sp_id=?");
		$statement->execute(array($_REQUEST['id']));
		
		$result = $statement->fetch(PDO::FETCH_ASSOC);
	}
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Edit Sale</h1>
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
                                    <option value="" <?php echo( empty($result['sale_type'])?'selected':''); ?> >Select sale type</option>
                                    <option value="sale" <?php echo( ($result['sale_type'] == 'sale')?'selected':''); ?> >Sale</option>
                                    <option value="special_sale" <?php echo (($result['sale_type'] == 'special_sale')?'selected':''); ?> >Special Sale</option>
                                </select>
							</div>
						</div>
                        <div class="form-group " id="sale_name_div" style="<?php echo (($result['sale_type'] == 'sale')?'display:none':''); ?>">
							<label for="" class="col-sm-2 control-label">Sale Name <span>*</span></label>
							<div class="col-sm-6">
								<input type="text" maxlength="250" value="<?php echo $result['sale_name'] ; ?>" name="sale_name" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Duration </label>
							<div class="col-sm-6">
								<div class="input-group input-daterange ">
									<input autocomplete="off" name="startDate" type="text" id="min-date" class="form-control edit-date-range"  placeholder="From:" value="<?php echo $result['sale_start_time']?>" onblur = "check_date_validation()">
									<div class="input-group-addon">to</div>
									<input autocomplete="off" value="<?php echo $result['sale_end_time']?>" name="endDate" type="text" id="max-date" class="form-control edit-date-range" onblur="check_date_validation()"  placeholder="To:">
								</div>
								<label class="error" id="saleduration-error"></label>
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
<script>
	$(document).ready(function(){
		$(".edit-date-range").datetimepicker({
			format:'YYYY-MM-DD'
		})
	})

	function check_date_validation(){
		var min_date = moment($("#min-date").val());
		var max_date = moment($("#max-date").val());
		$("#saleduration-error").text('');
		if(min_date > max_date){

			$("#saleduration-error").text('End Date can not be lesser than Start Date');
			setTimeout(function() {
				$("#saleduration-error").text('');
            }, 10000);
			$("#max-date").val('');
		}
	}

</script>
<?php require_once('footer.php'); ?>