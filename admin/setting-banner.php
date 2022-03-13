<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
    $valid = 1;

    $path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if($path == '') {
        $valid = 0;
        $error_message .= 'You must have to select a photo<br>';
    } else {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }

    if($valid == 1) {
        // removing the existing photo
        $statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
        foreach ($result as $row) {
            $banner_login = $row['banner_login'];
            unlink('../public_files/uploads/'.$banner_login);
        }

        // updating the data
        $final_name = 'banner_login'.time().'.'.$ext;
        move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );

        // updating the database
        $statement = $pdo->prepare("UPDATE tbl_setting_banner SET banner_login=? WHERE id=1");
        $statement->execute(array($final_name));

        $success_message = 'Login Page Banner is updated successfully.';
        
    }
}

if(isset($_POST['form2'])) {
    $valid = 1;

    $path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if($path == '') {
        $valid = 0;
        $error_message .= 'You must have to select a photo<br>';
    } else {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }

    if($valid == 1) {
        // removing the existing photo
        $statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
        foreach ($result as $row) {
            $banner_registration = $row['banner_registration'];
            unlink('../public_files/uploads/'.$banner_registration);
        }

        // updating the data
        $final_name = 'banner_registration'.time().'.'.$ext;
        move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );

        // updating the database
        $statement = $pdo->prepare("UPDATE tbl_setting_banner SET banner_registration=? WHERE id=1");
        $statement->execute(array($final_name));

        $success_message = 'Registration Page Banner is updated successfully.';
        
    }
}

if(isset($_POST['form3'])) {
    $valid = 1;

    $path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if($path == '') {
        $valid = 0;
        $error_message .= 'You must have to select a photo<br>';
    } else {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }

    if($valid == 1) {
        // removing the existing photo
        $statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
        foreach ($result as $row) {
            $banner_forget_password = $row['banner_forget_password'];
            unlink('../public_files/uploads/'.$banner_forget_password);
        }

        // updating the data
        $final_name = 'banner_forget_password'.time().'.'.$ext;
        move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );

        // updating the database
        $statement = $pdo->prepare("UPDATE tbl_setting_banner SET banner_forget_password=? WHERE id=1");
        $statement->execute(array($final_name));

        $success_message = 'Forget Password Page Banner is updated successfully.';
        
    }
}

if(isset($_POST['form4'])) {
    $valid = 1;

    $path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if($path == '') {
        $valid = 0;
        $error_message .= 'You must have to select a photo<br>';
    } else {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }

    if($valid == 1) {
        // removing the existing photo
        $statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
        foreach ($result as $row) {
            $banner_reset_password = $row['banner_reset_password'];
            unlink('../public_files/uploads/'.$banner_reset_password);
        }

        // updating the data
        $final_name = 'banner_reset_password'.time().'.'.$ext;
        move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );

        // updating the database
        $statement = $pdo->prepare("UPDATE tbl_setting_banner SET banner_reset_password=? WHERE id=1");
        $statement->execute(array($final_name));

        $success_message = 'Reset Password Page Banner is updated successfully.';
        
    }
}


if(isset($_POST['form6'])) {
    $valid = 1;

    $path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if($path == '') {
        $valid = 0;
        $error_message .= 'You must have to select a photo<br>';
    } else {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }

    if($valid == 1) {
        // removing the existing photo
        $statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
        foreach ($result as $row) {
            $banner_search = $row['banner_search'];
            unlink('../public_files/uploads/'.$banner_search);
        }

        // updating the data
        $final_name = 'banner_search'.time().'.'.$ext;
        move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );

        // updating the database
        $statement = $pdo->prepare("UPDATE tbl_setting_banner SET banner_search=? WHERE id=1");
        $statement->execute(array($final_name));

        $success_message = 'Search Page Banner is updated successfully.';
        
    }
}

if(isset($_POST['form7'])) {
    $valid = 1;

    $path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if($path == '') {
        $valid = 0;
        $error_message .= 'You must have to select a photo<br>';
    } else {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }

    if($valid == 1) {
        // removing the existing photo
        $statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
        foreach ($result as $row) {
            $banner_cart = $row['banner_cart'];
            unlink('../public_files/uploads/'.$banner_cart);
        }

        // updating the data
        $final_name = 'banner_cart'.time().'.'.$ext;
        move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );

        // updating the database
        $statement = $pdo->prepare("UPDATE tbl_setting_banner SET banner_cart=? WHERE id=1");
        $statement->execute(array($final_name));

        $success_message = 'Cart Page Banner is updated successfully.';
        
    }
}

if(isset($_POST['form8'])) {
    $valid = 1;

    $path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if($path == '') {
        $valid = 0;
        $error_message .= 'You must have to select a photo<br>';
    } else {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }

    if($valid == 1) {
        // removing the existing photo
        $statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
        foreach ($result as $row) {
            $banner_checkout = $row['banner_checkout'];
            unlink('../public_files/uploads/'.$banner_checkout);
        }

        // updating the data
        $final_name = 'banner_checkout'.time().'.'.$ext;
        move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );

        // updating the database
        $statement = $pdo->prepare("UPDATE tbl_setting_banner SET banner_checkout=? WHERE id=1");
        $statement->execute(array($final_name));

        $success_message = 'Checkout Page Banner is updated successfully.';
        
    }
}

if(isset($_POST['form9'])) {
    $valid = 1;

    $path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if($path == '') {
        $valid = 0;
        $error_message .= 'You must have to select a photo<br>';
    } else {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }

    if($valid == 1) {
        // removing the existing photo
        $statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
        foreach ($result as $row) {
            $banner_product_category = $row['banner_product_category'];
            unlink('../public_files/uploads/'.$banner_product_category);
        }

        // updating the data
        $final_name = 'banner_product_category'.time().'.'.$ext;
        move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );

        // updating the database
        $statement = $pdo->prepare("UPDATE tbl_setting_banner SET banner_product_category=? WHERE id=1");
        $statement->execute(array($final_name));

        $success_message = 'Product Category Page Banner is updated successfully.';
        
    }
}

if(isset($_POST['form5'])) {
    $valid = 1;

    $path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if($path == '') {
        $valid = 0;
        $error_message .= 'You must have to select a photo<br>';
    } else {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }

    if($valid == 1) {
        // removing the existing photo
        $statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
        foreach ($result as $row) {
            $banner_blog = $row['banner_blog'];
            unlink('../public_files/uploads/'.$banner_blog);
        }

        // updating the data
        $final_name = 'banner_blog'.time().'.'.$ext;
        move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );

        // updating the database
        $statement = $pdo->prepare("UPDATE tbl_setting_banner SET banner_blog=? WHERE id=1");
        $statement->execute(array($final_name));

        $success_message = 'Blog Page Banner is updated successfully.';
        
    }
}

if(isset($_POST['form10'])) {
    $valid = 1;

    $path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if($path == '') {
        $valid = 0;
        $error_message .= 'You must have to select a photo<br>';
    } else {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }

    if($valid == 1) {
        // removing the existing photo
        $statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
        foreach ($result as $row) {
            $banner_wishlist = $row['banner_wishlist'];

            if(file_exists('../public_files/uploads/'.$banner_wishlist)){
                unlink('../public_files/uploads/'.$banner_wishlist);
            }
            
        }

        // updating the data
        $final_name = 'banner_wishlist'.time().'.'.$ext;
        
        move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );

        // updating the database
        $statement = $pdo->prepare("UPDATE tbl_setting_banner SET banner_wishlist=? WHERE id=1");
        $statement->execute(array($final_name));

        $success_message = 'Wishlist Page Banner is updated successfully.';
        
    }
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Setting - Banner</h1>
    </div>
</section>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
    $banner_login            = $row['banner_login'];
    $banner_registration     = $row['banner_registration'];
    $banner_forget_password  = $row['banner_forget_password'];
    $banner_reset_password   = $row['banner_reset_password'];
    $banner_search           = $row['banner_search'];
    $banner_cart             = $row['banner_cart'];
    $banner_checkout         = $row['banner_checkout'];
    $banner_product_category = $row['banner_product_category'];
    $banner_blog             = $row['banner_blog'];
    $banner_wishlist         = $row['banner_wishlist'];
}
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

            <table class="table table-bordered">
                <tr>
                    <form action="" method="post" enctype="multipart/form-data">
                    <td style="width:50%">
                        <h4>Existing Login Page Banner</h4>
                        <p>
                            <img src="<?php echo '../public_files/uploads/'.$banner_login; ?>" alt="" style="width: 100%;height:auto;"> 
                        </p>                                        
                    </td>
                    <td style="width:50%">
                        <h4>Change Login Page Banner</h4>
                        Select Photo<input type="file" name="photo">
                        <input type="submit" class="btn btn-primary btn-xs" value="Change" style="margin-top:10px;" name="form1">
                    </td>
                    </form>
                </tr>
                <tr>
                    <form action="" method="post" enctype="multipart/form-data">
                    <td style="width:50%">
                        <h4>Existing Registration Page Banner</h4>
                        <p>
                            <img src="<?php echo '../public_files/uploads/'.$banner_registration; ?>" alt="" style="width: 100%;height:auto;">  
                        </p>                                        
                    </td>
                    <td style="width:50%">
                        <h4>Change Registration Page Banner</h4>
                        Select Photo<input type="file" name="photo">
                        <input type="submit" class="btn btn-primary btn-xs" value="Change" style="margin-top:10px;" name="form2">
                    </td>
                    </form>
                </tr>
                <tr>
                    <form action="" method="post" enctype="multipart/form-data">
                    <td style="width:50%">
                        <h4>Existing Forget Password Page Banner</h4>
                        <p>
                            <img src="<?php echo '../public_files/uploads/'.$banner_forget_password; ?>" alt="" style="width: 100%;height:auto;">   
                        </p>                                        
                    </td>
                    <td style="width:50%">
                        <h4>Change Forget Password Page Banner</h4>
                        Select Photo<input type="file" name="photo">
                        <input type="submit" class="btn btn-primary btn-xs" value="Change" style="margin-top:10px;" name="form3">
                    </td>
                    </form>
                </tr>
                <tr>
                    <form action="" method="post" enctype="multipart/form-data">
                    <td style="width:50%">
                        <h4>Existing Reset Password Page Banner</h4>
                        <p>
                            <img src="<?php echo '../public_files/uploads/'.$banner_reset_password; ?>" alt="" style="width: 100%;height:auto;">   
                        </p>                                        
                    </td>
                    <td style="width:50%">
                        <h4>Change Reset Password Page Banner</h4>
                        Select Photo<input type="file" name="photo">
                        <input type="submit" class="btn btn-primary btn-xs" value="Change" style="margin-top:10px;" name="form4">
                    </td>
                    </form>
                </tr>
                
                <tr>
                    <form action="" method="post" enctype="multipart/form-data">
                    <td style="width:50%">
                        <h4>Existing Search Page Banner</h4>
                        <p>
                            <img src="<?php echo '../public_files/uploads/'.$banner_search; ?>" alt="" style="width: 100%;height:auto;">  
                        </p>                                        
                    </td>
                    <td style="width:50%">
                        <h4>Change Search Page Banner</h4>
                        Select Photo<input type="file" name="photo">
                        <input type="submit" class="btn btn-primary btn-xs" value="Change" style="margin-top:10px;" name="form6">
                    </td>
                    </form>
                </tr>


                <tr>
                    <form action="" method="post" enctype="multipart/form-data">
                    <td style="width:50%">
                        <h4>Existing Cart Page Banner</h4>
                        <p>
                            <img src="<?php echo '../public_files/uploads/'.$banner_cart; ?>" alt="" style="width: 100%;height:auto;">  
                        </p>                                        
                    </td>
                    <td style="width:50%">
                        <h4>Change Cart Page Banner</h4>
                        Select Photo<input type="file" name="photo">
                        <input type="submit" class="btn btn-primary btn-xs" value="Change" style="margin-top:10px;" name="form7">
                    </td>
                    </form>
                </tr>


                <tr>
                    <form action="" method="post" enctype="multipart/form-data">
                    <td style="width:50%">
                        <h4>Existing Checkout Page Banner</h4>
                        <p>
                            <img src="<?php echo '../public_files/uploads/'.$banner_checkout; ?>" alt="" style="width: 100%;height:auto;">  
                        </p>                                        
                    </td>
                    <td style="width:50%">
                        <h4>Change Checkout Page Banner</h4>
                        Select Photo<input type="file" name="photo">
                        <input type="submit" class="btn btn-primary btn-xs" value="Change" style="margin-top:10px;" name="form8">
                    </td>
                    </form>
                </tr>

                <tr>
                    <form action="" method="post" enctype="multipart/form-data">
                    <td style="width:50%">
                        <h4>Existing Product Category Page Banner</h4>
                        <p>
                            <img src="<?php echo '../public_files/uploads/'.$banner_product_category; ?>" alt="" style="width: 100%;height:auto;">  
                        </p>                                        
                    </td>
                    <td style="width:50%">
                        <h4>Change Product Category Page Banner</h4>
                        Select Photo<input type="file" name="photo">
                        <input type="submit" class="btn btn-primary btn-xs" value="Change" style="margin-top:10px;" name="form9">
                    </td>
                    </form>
                </tr>

                <tr>
                    <form action="" method="post" enctype="multipart/form-data">
                    <td style="width:50%">
                        <h4>Existing Blog Page Banner</h4>
                        <p>
                            <img src="<?php echo '../public_files/uploads/'.$banner_blog; ?>" alt="" style="width: 100%;height:auto;">  
                        </p>                                        
                    </td>
                    <td style="width:50%">
                        <h4>Change Blog Page Banner</h4>
                        Select Photo<input type="file" name="photo">
                        <input type="submit" class="btn btn-primary btn-xs" value="Change" style="margin-top:10px;" name="form5">
                    </td>
                    </form>
                </tr>

                <tr>
                    <form action="" method="post" enctype="multipart/form-data">
                    <td style="width:50%">
                        <h4>Existing Wislist Page Banner</h4>
                        <p>
                            <img src="<?php echo '../public_files/uploads/'.$banner_wishlist; ?>" alt="" style="width: 100%;height:auto;">  
                        </p>                                        
                    </td>
                    <td style="width:50%">
                        <h4>Change Wislist Page Banner</h4>
                        Select Photo<input type="file" name="photo">
                        <input type="submit" class="btn btn-primary btn-xs" value="Change" style="margin-top:10px;" name="form10">
                    </td>
                    </form>
                </tr>
            </table>
                
        </div>
    </div>

</section>

<?php require_once('footer.php'); ?>