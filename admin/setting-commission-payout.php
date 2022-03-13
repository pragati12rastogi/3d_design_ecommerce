<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
    $valid = 1;
    $active = empty($_POST['active'])?0:1;

    if($active){

        if(empty($_POST['setting_type'])){
            $valid = 0;
            $error_message .= 'Please select setting type. <br>';
        }

        if(empty($_POST['setting_value'])){
            $valid = 0;
            $error_message .= 'Please select setting value. <br>';
        }
    }

    $statement = $pdo->prepare("UPDATE tbl_setting_commission SET setting_type=?,setting_value=? WHERE sc_id=1");
    $statement->execute(array($_POST['setting_type'],$_POST['setting_value']));

    $success_message = 'Commission setting is updated successfully.';
    
}

if(isset($_POST['form2'])) {
    

    $statement = $pdo->prepare("UPDATE tbl_setting_commission SET payout_date=? WHERE sc_id=1");
    $statement->execute(array($_POST['payout_date']));

    $success_message = 'Payout setting is updated successfully.';
    
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Setting - Commission & Payout</h1>
    </div>
</section>

<?php

$getcommission_sql = $pdo->prepare("SELECT * FROM tbl_setting_commission WHERE sc_id=1");
$getcommission_sql->execute();
$getcommission = $getcommission_sql->fetch(PDO::FETCH_ASSOC);                           

?>


<section class="content" style="min-height:auto;margin-bottom: -30px;">
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
        </div>
    </div>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="content-header-left">
                <h3>Commission </h3>
            </div>
            <form class="form-horizontal" action="" method="post">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Select Commission Type</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="setting_type" id="setting_type">
                                    <option value="">Select commission type</option>
                                    <option value="fixed" <?php echo $getcommission['setting_type']=="fixed"?"selected":"" ; ?> >Fixed</option>
                                    <option value="percent" <?php echo $getcommission['setting_type']=="percent"?"selected":"" ; ?> >Percentage</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Commission Value</label>
                            <div class="col-sm-4">
                                <input type="number" min="0" class="form-control" id="setting_value" name="setting_value" value="<?php echo $getcommission['setting_value']; ?>">
                                    
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-4">
                                <input type="checkbox" class="" name="active" value="1" <?php echo $getcommission['active']=="1"?'checked':''; ?> >
                                    
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label"></label>
                            <div class="col-sm-5">
                                <button type="submit" class="btn btn-success pull-left" name="form1">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
                
        </div>
        
        <div class="col-md-12">
            <div class="content-header-left">
                <h3>Payout </h3>
            </div>
            <form class="form-horizontal" action="" method="post">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Enter Payout Day <br> <small>(This will reflect in front panel to users)</small></label>
                            <div class="col-sm-4">
                                <input type="number"  min="0" step="1.0" class="form-control" name="payout_date" value="<?php echo $getcommission['payout_date']; ?>" max="30">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label"></label>
                            <div class="col-sm-5">
                                <button type="submit" class="btn btn-success pull-left" name="form2">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        
    </div>

</section>

<script>
    $("#setting_type").trigger('change');
    $("#setting_type").change(function(){
        var setting_value = $("#setting_type").val();

        if(setting_value == 'percent'){
            $("#setting_value").attr('max',100);
        }else{
            $("#setting_value").attr('max',false);
        }
    })
</script>
<?php require_once('footer.php'); ?>