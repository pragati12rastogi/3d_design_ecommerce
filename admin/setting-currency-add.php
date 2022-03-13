<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1']))
{  
    $pdo->begintransaction();
    $is_default = isset($_POST['default_currency']) ? $_POST['default_currency'] : 0;

    $check_default = $pdo->prepare("SELECT * FROM tbl_setting_currency WHERE default_currency=1");
    $check_default->execute();
    $default_arr = $check_default->fetchAll(PDO::FETCH_ASSOC);  

    if($is_default){
        // update all to default to 0
        foreach($default_arr as $ind => $arr){
            $statement = $pdo->prepare("UPDATE tbl_setting_currency SET default_currency=0 WHERE id=".$arr['id']);
            $statement->execute();
        }

    }else{
        if(count($default_arr)<0){
            $pdo->rollback();
            $error_message = 'Please mark any currency as default before proceeding.';
            exit();
        }
    }
    // insert the database
    $statement = $pdo->prepare("Insert into tbl_setting_currency (currency_code, currency_symbol, currency_position, currency_value_per_usd, default_currency) VALUE(?,?,?,?,?) ");
    $statement->execute(array($_POST['currency_code'],$_POST['currency_symbol'],$_POST['currency_position'], $_POST['currency_value_per_usd'],$is_default));

    if($pdo->lastInsertId()>0){
        $success_message = 'Currency setting is inserted successfully.';
        $pdo->commit();
    }else{
        $error_message = 'Currency not inserted, please try again.';
        $pdo->rollback();
    }
    
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Setting - Edit Currency</h1>
    </div>
    <div class="content-header-right">
		<a href="setting-currency-list.php" class="btn btn-primary btn-sm"> Currency List</a>
	</div>
</section>



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

            <form class="form-horizontal" action="" method="post">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Currency Code *</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="currency_code" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Currency Symbol *</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="currency_symbol" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Currency Position *</label>
                            <div class="col-sm-2">
                                <select name="currency_position" class="form-control">
                                    <option value="Before" >Before</option>
                                    <option value="After" >After</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Currency Value per <?php echo ADMIN_CURRENCY_CODE; ?> *</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="currency_value_per_usd" value="">
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Default Currency</label>
                            <div class="col-sm-2">
                                <input type="checkbox" class=" checkbox_design" name="default_currency" value="1"  >
                            </div>
                        </div> -->
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label"></label>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-success pull-left" name="form1">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
                
        </div>
    </div>

</section>

<style>
    
    .checkbox_design{
        height: 25px;
        width: 14%;
    }

</style>
<?php require_once('footer.php'); ?>