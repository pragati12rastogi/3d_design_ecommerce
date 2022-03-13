<html>
    <head>
        
        
    </head>
    <?php 
    require_once "admin/inc/config.php";
    
    $get_static_lang_sql = $pdo->prepare("SELECT * FROM tbl_language");
    $get_static_lang_sql->execute();
    $get_static_lang = $get_static_lang_sql->fetchAll();							
    foreach ($get_static_lang as $row) {
        define($row['lang_name'],$row['lang_value']);
    }

    if(!isset($_REQUEST['id'])) {
        header('location: index.php');
        exit;
    } else {
        // Check the id is valid or not
        $order_sql = $pdo->prepare("SELECT tbl_order.*,tbl_product.user_type, tbl_customer.cust_name as vendor_cust_name,
        tbl_customer.cust_email as vendor_cust_email,
        tbl_customer.cust_phone as vendor_cust_phone,
        
        tbl_user.full_name as vendor_admin_name,
        tbl_user.phone as vendor_admin_phone,
        
        tbl_payment.txnid,
        tbl_payment.payment_method,
        tbl_payment.currency,
        tbl_payment.customer_name,
        tbl_payment.customer_email,
        tbl_payment.payment_date,
        
        tbl_product.p_name,
        tbl_product.p_sku,
        tbl_product.file_extension,
        tbl_product.p_license

        from tbl_order
        left join tbl_payment on tbl_payment.payment_id = tbl_order.payment_id
        left join tbl_product on tbl_product.p_id = tbl_order.product_id
        left join tbl_customer on tbl_product.user_type = 'Customer' and tbl_customer.cust_id = tbl_product.user_id
        left join tbl_user on tbl_product.user_type = 'Admin' and tbl_user.id = tbl_product.user_id

        Where tbl_order.id = ?
        ");
        $order_sql->execute(array($_REQUEST['id']));
        $order_details = $order_sql->fetch(PDO::FETCH_ASSOC);
        
        $setting_email_sql = $pdo->prepare("SELECT * FROM tbl_setting_email WHERE id=1");
        $setting_email_sql->execute();
        $setting_email = $setting_email_sql->fetch(PDO::FETCH_ASSOC);
    }

    ?>
    <body>

        <div class="page-content container">
            <div class="page-header text-blue-d2">
                <h1 class="page-title text-secondary-d1">
                    Invoice
                    <small class="page-info">
                        <i class="fa fa-angle-double-right text-80"></i>
                        ID: #OD-INV-<?php echo $order_details["id"]; ?>
                    </small>
                </h1>

                <div class="page-tools">
                    <div class="action-buttons">
                        
                        <button class="btn bg-white btn-light mx-1px text-95" href="#" data-title="PDF" onclick="printDiv()">
                            <i class="mr-1 fa fa-file-pdf-o text-danger-m1 text-120 w-2"></i>
                            Export
                        </button>
                    </div>
                </div>
            </div>

            <div class="container px-0 " id="DivIdToPrint">


            <style>
            body{
                margin-top:20px;
                color: #484b51;
            }
            .text-secondary-d1 {
                color: #728299!important;
            }
            .page-header {
                margin: 0 0 1rem;
                padding-bottom: 1rem;
                padding-top: .5rem;
                border-bottom: 1px dotted #e2e2e2;
                display: -ms-flexbox;
                display: flex;
                -ms-flex-pack: justify;
                justify-content: space-between;
                -ms-flex-align: center;
                align-items: center;
            }
            .page-title {
                padding: 0;
                margin: 0;
                font-size: 1.75rem;
                font-weight: 300;
            }
            .brc-default-l1 {
                border-color: #dce9f0!important;
            }

            .ml-n1, .mx-n1 {
                margin-left: -.25rem!important;
            }
            .mr-n1, .mx-n1 {
                margin-right: -.25rem!important;
            }
            .mb-4, .my-4 {
                margin-bottom: 1.5rem!important;
            }

            hr {
                margin-top: 1rem;
                margin-bottom: 1rem;
                border: 0;
                border-top: 1px solid rgba(0,0,0,.1);
            }

            .text-grey-m2 {
                color: #888a8d!important;
            }

            .text-success-m2 {
                color: #86bd68!important;
            }

            .font-bolder, .text-600 {
                font-weight: 600!important;
            }

            .text-110 {
                font-size: 110%!important;
            }
            .text-blue {
                color: #478fcc!important;
            }
            .pb-25, .py-25 {
                padding-bottom: .75rem!important;
            }

            .pt-25, .py-25 {
                padding-top: .75rem!important;
            }
            .bgc-default-tp1 {
                background-color: rgba(121,169,197,.92)!important;
            }
            .bgc-default-l4, .bgc-h-default-l4:hover {
                background-color: #f3f8fa!important;
            }
            .page-header .page-tools {
                -ms-flex-item-align: end;
                align-self: flex-end;
            }

            .btn-light {
                color: #757984;
                background-color: #f5f6f9;
                border-color: #dddfe4;
                padding: 10px 20px;
                border: 1px solid;
                border-radius: 5px;
            }
            
            .w-2 {
                width: 1rem;
            }

            .text-120 {
                font-size: 120%!important;
            }
            .text-primary-m1 {
                color: #4087d4!important;
            }

            .text-danger-m1 {
                color: #dd4949!important;
            }
            .text-blue-m2 {
                color: #68a3d5!important;
            }
            .text-150 {
                font-size: 150%!important;
            }
            .text-60 {
                font-size: 60%!important;
            }
            .text-grey-m1 {
                color: #7b7d81!important;
            }
            .align-bottom {
                vertical-align: bottom!important;
            }

            .text-center{
                text-align: center;
            }
            .col-sm-6 {
                -ms-flex: 0 0 50%;
                flex: 0 0 50%;
                max-width: 50%;
                display:inline-block;
            }
            
            .table {
                width: 100%;
                margin-bottom: 1rem;
                color: #212529;
                border-collapse: collapse;
            }
            .table-bordered td, .table-bordered th {
                border: 1px solid #dee2e6;
            }
            .table td, .table th {
                padding: .75rem;
                vertical-align: top;
                border-top: 1px solid #dee2e6;
            }
        </style>
                <div class="row mt-4">
                    <div class="col-12 col-lg-10 offset-lg-1">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center text-150">
                                    <i class="fa fa-book fa-2x text-success-m2 mr-1"></i>
                                    <span class="text-default-d3"><?php echo WEBSITE_NAME?></span>
                                </div>
                            </div>
                        </div>
                        <!-- .row -->

                        <hr class="row brc-default-l1 mx-n1 mb-4" />

                        <table width="100%">
                            <tr>
                                <td>
                                    <div class="col-sm-6">
                                        <div>
                                            <span class="text-sm text-grey-m2 align-middle">To:</span>
                                            <span class="text-600 text-110 text-blue align-middle"><?php echo $order_details['customer_name'] ?></span>
                                        </div>
                                        <div class="text-grey-m2">
                                            

                                            <div class="my-1"><i class="fa fa-circle text-blue-m2 text-xs mr-1"></i> <span class="text-600 text-90"> Invoice ID:</span>#OD-INV-<?php echo $order_details["id"]; ?></div>

                                            <div class="my-1"><i class="fa fa-circle text-blue-m2 text-xs mr-1"></i> <span class="text-600 text-90"> Transaction Id:</span><?php echo $order_details["txnid"]; ?></div>

                                            <div class="my-1"><i class="fa fa-circle text-blue-m2 text-xs mr-1"></i> <span class="text-600 text-90">Issue Date:</span> <?php echo $order_details['payment_date']?></div>

                                            <div class="my-1"><i class="fa fa-circle text-blue-m2 text-xs mr-1"></i> <span class="text-600 text-90">Payment Done By:</span> <span class="badge badge-warning badge-pill px-25"><?php echo $order_details['payment_method']?></span></div>
                                        </div>
                                        
                                    </div>
                                </td>
                                <!-- /.col -->
                                <td>
                                    <div class="text-95 col-sm-6 align-self-start d-sm-flex justify-content-end">
                                        <br>
                                        <!-- seller Info -->
                                        <?php if($order_details['user_type'] == 'Customer'){ ?>
                                            <div class="text-grey-m2">
                                                <div class="my-1"><i class="fa fa-circle text-blue-m2 text-xs mr-1"></i> <span class="text-600 text-90"> Seller Name:</span><?php echo $order_details["vendor_cust_name"]; ?></div>

                                                <div class="my-1"><i class="fa fa-circle text-blue-m2 text-xs mr-1"></i> <span class="text-600 text-90">Email:</span> <?php echo $order_details['vendor_cust_email']?></div>

                                                <div class="my-1"><i class="fa fa-circle text-blue-m2 text-xs mr-1"></i> <span class="text-600 text-90">Phone:</span> <?php echo $order_details['vendor_cust_phone']?></div>
                                            </div>
                                        <?php } else{?>
                                            <div class="text-grey-m2">
                                            <div class="my-1"><i class="fa fa-circle text-blue-m2 text-xs mr-1"></i> <span class="text-600 text-90"> Seller Name:</span><?php echo $order_details["vendor_admin_name"]; ?></div>

                                            <div class="my-1"><i class="fa fa-circle text-blue-m2 text-xs mr-1"></i> <span class="text-600 text-90">Email:</span> <?php echo $setting_email['receive_email_to']?></div>

                                            <div class="my-1"><i class="fa fa-circle text-blue-m2 text-xs mr-1"></i> <span class="text-600 text-90">Phone:</span> <?php echo $order_details['vendor_admin_phone']?></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>   
                            <!-- /.col -->
                        </table>

                        <div class="mt-4">
                            

                            <div class="row border-b-2 brc-default-l2"></div>

                            <!-- or use a table instead -->
                            
                            <div class="table-responsive">
                                <table class="table table-bordered border-0 border-b-2 brc-default-l1">
                                    <thead class="bg-none bgc-default-tp1">
                                        <tr class="text-white">
                                            <th class="opacity-2">#</th>
                                            <th>Product</th>
                                            <th>Currency</th>
                                            <th>Unit Price</th>
                                            <th>Discount</th>
                                            <th>Price</th>
                                            
                                        </tr>
                                    </thead>

                                    <tbody class="text-95 text-secondary-d3">
                                        
                                        <tr>
                                            <td>1</td>
                                            <td><?php
                                                echo '<p>'.$order_details['p_name'].'</p>';
                                                echo empty($order_details['p_sku'])?'':'<p>'.$order_details['p_sku'].'</p>';
                                                echo '<p> File types:'.$order_details['file_extension'].'</p>';
                                                echo '<p> Licence:'.$order_details['p_license'].'</p>';
                                                echo '<p> Purchased On: '.date('d-m-Y h:i A',strtotime($order_details['payment_date'])).'</p>';
                                                
                                            ?></td>
                                            <td><?php echo $order_details['currency']; ?></td>
                                            <td><?php echo number_format($order_details['actual_price'],2); ?></td>
                                            <td><?php echo number_format($order_details['actual_price']-$order_details['unit_price'],2); ?></td>
                                            <td class="text-95"><?php echo number_format($order_details['unit_price'],2); ?></td>
                                            
                                        </tr> 
                                    </tbody>
                                </table>
                            </div>
                        

                        
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function printDiv() 
            {

            var divToPrint=document.getElementById('DivIdToPrint');

            var newWin=window.open('','Print-Window');

            newWin.document.open();

            newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');

            newWin.document.close();

            setTimeout(function(){newWin.close();},10);

            }
        </script>
    </body>
</html>