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
                        <div class="" id="">
                            <!-- method="POST" action="" -->
                            <form class="form-horizontal"  enctype="multipart/form-data">

                                <div class="box box-info">
                                    <div class="box-body">
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Category Name <span>*</span></label>
                                            <div class="col-sm-8">
                                                <select name="tcat_id" class="form-control select2 top-cat" required>
                                                    <option value="">Select Category</option>
                                                    <?php
                                                    $statement = $pdo->prepare("SELECT * FROM tbl_top_category ORDER BY tcat_name ASC");
                                                    $statement->execute();
                                                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);	
                                                    foreach ($result as $row) {
                                                        ?>
                                                        <option value="<?php echo $row['tcat_id']; ?>"><?php echo $row['tcat_name']; ?></option>
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
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Upload Your 3D model File <span>*</span><br><span style="font-size:12px;font-weight:normal;">( File Format in .3dm, .jcd and .stl Only )</span></label>
                                            <div class="col-sm-8" style="padding-top:4px;">
                                                <input type="file" name="prod_model_file[]" id="prod_model_file" class="image-uploader" multiple>
                                            </div>
                                            <div class="col-md-12">
                                                <span id="append_after_list" class="fa fa-spin fa-spinner" style="margin: 1% 45%; display:none"></span>
                                            </div>
                                            
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">3D Model Name <span>*</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" name="p_name" class="form-control" required>
                                            </div>
                                        </div>	
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">SKU No </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="p_sku" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Old Price <br><span style="font-size:12px;font-weight:normal;">(In <?php echo $currency_sign; ?>)</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" name="p_old_price" class="form-control" id="p_old_price">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Current Price <span>*</span><br><span style="font-size:12px;font-weight:normal;">(In <?php echo $currency_sign; ?>)</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" name="p_current_price" class="form-control" id="p_current_price" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="checkbox-inline" >
                                                <input type="checkbox" name="is_free" id="is_free" value="1" style="height: 15px;width: 15px;" onclick="is_freebtnfn()">Share For Free</label> 
                                            </div>
                                        </div>	
                                       
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Embeds Youtube Link</label>
                                            <div class="col-sm-8" style="padding-top:4px;">
                                                <input name="youtube_prev" value="" type="text" class="form-control" placeholder="eg: https://youtube.com/watch?v=">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Embeds Vimeo Link</label>
                                            <div class="col-sm-8" style="padding-top:4px;">
                                                <input name="vimeo_prev" value="" type="text" class="form-control" placeholder="eg: https://vimeo.com/">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Featured Photo <span>*</span> <br><span style="font-size:10px;font-weight:normal;">File Size: Max=5MB & Formats: jpg,png,jpeg,bmp</span> </label>
                                            <div class="col-sm-8" style="padding-top:4px;">
                                                <input type="file" name="p_featured_photo" accept="image/*" required>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">Tags <span>*</span> <sup class="fa fa-question-circle" data-toggle="tooltip" title="Add specific words that target your model, broad words which will generalize it and note its qualities or characteristics (like color, material, condition, etc). Do not add unrelated tags to your product." data-placement="top"></sup><br><span style="font-weight:100;font-size: small;">(Use comma(,) to seperate tags)</span> </label>
                                            <div class="col-sm-8">
                                                <input name="p_tags" data-role="tagsinput"  class="tag_input" type="text" >
                                                <br>
                                                <div id="normally-hidden" class="form-control-static">
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="" class="col-sm-2 control-label">Preview Photos <br><span style="font-size:10px;font-weight:normal;">File Size: Max=5MB & Formats: jpg,png,jpeg,bmp</span></label>
                                            <div class="col-sm-6" style="padding-top:4px;">
                                                <div class="upload-btn">
                                                <div id="preview_images" style="padding-top: .5rem;"></div>
                                                    <!-- <input type="file" name="photo[]" style="margin-bottom:5px;" id="preview_images"> -->
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="" class="col-sm-2 control-label">Description <span>*</span></label>
                                            <div class="col-sm-10">
                                                <textarea id="editor1" class="form-control ckeditor" required></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group col-md-6">
                                            <label for="" class="col-sm-4 control-label">License <span>*</span></label>
                                            <div class="col-sm-8 pl_20">
                                                <select name="p_license" class="form-control" id="p_license" onchange="custom_license()" >
                                                    <option value="royalty_free">Royalty Free</option>
                                                    <option value="editorial">Editorial</option>
                                                    <option value="custom">Custom</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12" id="custom_license_div" style="display:none">
                                            <label for="" class="col-sm-2 control-label">Custom License <span>*</span></label>
                                            <div class="col-sm-10">
                                                <textarea name="p_custom_license" class="form-control" cols="30" rows="10" id="editor5"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="" class="col-sm-2 control-label">Is Featured?</label>
                                            <div class="col-sm-4">
                                                <select name="p_is_featured" class="form-control" style="width:auto;">
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select> 
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="" class="col-sm-2 control-label">Is Active?</label>
                                            <div class="col-sm-4 ">
                                                <select name="p_is_active" class="form-control" style="width:auto;">
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select> 
                                            </div>
                                        </div>
                                        <input type="hidden" name="deleted_photo" id="deleted_photo" >
                                        <div class="form-group container-fluid">
                                            
                                            <div class="col-sm-12 pl_70 pr_70 pt_40">
                                                <button type="submit" class="btn btn-info btn-block pull-left" name="form1">Submit</button>
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
</div>

<script>
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
    
	$('#preview_images').imageUploader();

    $('form').on('submit', function (event) {
        // Stop propagation
        event.preventDefault();
        event.stopPropagation();

        $("#alert-success").empty();
        $("#alert-danger").empty();
        
        var formData = new FormData(this);

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
                url : 'ajax_function.php?publishing_model=1',
                type : 'POST',
                data : formData,
                dataType: "json",
                processData: false,  // tell jQuery not to process the data
                contentType: false,  // tell jQuery not to set contentType
                success : function(data) {
                    console.log(data);
                    // data = JSON.parse(data);
                    document.getElementById("preloader").style.display = 'none';
                    document.getElementById("status").style.display = 'none';
                    if(data.status == 'success'){
                        $("#alert-success").append('<p>'+data.msg+'</p>').show();
                        setTimeout(function(){ $("#alert-success").hide(); }, 10000);
                        window.location.href="<?php echo BASE_URL; ?>publishing.php";

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
                    alert('A jQuery error has occurred. Error:'+ jq.responseText);
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
    .prod_upload_files{
        
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        background:#e4e4e4;
        border-radius: 5px;
    }
</style>
<?php require_once('footer.php'); ?>