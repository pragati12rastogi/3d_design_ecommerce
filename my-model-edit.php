<?php require_once('header.php'); ?>
<style>
    .user-pub-nav{
        background: transparent !important;
        box-shadow: 0px 0px 20px 5px lightgrey;
    }
</style>
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

    $statement_p = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=? and user_type =?  and user_id =? ");
	$statement_p->execute(array($_REQUEST['id'],'Customer',$_SESSION['customer']['cust_id']));
	$total_p = $statement_p->rowCount();
	$result = $statement_p->fetchAll(PDO::FETCH_ASSOC);
	if( $total_p == 0 ) {
        alert('Product id not present');
		header('location: my-models.php');
		exit;
	}

    $setting_currency = $pdo->prepare("SELECT * FROM tbl_setting_currency WHERE default_currency=1");
    $setting_currency->execute();
    $default_currency = $setting_currency->fetch(PDO::FETCH_ASSOC);
    $currency_sign = $default_currency['currency_symbol'];
}


$statement = $pdo->prepare("SELECT t1.* FROM tbl_product t1 
LEFT JOIN tbl_top_category t2 on t2.tcat_id = t1.cat_id
LEFT JOIN tbl_mid_category t3 on t3.mcat_id = t1.subcat_id WHERE p_id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
	$p_name = $row['p_name'];
	$p_old_price = $row['p_old_price'];
	$p_current_price = $row['p_current_price'];
	
	$p_featured_photo = $row['p_featured_photo'];
	$p_description = $row['p_description'];
	
	
	$p_is_featured = $row['p_is_featured'];
	$p_is_active = $row['p_is_active'];

	$p_sku = $row['p_sku'];
	$prod_model_file = $row['prod_model_file'];
    $youtube_prev = $row['youtube_prev'];
    $vimeo_prev = $row['vimeo_prev'];
    $p_tags = $row['p_tags'];
    $p_license = $row['p_license'];
    $p_custom_license = $row['p_custom_license'];
    $is_free = $row['is_free'];
	$tcat_id = $row['cat_id'];
    $mcat_id = $row['subcat_id'];
}


$product_photo_query = $pdo->prepare("SELECT * FROM tbl_product_photo WHERE p_id=?");
$product_photo_query->execute(array($_REQUEST['id']));
$product_photo = $product_photo_query->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page">
    <div class="container">
        <div class="row">            
            <div class="col-md-12"> 
                <?php require_once('customer-sidebar.php'); ?>
            </div>
            <div class="col-md-12">
                <div class="alert alert-danger" id="alert-danger" style="display:none">
                    
                </div>
                
                <div class="alert alert-success" id="alert-success" style="display:none">
                    
                </div>
                
                <?php if(isset($_COOKIE['publishing_success'])){
                    echo '<div class="alert alert-success" ><p>'.$_COOKIE['publishing_success'].'</p></div>';
                }?>
                <script>setTimeout(function(){ $(".alert-success").hide(); }, 10000);</script>
                <div class="nav-tabs-custom">
                    <div class=" p_10 mt_40 user-pub-nav text-center">
                        <h4>Model Upload</h4>
                    </div>
                    <div class="mt_20 tab-content user-pub-nav">
                        <div class="">
                            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">

                                <div class="box box-info">
                                    <div class="box-body">
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Category Name <span>*</span></label>
                                            <div class="col-sm-8">
                                                <select name="tcat_id" class="form-control select2 top-cat">
                                                    <option value="">Select Category</option>
                                                    <?php
                                                    $statement = $pdo->prepare("SELECT * FROM tbl_top_category ORDER BY tcat_name ASC");
                                                    $statement->execute();
                                                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);   
                                                    foreach ($result as $row) {
                                                        ?>
                                                        <option value="<?php echo $row['tcat_id']; ?>" <?php if($row['tcat_id'] == $tcat_id){echo 'selected';} ?>><?php echo $row['tcat_name']; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Sub Category Name </label>
                                            <div class="col-sm-8">
                                                <select name="mcat_id" class="form-control select2 mid-cat">
                                                    <option value="">Select Sub Category</option>
                                                    <?php
                                                    $statement = $pdo->prepare("SELECT * FROM tbl_mid_category WHERE tcat_id = ? ORDER BY mcat_name ASC");
                                                    $statement->execute(array($tcat_id));
                                                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);   
                                                    foreach ($result as $row) {
                                                        ?>
                                                        <option value="<?php echo $row['mcat_id']; ?>" <?php if($row['mcat_id'] == $mcat_id){echo 'selected';} ?>><?php echo $row['mcat_name']; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Uploaded file </label>
                                            <div class="col-sm-8" style="padding-top:4px;">
                                                <a href="download-product.php?zip_product=<?php echo $_REQUEST['id']; ?>" > Click here to download file: <b><?php echo $p_name; ?></b></a>
                                                <input type="hidden" name="old_prod_model_file" value="<?php echo $prod_model_file; ?>">
                                                <input type="hidden" name="old_file_extension" value="<?php echo $file_extension; ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Upload Your 3D model File <span>*</span><br><span style="font-size:12px;font-weight:normal;">( File Format in .zip Only )</span></label>
                                            <div class="col-sm-8" style="padding-top:4px;">
                                                <input type="file" name="prod_model_file[]" id="prod_model_file" class="image-uploader" multiple >
                                            </div>
                                            <div class="col-md-12">
                                                <span id="append_after_list" class="fa fa-spin fa-spinner" style="margin: 1% 45%; display:none"></span>
                                                <?php  
                                                    $get_files_query = $pdo->prepare("SELECT * from temp_modal_files_upload where prod_id=?");
                                                    $get_files_query->execute(array($_REQUEST['id']));
                                                    $get_files = $get_files_query->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach( $get_files as $file_id => $files_details){ 
                                                        echo '<div  class="prod_upload_files_old row mb_10 ">
                                                            <div class="col-md-10 p_10">
                                                                <p class="imageThumb m_0 "  title="'.$files_details['filename'].'">'. $files_details['filename'] .'</p>
                                                            </div>
                                                        <div class="col-md-2 center p_10"><button type="button" class="btn btn-danger btn-block btn-xs prev_remove" btn-id='. $files_details['id'] .' onclick="old_model_del(this)" >Delete</button></div></div>';
                                                    }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">3D Model Name <span>*</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" name="p_name" class="form-control" value="<?php echo $p_name; ?>">
                                            </div>
                                        </div>	
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">SKU No </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="p_sku" class="form-control" value="<?php echo $p_sku; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Old Price<br><span style="font-size:10px;font-weight:normal;">(In <?php echo $currency_sign; ?>)</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" name="p_old_price" id="p_old_price" class="form-control" value="<?php echo $p_old_price; ?>">
                                            </div>
                                        </div>	
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Current Price <span>*</span><br><span style="font-size:10px;font-weight:normal;">(In <?php echo $currency_sign; ?>)</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" name="p_current_price" id="p_current_price" class="form-control" value="<?php echo $p_current_price; ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="checkbox-inline" >
                                                <input type="checkbox" name="is_free" id="is_free" value="1" <?php echo ($is_free==1)?'checked':''; ?> style="height: 15px;width: 15px;" onclick="is_freebtnfn()">Share For Free</label> 
                                            </div>
                                        </div>	
                                        
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Embeds Youtube Link</label>
                                            <div class="col-sm-8" style="padding-top:4px;">
                                                <input name="youtube_prev" value="<?php echo $youtube_prev; ?>" type="text" class="form-control" placeholder="eg: https://youtube.com/watch?v=">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Embeds Vimeo Link</label>
                                            <div class="col-sm-8" style="padding-top:4px;">
                                                <input name="vimeo_prev" value="<?php echo $vimeo_prev; ?>" type="text" class="form-control" placeholder="eg: https://vimeo.com/">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Existing Featured Photo</label>
                                            <div class="col-sm-8" style="padding-top:4px;">
                                                <img src="public_files/uploads/<?php echo $p_featured_photo; ?>" alt="" style="width:150px;">
                                                <input type="hidden" name="current_photo" value="<?php echo $p_featured_photo; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Change Featured Photo <br><span style="font-size:10px;font-weight:normal;">File Size: Max=5MB & Formats: jpg,png,jpeg,bmp</span></label>
                                            <div class="col-sm-8" style="padding-top:4px;">
                                                <input type="file" name="p_featured_photo">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="" class="col-sm-2 control-label">Other Photos <br><span style="font-size:10px;font-weight:normal;">File Size: Max=5MB & Formats: jpg,png,jpeg,bmp</span></label>
                                            <div class="col-sm-6" style="padding-top:4px;">
                                                <div id="preview_images" style="padding-top: .5rem;"></div>
                                                
                                            </div>
                                           
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="" class="col-sm-2 control-label">Description</label>
                                            <div class="col-sm-10">
                                                <textarea id="editor1" class="form-control ckeditor" cols="30" rows="10"><?php echo $p_description; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="" class="col-sm-2 control-label">Tags <span>*</span> <sup class="fa fa-question-circle" data-toggle="tooltip" title="Add specific words that target your model, broad words which will generalize it and note its qualities or characteristics (like color, material, condition, etc). Do not add unrelated tags to your product." data-placement="top"></sup><br><span style="font-weight:100;font-size: small;">(Use comma(,) to seperate tags)</span> </label>
                                            <div class="col-sm-10">
                                                <input name="p_tags" data-role="tagsinput" id="p_tags" value="<?php echo $p_tags; ?>" class="form-control tag_input" type="text">
                                                <br>
                                                <div id="normally-hidden" class="form-control-static">
                                                    
                                                </div>
                                            </div>

                                            
                                        </div>
                                        
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">License <span>*</span></label>
                                            <div class="col-sm-8">
                                                <select name="p_license" class="form-control" id="p_license" onchange="custom_license()" >
                                                    <option value="royalty_free" <?php echo ($p_license == 'royalty_free')?'selected':''; ?> >Royalty Fee</option>
                                                    <option value="editorial" <?php echo ($p_license == 'editorial')?'selected':''; ?>>Editorial</option>
                                                    <option value="custom" <?php echo ($p_license == 'custom')?'selected':''; ?>>Custom</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6" id="custom_license_div" style="display:none">
                                            <label for="" class="col-sm-4 control-label">Custom License <span>*</span></label>
                                            <div class="col-sm-8">
                                                <textarea name="p_custom_license" class="form-control" cols="30" rows="10" id="editor5"><?php echo $p_custom_license; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="" class="col-sm-2 control-label">Is Featured?</label>
                                            <div class="col-sm-4">
                                                <select name="p_is_featured" class="form-control" style="width:auto;">
                                                    <option value="0" <?php if($p_is_featured == '0'){echo 'selected';} ?>>No</option>
                                                    <option value="1" <?php if($p_is_featured == '1'){echo 'selected';} ?>>Yes</option>
                                                </select> 
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Is Active?</label>
                                            <div class="col-sm-8">
                                                <select name="p_is_active" class="form-control" style="width:auto;">
                                                    <option value="0" <?php if($p_is_active == '0'){echo 'selected';} ?>>No</option>
                                                    <option value="1" <?php if($p_is_active == '1'){echo 'selected';} ?>>Yes</option>
                                                </select> 
                                            </div>
                                        </div>

                                        <input type="hidden" name="product_id" value="<?php echo $_REQUEST['id']; ?>">

                                        <input type="hidden" name="deleted_photo" id="deleted_photo" >

                                        <div class="form-group container-fluid">
                                            
                                            <div class="col-sm-12 pl_70 pr_70 pt_40">
                                                <button type="submit" class="btn btn-block btn-success pull-left" name="form1">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>               
            </div>
        </div>
    </div>
    <?php 
        foreach($product_photo as $ind => $photo_data){
            $preloaded[] = ['id'=>$photo_data['pp_id'],'src'=>'public_files/uploads/product_photos/'.$photo_data["photo"]];
        }
        $preloaded_obj = json_encode($preloaded);
                
    ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
		is_freebtnfn();
		custom_license();
		var tag_old_item = $('#p_tags').val();
		trigger_after_tag_add(tag_old_item);
        
        let preloaded = <?php echo $preloaded_obj; ?>;
        $('#preview_images').imageUploader({
            preloaded: preloaded,
            imagesInputName: 'photos',
            preloadedInputName: 'old'
        });
        $(".delete-image").on('click',function(){
            var photo_id = $(this).attr('data-id');
            if(photo_id != 0){
                var url = "model-other-photo-del.php?id="+photo_id+"&id1=<?php echo $_REQUEST['id']; ?>";
                $.ajax({
                    url : url,
                    type : 'GET',
                    dataType: "json",
                    success: function(result){
                        if(result.status == 'success'){
							$("#alert-success").append('<p>'+result.msg+'</p>').show();
                        	setTimeout(function(){ $("#alert-success").hide(); }, 10000);
						}else{
							alert(result.msg);
							window.location.href= result.redirect;
						}
                    },
					error: function(jq,status,message) {
						alert('A jQuery error has occurred. Error:'+responseText);
					}
                })
            }    
        })

	}, false);

	function is_freebtnfn(){
		var chcekbox_selected = $("#is_free").prop("checked");
		if(chcekbox_selected){
			$("#p_old_price").val(0).css({"background": "lightgray","pointer-events": "none"});
			$("#p_current_price").val(0).css({"background": "lightgray","pointer-events": "none"});
		}else{
			$("#p_old_price").removeAttr("style");
			$("#p_current_price").removeAttr("style");
		}
	}

	function custom_license(){
		var license_val = $("#p_license").val();
		if(license_val == "custom"){
			$("#custom_license_div").show('slow');
		}else{
			$("#custom_license_div").hide('slow');
		}
		
	}

</script>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>public_files/css/image-uploader.css">
<script src="<?php echo BASE_URL; ?>public_files/js/multiple-file-upload/prod_file_upload.js"></script>
<script src="<?php echo BASE_URL; ?>public_files/js/multiple-file-upload/prod_image_upload.js"></script>
<script>
    function old_model_del(selected_file){
        var model_file_id = selected_file.getAttribute("btn-id");
        $.ajax({
            url : 'ajax_function.php',
            type : 'GET',
            dataType: "json",
            data : {model_file_del:1,model_file_id:model_file_id},
            success: function(result){
                
                if(result.status == 'success'){
                    $(selected_file).parent().parent('.prod_upload_files_old').remove();
                }else{
                    alert(result);
                }
                
            }
        }) 
    }

    $('form').on('submit', function (event) {
        // Stop propagation
        event.preventDefault();
        event.stopPropagation();

        $("#alert-success").empty();
        $("#alert-danger").empty();
        
        var formData = new FormData(this);
        var decription_data = CKEDITOR.instances.editor1.getData();
		formData.append('p_description',decription_data);

        $uploaded_prodfile_path = $("input[name='uploaded_prodfile_path[]']");
        $uploaded_prodfile_name = $("input[name='uploaded_prodfile_name[]']");
        $uploaded_prodfile_type = $("input[name='uploaded_prodfile_type[]']");
        pathlength = $uploaded_prodfile_path.length;

        var prodfile_size = 0        
        for( var ind =0; ind < pathlength;ind++){

            $file_path = $uploaded_prodfile_path[ind].defaultValue;
            $file_name = $uploaded_prodfile_name[ind].defaultValue;
            $file_type = $uploaded_prodfile_type[ind].defaultValue;

            var file_form = dataURLtoFile($file_path,$file_name);
            prodfile_size= prodfile_size+file_form.size;
            formData.append('temp_prod_model_file[]', file_form);
        }

        if(prodfile_size > 5368706371){
            $("#alert-danger").append('<p>Uploaded Model size cannot be greater than 5GB </p>').show();
            
            setTimeout(function(){ $("#alert-danger").hide(); }, 10000);
        }else{

            document.getElementById("preloader").style.display = 'block';
            document.getElementById("status").style.display = 'block';

            
            $.ajax({
                url : 'ajax_function.php?my-model-edit=1',
                type : 'POST',
                data : formData,
                dataType: "json",
                processData: false,  // tell jQuery not to process the data
                contentType: false,  // tell jQuery not to set contentType
                success : function(data) {
                    
                    document.getElementById("preloader").style.display = 'none';
                    document.getElementById("status").style.display = 'none';
                    if(data.status == 'success'){
                        $("#alert-success").append('<p>'+data.msg+'</p>').show();
                        setTimeout(function(){ $("#alert-success").hide(); }, 10000);
                        window.location.href="<?php echo BASE_URL; ?>my-model-edit.php?id=<?php echo $_REQUEST['id']; ?>";

                    }else if(data.status == 'error'){
                        $("#alert-danger").append('<p>'+data.msg+'</p>').show();
                        
                        setTimeout(function(){ $("#alert-danger").hide(); }, 10000);
                    }else{
                        alert(data);
                    }   
                },
                error: function(jq,status,message) {
                    document.getElementById("preloader").style.display = 'none';
                    document.getElementById("status").style.display = 'none';
                    alert('A jQuery error has occurred. Error:'+responseText);
                }
            });
        
        }
        
    });
    
</script>
<style>
    input#prod_model_file {
        display: inline-block;
        width: 100%;
        padding: 100px 0 0 0;
        height: 80px;
        overflow: hidden;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        background: url('<?php echo BASE_URL; ?>public_files/img/698394.png') center center no-repeat #e4e4e4;
        border-radius: 20px;
        background-size: 60px 60px;
    }
    .prod_upload_files, .prod_upload_files_old{
        
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        background:#e4e4e4;
        border-radius: 5px;
    }
</style>
<?php require_once('footer.php'); ?>