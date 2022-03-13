<?php require_once('header.php'); ?>
<?php
// Check if the customer is logged in or not
if(!isset($_SESSION['customer'])) {
    header('location: '.BASE_URL.'logout.php');
    exit;
} else {
    // If customer is logged in, but admin make him inactive, then force logout this user.
    $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_id=? AND cust_status=?");
    $statement->execute(array($_SESSION['customer']['cust_id'],0));
    $total = $statement->rowCount();
    if($total) {
        header('location: '.BASE_URL.'logout.php');
        exit;
    }

    $setting_currency = $pdo->prepare("SELECT * FROM tbl_setting_currency WHERE default_currency=1");
    $setting_currency->execute();
    $default_currency = $setting_currency->fetch(PDO::FETCH_ASSOC);
    $currency_sign = $default_currency['currency_symbol'];
}
?>


<div class="page">
    <div class="container">
        <div class="row">            
            <div class="col-md-12"> 
                <?php require_once('customer-sidebar.php'); ?>
            </div>
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-body table-responsive">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="30">SL</th>
                                    <th>Image</th>
                                    <th width="200"> Name</th>
                                    <th>Date Added</th>
                                    <th>Date Modified</th>
                                    <th width="60">Current Price</th>
                                    <th width="60">Views</th>
                                    <th>Status</th>
                                    <th width="150">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i=0;
                                $statement = $pdo->prepare("SELECT
                                                            
                                                            t1.*,
                                                            
                                                            t2.tcat_name,
                                                            t2.tcat_id,
                                                            
                                                            t3.mcat_id,
                                                            t3.mcat_name

                                                            FROM tbl_product t1

                                                            left JOIN tbl_top_category t2
                                                            ON t1.cat_id = t2.tcat_id

                                                            left JOIN tbl_mid_category t3
                                                            ON t1.subcat_id = t3.mcat_id

                                                            WHERE t1.user_id = ? AND t1.user_type = 'Customer' and is_delete = 0
                                                            ORDER BY t1.p_id DESC
                                                            ");
                                $statement->execute([$_SESSION['customer']['cust_id']]);
                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $row) {
                                    $i++;
                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td style="width:130px;"><img src="public_files/uploads/<?php echo $row['p_featured_photo']; ?>" alt="<?php echo $row['p_name']; ?>" style="width:100px;"></td>
                                        
                                        <td><?php echo $row['p_name']; ?></td>
                                        <td><?php echo date("d-M-Y h:i A",strtotime($row['created_at'])); ?></td>
                                        <td><?php echo date("d-M-Y h:i A",strtotime($row['updated_at'])); ?></td>
                                        <td><?php echo ($row['is_free']== 0)?($row['p_current_price']):'Free'; ?></td>

                                        <td>
                                            <?php echo $row['p_total_view']; ?>
                                        </td>
                                        <td>
                                            <?php if($row['p_is_active'] == 1) {echo 'Yes';} else {echo 'No';} ?>
                                        </td>
                                        <td>										
                                            <a href="my-model-edit.php?id=<?php echo $row['p_id']; ?>" class="btn btn-primary btn-xs">Edit</a>
                                            <a href="#" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#confirm-delete">Delete</a>  
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
    </div>
</div>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure want to delete this item?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a href="my-model-delete.php?id=<?php echo $row['p_id']; ?>" class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>