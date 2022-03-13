<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
    $valid = 1;
    if($_POST['adv_type'] == 'Image Advertisement') {
        $path = $_FILES['photo']['name'];
        $path_tmp = $_FILES['photo']['tmp_name'];
        if($path != '') {
            $ext = pathinfo( $path, PATHINFO_EXTENSION );
            $file_name = basename( $path, '.' . $ext );
            if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
                $valid = 0;
                $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
            }
        }
    } else {
        if(empty($_POST['adv_adsense_code'])) {
            $valid = 0;
            $error_message .= 'You must have to give an adsense code<br>';
        }
    }

    if($valid == 1) {
        if($_POST['adv_type'] == 'Adsense Code') {
            
            if(isset($_POST['previous_photo'])) {
                unlink('../public_files/uploads/'.$_POST['previous_photo']);    
            }

            $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?,adv_photo=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
            $statement->execute(array($_POST['adv_type'],'','',$_POST['adv_adsense_code'],1));
        } else {
            if($path == '') {
                // updating into the database
                $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
                $statement->execute(array($_POST['adv_type'],$_POST['adv_url'],'',1));
            } else {
                if(isset($_POST['previous_photo'])) {
                    unlink('../public_files/uploads/'.$_POST['previous_photo']);    
                }

                $final_name = 'ad-1.'.$ext;
                move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );

                // updating into the database
                $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?, adv_photo=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
                $statement->execute(array($_POST['adv_type'],$final_name,$_POST['adv_url'],'',1));
            }
        }

        $success_message = 'Advertisement is updated successfully.';
    }
}

if(isset($_POST['form2'])) {
    $valid = 1;
    
    if($_POST['adv_type'] == 'Image Advertisement') {
        $path = $_FILES['photo']['name'];
        $path_tmp = $_FILES['photo']['tmp_name'];
        if($path != '') {
            $ext = pathinfo( $path, PATHINFO_EXTENSION );
            $file_name = basename( $path, '.' . $ext );
            if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
                $valid = 0;
                $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
            }
        }

        $path2 = $_FILES['photo2']['name'];
        $path_tmp2 = $_FILES['photo2']['tmp_name'];
        if($path2 != '') {
            $ext2 = pathinfo( $path2, PATHINFO_EXTENSION );
            $file_name2 = basename( $path2, '.' . $ext2 );
            if( $ext2!='jpg' && $ext2!='png' && $ext2!='jpeg' && $ext2!='gif' ) {
                $valid = 0;
                $error_message .= '[Image-2] You must have to upload jpg, jpeg, gif or png file<br>';
            }
        }

        $path3 = $_FILES['photo3']['name'];
        $path_tmp3 = $_FILES['photo3']['tmp_name'];

        if($path3 != '') {
            $ext3 = pathinfo( $path3, PATHINFO_EXTENSION );
            $file_name3 = basename( $path3, '.' . $ext3 );
            if( $ext3!='jpg' && $ext3!='png' && $ext3!='jpeg' && $ext3!='gif' ) {
                $valid = 0;
                $error_message .= '[Image-3] You must have to upload jpg, jpeg, gif or png file<br>';
            }
        }

    } else {
        if(empty($_POST['adv_adsense_code'])) {
            $valid = 0;
            $error_message .= 'You must have to give an adsense code<br>';
        }
    }
    
    if($valid == 1) {
        if($_POST['adv_type'] == 'Adsense Code') {
            
            if(isset($_POST['previous_photo']) && file_exists('../public_files/uploads/'.$_POST['previous_photo'])) {
                unlink('../public_files/uploads/'.$_POST['previous_photo']);    
            }

            $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?,adv_photo=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
            $statement->execute(array($_POST['adv_type'],'','',$_POST['adv_adsense_code'],2));
        } else {
            if($path == '' && $path2 =='' && $path3 = '') {
                // updating into the database
                $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
                $statement->execute(array($_POST['adv_type'],$_POST['adv_url'],'',2));
            } else {
                if(!empty($path)){
                    if(isset($_POST['previous_photo']) && file_exists('../public_files/uploads/'.$_POST['previous_photo'])) {
                        unlink('../public_files/uploads/'.$_POST['previous_photo']);    
                    }
                    $final_name = 'ad-2.'.$ext;
                    move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );
                }else{
                    $final_name = $_POST['previous_photo'];
                }
                
                if(!empty($path2)){
                    if(isset($_POST['previous_photo2']) && file_exists('../public_files/uploads/'.$_POST['previous_photo2'])) {
                        unlink('../public_files/uploads/'.$_POST['previous_photo2']);    
                    }
                    $final_name2 = 'ad-2-2.'.$ext2;
                    move_uploaded_file( $path_tmp2, '../public_files/uploads/'.$final_name2 );
                }else{
                    $final_name2 = $_POST['previous_photo2'];
                }

                if(!empty($path3)){
                    if(isset($_POST['previous_photo3']) && file_exists('../public_files/uploads/'.$_POST['previous_photo3'])) {
                        unlink('../public_files/uploads/'.$_POST['previous_photo3']);    
                    }
                    $final_name3 = 'ad-2-3.'.$ext3;
                    move_uploaded_file( $path_tmp3, '../public_files/uploads/'.$final_name3 );
                }else{
                    $final_name3 = $_POST['previous_photo3'];
                }
                // updating into the database
                $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?, adv_photo=?,adv_photo2=?,adv_photo3=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
                $statement->execute(array($_POST['adv_type'],$final_name,$final_name2,$final_name3,$_POST['adv_url'],'',2));
            }
        }

        $success_message = 'Advertisement is updated successfully.';
    }
}


if(isset($_POST['form3'])) {
    $valid = 1;
    if($_POST['adv_type'] == 'Image Advertisement') {
        $path = $_FILES['photo']['name'];
        $path_tmp = $_FILES['photo']['tmp_name'];
        if($path != '') {
            $ext = pathinfo( $path, PATHINFO_EXTENSION );
            $file_name = basename( $path, '.' . $ext );
            if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
                $valid = 0;
                $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
            }
        }

        $path2 = $_FILES['photo2']['name'];
        $path_tmp2 = $_FILES['photo2']['tmp_name'];
        if($path2 != '') {
            $ext2 = pathinfo( $path2, PATHINFO_EXTENSION );
            $file_name2 = basename( $path2, '.' . $ext2 );
            if( $ext2!='jpg' && $ext2!='png' && $ext2!='jpeg' && $ext2!='gif' ) {
                $valid = 0;
                $error_message .= '[Image-2] You must have to upload jpg, jpeg, gif or png file<br>';
            }
        }

        $path3 = $_FILES['photo3']['name'];
        $path_tmp3 = $_FILES['photo3']['tmp_name'];

        if($path3 != '') {
            $ext3 = pathinfo( $path3, PATHINFO_EXTENSION );
            $file_name3 = basename( $path3, '.' . $ext3 );
            if( $ext3!='jpg' && $ext3!='png' && $ext3!='jpeg' && $ext3!='gif' ) {
                $valid = 0;
                $error_message .= '[Image-3] You must have to upload jpg, jpeg, gif or png file<br>';
            }
        }
    } else {
        if(empty($_POST['adv_adsense_code'])) {
            $valid = 0;
            $error_message .= 'You must have to give an adsense code<br>';
        }
    }

    if($valid == 1) {
        if($_POST['adv_type'] == 'Adsense Code') {
            
            if(isset($_POST['previous_photo']) && file_exists('../public_files/uploads/'.$_POST['previous_photo'])) {
                unlink('../public_files/uploads/'.$_POST['previous_photo']);    
            }

            $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?,adv_photo=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
            $statement->execute(array($_POST['adv_type'],'','',$_POST['adv_adsense_code'],3));
        } else {
            if($path == '' && $path2 =='' && $path3 = '') {
                // updating into the database
                $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
                $statement->execute(array($_POST['adv_type'],$_POST['adv_url'],'',3));
            } else {
                if(!empty($path)){
                    if(isset($_POST['previous_photo']) && file_exists('../public_files/uploads/'.$_POST['previous_photo'])) {
                        unlink('../public_files/uploads/'.$_POST['previous_photo']);    
                    }
                    $final_name = 'ad-3.'.$ext;
                    move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );
                }else{
                    $final_name = $_POST['previous_photo'];
                }
                
                if(!empty($path2)){
                    if(isset($_POST['previous_photo2']) && file_exists('../public_files/uploads/'.$_POST['previous_photo2'])) {
                        unlink('../public_files/uploads/'.$_POST['previous_photo2']);    
                    }
                    $final_name2 = 'ad-3-2.'.$ext2;
                    move_uploaded_file( $path_tmp2, '../public_files/uploads/'.$final_name2 );
                }else{
                    $final_name2 = $_POST['previous_photo2'];
                }

                if(!empty($path3)){
                    if(isset($_POST['previous_photo3']) && file_exists('../public_files/uploads/'.$_POST['previous_photo3'])) {
                        unlink('../public_files/uploads/'.$_POST['previous_photo3']);    
                    }
                    $final_name3 = 'ad-3-3.'.$ext3;
                    move_uploaded_file( $path_tmp3, '../public_files/uploads/'.$final_name3 );
                }else{
                    $final_name3 = $_POST['previous_photo3'];
                }
                // updating into the database
                $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?, adv_photo=?,adv_photo2=?,adv_photo3=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
                $statement->execute(array($_POST['adv_type'],$final_name,$final_name2,$final_name3,$_POST['adv_url'],'',3));
            }
        }

        $success_message = 'Advertisement is updated successfully.';
    }
}


if(isset($_POST['form4'])) {
    $valid = 1;
    if($_POST['adv_type'] == 'Image Advertisement') {
        $path = $_FILES['photo']['name'];
        $path_tmp = $_FILES['photo']['tmp_name'];
        if($path != '') {
            $ext = pathinfo( $path, PATHINFO_EXTENSION );
            $file_name = basename( $path, '.' . $ext );
            if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
                $valid = 0;
                $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
            }
        }

        $path2 = $_FILES['photo2']['name'];
        $path_tmp2 = $_FILES['photo2']['tmp_name'];
        if($path2 != '') {
            $ext2 = pathinfo( $path2, PATHINFO_EXTENSION );
            $file_name2 = basename( $path2, '.' . $ext2 );
            if( $ext2!='jpg' && $ext2!='png' && $ext2!='jpeg' && $ext2!='gif' ) {
                $valid = 0;
                $error_message .= '[Image-2] You must have to upload jpg, jpeg, gif or png file<br>';
            }
        }

        $path3 = $_FILES['photo3']['name'];
        $path_tmp3 = $_FILES['photo3']['tmp_name'];

        if($path3 != '') {
            $ext3 = pathinfo( $path3, PATHINFO_EXTENSION );
            $file_name3 = basename( $path3, '.' . $ext3 );
            if( $ext3!='jpg' && $ext3!='png' && $ext3!='jpeg' && $ext3!='gif' ) {
                $valid = 0;
                $error_message .= '[Image-3] You must have to upload jpg, jpeg, gif or png file<br>';
            }
        }

    } else {
        if(empty($_POST['adv_adsense_code'])) {
            $valid = 0;
            $error_message .= 'You must have to give an adsense code<br>';
        }
    }

    if($valid == 1) {
        if($_POST['adv_type'] == 'Adsense Code') {
            
            if(isset($_POST['previous_photo']) && file_exists('../public_files/uploads/'.$_POST['previous_photo'])) {
                unlink('../public_files/uploads/'.$_POST['previous_photo']);    
            }

            $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?,adv_photo=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
            $statement->execute(array($_POST['adv_type'],'','',$_POST['adv_adsense_code'],4));
        } else {
            if($path == '' && $path2 =='' && $path3 = '') {
                // updating into the database
                $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
                $statement->execute(array($_POST['adv_type'],$_POST['adv_url'],'',4));
            } else {
                if(!empty($path)){
                    if(isset($_POST['previous_photo']) && file_exists('../public_files/uploads/'.$_POST['previous_photo'])) {
                        unlink('../public_files/uploads/'.$_POST['previous_photo']);    
                    }
                    $final_name = 'ad-4.'.$ext;
                    move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );
                }else{
                    $final_name = $_POST['previous_photo'];
                }
                if(!empty($path2)){
                    if(isset($_POST['previous_photo2']) && file_exists('../public_files/uploads/'.$_POST['previous_photo2'])) {
                        unlink('../public_files/uploads/'.$_POST['previous_photo2']);    
                    }
                    $final_name2 = 'ad-4-2.'.$ext2;
                    move_uploaded_file( $path_tmp2, '../public_files/uploads/'.$final_name2 );
                }else{
                    $final_name2 = $_POST['previous_photo2'];
                }

                if(!empty($path3)){
                    if(isset($_POST['previous_photo3']) && file_exists('../public_files/uploads/'.$_POST['previous_photo3'])) {
                        unlink('../public_files/uploads/'.$_POST['previous_photo3']);    
                    }
                    $final_name3 = 'ad-4-3.'.$ext3;
                    move_uploaded_file( $path_tmp3, '../public_files/uploads/'.$final_name3 );
                }else{
                    $final_name3 = $_POST['previous_photo3'];
                }
                // updating into the database
                $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?, adv_photo=?,adv_photo2=?,adv_photo3=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
                $statement->execute(array($_POST['adv_type'],$final_name,$final_name2,$final_name3,$_POST['adv_url'],'',4));
            }
        }

        $success_message = 'Advertisement is updated successfully.';
    }
}

if(isset($_POST['form5'])) {
    $valid = 1;
    if($_POST['adv_type'] == 'Image Advertisement') {
        $path = $_FILES['photo']['name'];
        $path_tmp = $_FILES['photo']['tmp_name'];
        if($path != '') {
            $ext = pathinfo( $path, PATHINFO_EXTENSION );
            $file_name = basename( $path, '.' . $ext );
            if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
                $valid = 0;
                $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
            }
        }

        $path2 = $_FILES['photo2']['name'];
        $path_tmp2 = $_FILES['photo2']['tmp_name'];
        if($path2 != '') {
            $ext2 = pathinfo( $path2, PATHINFO_EXTENSION );
            $file_name2 = basename( $path2, '.' . $ext2 );
            if( $ext2!='jpg' && $ext2!='png' && $ext2!='jpeg' && $ext2!='gif' ) {
                $valid = 0;
                $error_message .= '[Image-2] You must have to upload jpg, jpeg, gif or png file<br>';
            }
        }

        $path3 = $_FILES['photo3']['name'];
        $path_tmp3 = $_FILES['photo3']['tmp_name'];

        if($path3 != '') {
            $ext3 = pathinfo( $path3, PATHINFO_EXTENSION );
            $file_name3 = basename( $path3, '.' . $ext3 );
            if( $ext3!='jpg' && $ext3!='png' && $ext3!='jpeg' && $ext3!='gif' ) {
                $valid = 0;
                $error_message .= '[Image-3] You must have to upload jpg, jpeg, gif or png file<br>';
            }
        }

    } else {
        if(empty($_POST['adv_adsense_code'])) {
            $valid = 0;
            $error_message .= 'You must have to give an adsense code<br>';
        }
    }

    if($valid == 1) {
        if($_POST['adv_type'] == 'Adsense Code') {
            
            if(isset($_POST['previous_photo']) && file_exists('../public_files/uploads/'.$_POST['previous_photo'])) {
                unlink('../public_files/uploads/'.$_POST['previous_photo']);    
            }

            $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?,adv_photo=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
            $statement->execute(array($_POST['adv_type'],'','',$_POST['adv_adsense_code'],5));
        } else {
            if($path == ''&& $path2 =='' && $path3 = '') {
                // updating into the database
                $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
                $statement->execute(array($_POST['adv_type'],$_POST['adv_url'],'',5));
            } else {
                if(!empty($path)){
                    if(isset($_POST['previous_photo']) && file_exists('../public_files/uploads/'.$_POST['previous_photo'])) {
                        unlink('../public_files/uploads/'.$_POST['previous_photo']);    
                    }
                    $final_name = 'ad-5.'.$ext;
                    move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );
                }else{
                    $final_name = $_POST['previous_photo'];
                }
                
                if(!empty($path2)){
                    if(isset($_POST['previous_photo2']) && file_exists('../public_files/uploads/'.$_POST['previous_photo2'])) {
                        unlink('../public_files/uploads/'.$_POST['previous_photo2']);    
                    }
                    $final_name2 = 'ad-5-2.'.$ext2;
                    move_uploaded_file( $path_tmp2, '../public_files/uploads/'.$final_name2 );
                }else{
                    $final_name2 = $_POST['previous_photo2'];
                }

                if(!empty($path3)){
                    if(isset($_POST['previous_photo3']) && file_exists('../public_files/uploads/'.$_POST['previous_photo3'])) {
                        unlink('../public_files/uploads/'.$_POST['previous_photo3']);    
                    }
                    $final_name3 = 'ad-5-3.'.$ext3;
                    move_uploaded_file( $path_tmp3, '../public_files/uploads/'.$final_name3 );
                }else{
                    $final_name3 = $_POST['previous_photo3'];
                }

                // updating into the database
                $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?, adv_photo=?,adv_photo2=?,adv_photo3=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
                $statement->execute(array($_POST['adv_type'],$final_name,$final_name2,$final_name3,$_POST['adv_url'],'',5));
            }
        }

        $success_message = 'Advertisement is updated successfully.';
    }
}

if(isset($_POST['form6'])) {
    $valid = 1;
    if($_POST['adv_type'] == 'Image Advertisement') {
        $path = $_FILES['photo']['name'];
        $path_tmp = $_FILES['photo']['tmp_name'];
        if($path != '') {
            $ext = pathinfo( $path, PATHINFO_EXTENSION );
            $file_name = basename( $path, '.' . $ext );
            if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
                $valid = 0;
                $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
            }
        }
    } else {
        if(empty($_POST['adv_adsense_code'])) {
            $valid = 0;
            $error_message .= 'You must have to give an adsense code<br>';
        }
    }

    if($valid == 1) {
        if($_POST['adv_type'] == 'Adsense Code') {
            
            if(isset($_POST['previous_photo'])) {
                unlink('../public_files/uploads/'.$_POST['previous_photo']);    
            }

            $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?,adv_photo=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
            $statement->execute(array($_POST['adv_type'],'','',$_POST['adv_adsense_code'],6));
        } else {
            if($path == '') {
                // updating into the database
                $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
                $statement->execute(array($_POST['adv_type'],$_POST['adv_url'],'',6));
            } else {
                if(isset($_POST['previous_photo'])) {
                    unlink('../public_files/uploads/'.$_POST['previous_photo']);    
                }

                $final_name = 'ad-6.'.$ext;
                move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );

                // updating into the database
                $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_type=?, adv_photo=?, adv_url=?,adv_adsense_code=? WHERE adv_id=?");
                $statement->execute(array($_POST['adv_type'],$final_name,$_POST['adv_url'],'',6));
            }
        }

        $success_message = 'Advertisement is updated successfully.';
    }
}

?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Advertisement</h1>
    </div>
</section>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_advertisement");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
foreach ($result as $row) {
    $adv_location[] = $row['adv_location'];
    $adv_type[] = $row['adv_type'];
    $adv_photo[] = $row['adv_photo'];
    $adv_photo2[] = $row['adv_photo2'];
    $adv_photo3[] = $row['adv_photo3'];
    $adv_url[] = $row['adv_url'];
    $adv_adsense_code[] = $row['adv_adsense_code'];
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
                            
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <!-- <li ><a href="#tab_1" data-toggle="tab">Above Welcome Section</a></li> -->
                        <li class="active"><a href="#tab_2" data-toggle="tab">Above Featured Product</a></li>
                        <li><a href="#tab_3" data-toggle="tab">Above Latest Product</a></li>
                        <li><a href="#tab_4" data-toggle="tab">Above Popular Product</a></li>
                        <li><a href="#tab_5" data-toggle="tab">Above Testimonial Section</a></li>
                        <li><a href="#tab_6" data-toggle="tab">Category Page Sidebar</a></li>
                    </ul>
                    <div class="tab-content">


                        <div class="tab-pane " id="tab_1">
                            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                                <div class="box box-info">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="" class="col-sm-3 control-label">Type</label>
                                            <div class="col-sm-6" style="width:auto;">
                                                <select name="adv_type" class="form-control" onchange="funcTab1(this)">
                                                    <?php
                                                    if($adv_type[0] == 'Image Advertisement') {
                                                        ?>
                                                            <option value="Image Advertisement" selected>Image Advertisement</option>
                                                            <option value="Adsense Code">Adsense Code</option>
                                                        <?php
                                                    } else {
                                                        ?>
                                                            <option value="Image Advertisement">Image Advertisement</option>
                                                            <option value="Adsense Code" selected>Adsense Code</option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if($adv_type[0] == 'Image Advertisement'): ?>
                                        <div class="form-group" id="tabField1">
                                            <label class="col-sm-3 control-label">Existing Photo</label>
                                            <div class="col-sm-5" style="padding-top:5px;">
                                                <img src="../public_files/uploads/<?php echo $adv_photo[0]; ?>" style="width:400px;">
                                                <input type="hidden" name="previous_photo" value="<?php echo $adv_photo[0]; ?>">
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <div class="form-group" id="tabField2">
                                            <label class="col-sm-3 control-label">New Photo<br><span style="font-size:12px;font-weight:normal;">(Recommended Width: 1170 pixels and Height: any size)</span></label>
                                            <div class="col-sm-5" style="padding-top:5px;">
                                                <input type="file" name="photo">
                                            </div>
                                        </div>
                                        <div class="form-group" id="tabField3">
                                            <label class="col-sm-3 control-label">URL</label>
                                            <div class="col-sm-5">
                                                <input type="text" name="adv_url" class="form-control" value="<?php echo $adv_url[0]; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group" id="tabField4">
                                            <label class="col-sm-3 control-label">Adsense Code</label>
                                            <div class="col-sm-8">
                                                <textarea name="adv_adsense_code" class="form-control" cols="30" rows="10" style="height:280px;"><?php echo $adv_adsense_code[0]; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="col-sm-3 control-label"></label>
                                            <div class="col-sm-6">
                                                <button type="submit" class="btn btn-success pull-left" name="form1">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane active" id="tab_2">
                            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                                <div class="box box-info">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="" class="col-sm-3 control-label">Type</label>
                                            <div class="col-sm-6" style="width:auto;">
                                                <select name="adv_type" class="form-control"onchange="funcTab2(this)">
                                                    <?php
                                                    if($adv_type[1] == 'Image Advertisement') {
                                                        ?>
                                                            <option value="Image Advertisement" selected>Image Advertisement</option>
                                                            <option value="Adsense Code">Adsense Code</option>
                                                        <?php
                                                    } else {
                                                        ?>
                                                            <option value="Image Advertisement">Image Advertisement</option>
                                                            <option value="Adsense Code" selected>Adsense Code</option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if($adv_type[1] == 'Image Advertisement'): ?>
                                        <div class="form-group" id="tabField5">
                                            <label class="col-sm-3 control-label">Existing Photo</label>
                                            <div class="col-sm-5" style="padding-top:5px;">
                                                <div class="row">
                                                    <?php 
                                                        $style_width = "width: 100%;";
                                                        $div_col="";
                                                        if(!empty($adv_photo[1]) && empty($adv_photo2[1]) && empty($adv_photo3[1])){
                                                            
                                                            $div_col="col-md-12";
                                                        }elseif(!empty($adv_photo[1]) && !empty($adv_photo2[1]) && empty($adv_photo3[1])){
                                                            
                                                            $div_col="col-md-6";
                                                        }elseif(!empty($adv_photo[1]) && empty($adv_photo2[1]) && !empty($adv_photo3[1])){
                                                            
                                                            $div_col="col-md-6";
                                                        }elseif(!empty($adv_photo[1]) && !empty($adv_photo2[1]) && !empty($adv_photo3[1])){
                                                            
                                                            $div_col="col-md-4";
                                                        }
                                                    ?>
                                                    <div class="<?php echo $div_col; ?>">
                                                        <img src="../public_files/uploads/<?php echo $adv_photo[1]; ?>" style="<?php echo $style_width; ?>"> 
                                                    </div>
                                                    <?php if(!empty($adv_photo2[1])){
                                                        if(file_exists('../public_files/uploads/'.$adv_photo2[1])){
                                                        ?>
                                                        <div class="<?php echo $div_col; ?>">
                                                            <a href="delete.php?advtab=2&advimg=2"><span class="fa fa-close" style="color:red"></span></a>
                                                            <img src="../public_files/uploads/<?php echo $adv_photo2[1]; ?>" style="<?php echo $style_width; ?>">
                                                        </div>
                                                    <?php 
                                                        }
                                                    } 
                                                    if(!empty($adv_photo3[1])){ 
                                                        if(file_exists('../public_files/uploads/'.$adv_photo3[1])){ ?>
                                                    <div class="<?php echo $div_col; ?>">
                                                        <a href="delete.php?advtab=2&advimg=3"><span class="fa fa-close" style="color:red"></span></a>
                                                        <img src="../public_files/uploads/<?php echo $adv_photo3[1]; ?>" style="<?php echo $style_width; ?>">
                                                    </div>
                                                    <?php }} ?>
                                                </div>
                                                
                                                <input type="hidden" name="previous_photo" value="<?php echo $adv_photo[1]; ?>">
                                                <input type="hidden" name="previous_photo2" value="<?php echo $adv_photo2[1]; ?>">
                                                <input type="hidden" name="previous_photo3" value="<?php echo $adv_photo3[1]; ?>">
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <div class="form-group" id="tabField6">
                                            <label class="col-sm-3 control-label">New Photo</label>
                                            <div class="col-sm-7" style="padding-top:5px;">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="file" name="photo" accept="image/*"><br><span style="font-size:12px;font-weight:normal;">(Recommended Width: 1170 pixels and Height: any size (If selecting only one.))</span>
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                        <input type="file" name="photo2" accept="image/*">
                                                        <br><span style="font-size:12px;font-weight:normal;">(Recommended Width: 566 pixels and Height: any size (If selecting two then both recommended size.))</span>
                                                        
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                        <input type="file" name="photo3" accept="image/*">
                                                        <br><span style="font-size:12px;font-weight:normal;">(Recommended Width: 466 pixels and Height: any size (If selecting three then both recommended size.))</span>
                                                    </div>
                                                    
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="form-group" id="tabField7">
                                            <label class="col-sm-3 control-label">URL <br><span style="font-size:12px;font-weight:normal;">(Insert Url comma(,) seperated)</span></label>
                                            <div class="col-sm-5">
                                                <input type="text" name="adv_url" class="form-control" value="<?php echo $adv_url[1]; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group" id="tabField8">
                                            <label class="col-sm-3 control-label">Adsense Code</label>
                                            <div class="col-sm-8">
                                                <textarea name="adv_adsense_code" class="form-control" cols="30" rows="10" style="height:280px;"><?php echo $adv_adsense_code[1]; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="col-sm-3 control-label"></label>
                                            <div class="col-sm-6">
                                                <button type="submit" class="btn btn-success pull-left" name="form2">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>


                        <div class="tab-pane" id="tab_3">
                            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                                <div class="box box-info">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="" class="col-sm-3 control-label">Type</label>
                                            <div class="col-sm-6" style="width:auto;">
                                                <select name="adv_type" class="form-control"onchange="funcTab3(this)">
                                                    <?php
                                                    if($adv_type[2] == 'Image Advertisement') {
                                                        ?>
                                                            <option value="Image Advertisement" selected>Image Advertisement</option>
                                                            <option value="Adsense Code">Adsense Code</option>
                                                        <?php
                                                    } else {
                                                        ?>
                                                            <option value="Image Advertisement">Image Advertisement</option>
                                                            <option value="Adsense Code" selected>Adsense Code</option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if($adv_type[2] == 'Image Advertisement'): ?>
                                        <div class="form-group" id="tabField9">
                                            <label class="col-sm-3 control-label">Existing Photo</label>
                                            <div class="col-sm-5" style="padding-top:5px;">
                                                <div class="row">
                                                    <?php 
                                                        $style_width = "width: 100%;";
                                                        $div_col = "";
                                                        if(!empty($adv_photo[2]) && empty($adv_photo2[2]) && empty($adv_photo3[2])){
                                                            
                                                            $div_col = "col-md-12";
                                                        }elseif(!empty($adv_photo[2]) && !empty($adv_photo2[2]) && empty($adv_photo3[2])){
                                                            
                                                            $div_col = "col-md-6";
                                                        }elseif(!empty($adv_photo[2]) && empty($adv_photo2[2]) && !empty($adv_photo3[2])){
                                                            
                                                            $div_col = "col-md-6";
                                                        }elseif(!empty($adv_photo[2]) && !empty($adv_photo2[2]) && !empty($adv_photo3[2])){
                                                            
                                                            $div_col = "col-md-4";
                                                        }
                                                    ?>
                                                </div>
                                                <div class="<?php echo $div_col; ?>">
                                                    <img src="../public_files/uploads/<?php echo $adv_photo[2]; ?>" style="<?php echo $style_width; ?>">
                                                </div>
                                                <?php if(!empty($adv_photo2[2])){
                                                    if(file_exists('../public_files/uploads/'.$adv_photo2[2])){
                                                    ?>
                                                    <div class="<?php echo $div_col; ?>">
                                                        <a href="delete.php?advtab=3&advimg=2"><span class="fa fa-close" style="color:red"></span></a>
                                                        <img src="../public_files/uploads/<?php echo $adv_photo2[2]; ?>" style="<?php echo $style_width; ?>">
                                                    </div>
                                                <?php 
                                                    }
                                                } 
                                                if(!empty($adv_photo3[2])){ 
                                                    if(file_exists('../public_files/uploads/'.$adv_photo3[2])){ ?>
                                                    <div class="<?php echo $div_col; ?>">
                                                        <a href="delete.php?advtab=3&advimg=3"><span class="fa fa-close" style="color:red"></span></a>
                                                        <img src="../public_files/uploads/<?php echo $adv_photo3[2]; ?>" style="<?php echo $style_width; ?>">
                                                    </div>
                                                <?php }} ?>
                                            </div>    
                                            <input type="hidden" name="previous_photo" value="<?php echo $adv_photo[2]; ?>">
                                            <input type="hidden" name="previous_photo2" value="<?php echo $adv_photo2[2]; ?>">
                                            <input type="hidden" name="previous_photo3" value="<?php echo $adv_photo3[2]; ?>">
                                            
                                        </div>
                                        <?php endif; ?>
                                        <div class="form-group" id="tabField10">
                                            <label class="col-sm-3 control-label">New Photo</label>
                                            <div class="col-sm-7" style="padding-top:5px;">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="file" name="photo" accept="image/*"><br><span style="font-size:12px;font-weight:normal;">(Recommended Width: 1170 pixels and Height: any size (If selecting only one.))</span>
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                        <input type="file" name="photo2" accept="image/*">
                                                        <br><span style="font-size:12px;font-weight:normal;">(Recommended Width: 566 pixels and Height: any size (If selecting two then both recommended size.))</span>
                                                        
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                        <input type="file" name="photo3" accept="image/*">
                                                        <br><span style="font-size:12px;font-weight:normal;">(Recommended Width: 466 pixels and Height: any size (If selecting three then both recommended size.))</span>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" id="tabField11">
                                            <label class="col-sm-3 control-label">URL<br><span style="font-size:12px;font-weight:normal;">(Insert Url comma(,) seperated)</span></label>
                                            <div class="col-sm-5">
                                                <input type="text" name="adv_url" class="form-control" value="<?php echo $adv_url[2]; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group" id="tabField12">
                                            <label class="col-sm-3 control-label">Adsense Code</label>
                                            <div class="col-sm-8">
                                                <textarea name="adv_adsense_code" class="form-control" cols="30" rows="10" style="height:280px;"><?php echo $adv_adsense_code[2]; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="col-sm-3 control-label"></label>
                                            <div class="col-sm-6">
                                                <button type="submit" class="btn btn-success pull-left" name="form3">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>



                        <div class="tab-pane" id="tab_4">
                            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                                <div class="box box-info">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="" class="col-sm-3 control-label">Type</label>
                                            <div class="col-sm-6" style="width:auto;">
                                                <select name="adv_type" class="form-control"onchange="funcTab4(this)">
                                                    <?php
                                                    if($adv_type[3] == 'Image Advertisement') {
                                                        ?>
                                                            <option value="Image Advertisement" selected>Image Advertisement</option>
                                                            <option value="Adsense Code">Adsense Code</option>
                                                        <?php
                                                    } else {
                                                        ?>
                                                            <option value="Image Advertisement">Image Advertisement</option>
                                                            <option value="Adsense Code" selected>Adsense Code</option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if($adv_type[3] == 'Image Advertisement'): ?>
                                        <div class="form-group" id="tabField13">
                                            <label class="col-sm-3 control-label">Existing Photo</label>
                                            <div class="col-sm-5" style="padding-top:5px;">
                                                <div class="row">
                                                    <?php 
                                                        $style_width = "width: 100%;";
                                                        $div_col = '';
                                                        if(!empty($adv_photo[3]) && empty($adv_photo2[3]) && empty($adv_photo3[3])){
                                                            
                                                            $div_col = 'col-md-12';
                                                        }elseif(!empty($adv_photo[3]) && !empty($adv_photo2[3]) && empty($adv_photo3[3])){
                                                            
                                                            $div_col = 'col-md-6';
                                                        }elseif(!empty($adv_photo[3]) && empty($adv_photo2[3]) && !empty($adv_photo3[3])){
                                                            
                                                            $div_col = 'col-md-6';
                                                        }elseif(!empty($adv_photo[3]) && !empty($adv_photo2[3]) && !empty($adv_photo3[3])){
                                                           
                                                            $div_col = 'col-md-4';
                                                        }
                                                    ?>
                                                    <div class="<?php echo $div_col; ?>">
                                                        <img src="../public_files/uploads/<?php echo $adv_photo[3]; ?>" style="<?php echo $style_width; ?>">
                                                    </div>
                                                    <?php if(!empty($adv_photo2[3])){
                                                        if(file_exists('../public_files/uploads/'.$adv_photo2[3])){
                                                        ?>
                                                        <div class="<?php echo $div_col; ?>">
                                                            <a href="delete.php?advtab=4&advimg=2"><span class="fa fa-close" style="color:red"></span></a>
                                                            <img src="../public_files/uploads/<?php echo $adv_photo2[3]; ?>" style="<?php echo $style_width; ?>">
                                                        </div>
                                                    <?php 
                                                        }
                                                    } 
                                                    if(!empty($adv_photo3[3])){ 
                                                        if(file_exists('../public_files/uploads/'.$adv_photo3[3])){ ?>
                                                    <div class="<?php echo $div_col; ?>">
                                                        <a href="delete.php?advtab=4&advimg=3"><span class="fa fa-close" style="color:red"></span></a>
                                                        <img src="../public_files/uploads/<?php echo $adv_photo3[3]; ?>" style="<?php echo $style_width; ?>">
                                                    </div>
                                                    <?php }} ?>
                                                </div>
                                                
                                                <input type="hidden" name="previous_photo" value="<?php echo $adv_photo[3]; ?>">
                                                <input type="hidden" name="previous_photo2" value="<?php echo $adv_photo2[3]; ?>">
                                                <input type="hidden" name="previous_photo3" value="<?php echo $adv_photo3[3]; ?>">
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <div class="form-group" id="tabField14">
                                            <label class="col-sm-3 control-label">New Photo</label>
                                            <div class="col-sm-7" style="padding-top:5px;">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="file" name="photo" accept="image/*"><br><span style="font-size:12px;font-weight:normal;">(Recommended Width: 1170 pixels and Height: any size (If selecting only one.))</span>
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                        <input type="file" name="photo2" accept="image/*">
                                                        <br><span style="font-size:12px;font-weight:normal;">(Recommended Width: 566 pixels and Height: any size (If selecting two then both recommended size.))</span>
                                                        
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                        <input type="file" name="photo3" accept="image/*">
                                                        <br><span style="font-size:12px;font-weight:normal;">(Recommended Width: 466 pixels and Height: any size (If selecting three then both recommended size.))</span>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" id="tabField15">
                                            <label class="col-sm-3 control-label">URL<br><span style="font-size:12px;font-weight:normal;">(Insert Url comma(,) seperated)</span></label>
                                            <div class="col-sm-5">
                                                <input type="text" name="adv_url" class="form-control" value="<?php echo $adv_url[3]; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group" id="tabField16">
                                            <label class="col-sm-3 control-label">Adsense Code</label>
                                            <div class="col-sm-8">
                                                <textarea name="adv_adsense_code" class="form-control" cols="30" rows="10" style="height:280px;"><?php echo $adv_adsense_code[3]; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="col-sm-3 control-label"></label>
                                            <div class="col-sm-6">
                                                <button type="submit" class="btn btn-success pull-left" name="form4">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>



                        <div class="tab-pane" id="tab_5">
                            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                                <div class="box box-info">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="" class="col-sm-3 control-label">Type</label>
                                            <div class="col-sm-6" style="width:auto;">
                                                <select name="adv_type" class="form-control"onchange="funcTab5(this)">
                                                    <?php
                                                    if($adv_type[4] == 'Image Advertisement') {
                                                        ?>
                                                            <option value="Image Advertisement" selected>Image Advertisement</option>
                                                            <option value="Adsense Code">Adsense Code</option>
                                                        <?php
                                                    } else {
                                                        ?>
                                                            <option value="Image Advertisement">Image Advertisement</option>
                                                            <option value="Adsense Code" selected>Adsense Code</option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if($adv_type[4] == 'Image Advertisement'): ?>
                                        <div class="form-group" id="tabField17">
                                            <label class="col-sm-3 control-label">Existing Photo</label>
                                            <div class="col-sm-7" style="padding-top:5px;">
                                                <div class="row">
                                                    <?php 
                                                        $style_width = "width: 100%;";
                                                        $div_col ="";
                                                        if(!empty($adv_photo[4]) && empty($adv_photo2[4]) && empty($adv_photo3[4])){
                                                            
                                                            $div_col = 'col-md-12';
                                                        }elseif(!empty($adv_photo[4]) && !empty($adv_photo2[4]) && empty($adv_photo3[4])){
                                                            
                                                            $div_col = 'col-md-6';
                                                        }elseif(!empty($adv_photo[4]) && empty($adv_photo2[4]) && !empty($adv_photo3[4])){
                                                            
                                                            $div_col = 'col-md-6';
                                                        }elseif(!empty($adv_photo[4]) && !empty($adv_photo2[4]) && !empty($adv_photo3[4])){
                                                            
                                                            $div_col = 'col-md-4';
                                                        }
                                                    ?>
                                                    <div class="<?php echo $div_col; ?>">
                                                        <img src="../public_files/uploads/<?php echo $adv_photo[4]; ?>" style="<?php echo $style_width; ?>">
                                                    </div>
                                                    <?php if(!empty($adv_photo2[4])){
                                                        if(file_exists('../public_files/uploads/'.$adv_photo2[4])){
                                                        ?>
                                                        <div class="<?php echo $div_col; ?>">
                                                            <a href="delete.php?advtab=5&advimg=2"><span class="fa fa-close" style="color:red"></span></a>
                                                            <img src="../public_files/uploads/<?php echo $adv_photo2[4]; ?>" style="<?php echo $style_width; ?>">
                                                        </div>
                                                    <?php 
                                                        }
                                                    } 
                                                    if(!empty($adv_photo3[4])){ 
                                                        if(file_exists('../public_files/uploads/'.$adv_photo3[4])){ ?>
                                                    <div class="<?php echo $div_col; ?>">
                                                        <a href="delete.php?advtab=5&advimg=3"><span class="fa fa-close" style="color:red"></span></a>
                                                        <img src="../public_files/uploads/<?php echo $adv_photo3[4]; ?>" style="<?php echo $style_width; ?>">
                                                    </div>
                                                    <?php }} ?>
                                                </div>
                                                
                                                <input type="hidden" name="previous_photo" value="<?php echo $adv_photo[4]; ?>">
                                                <input type="hidden" name="previous_photo2" value="<?php echo $adv_photo2[4]; ?>">
                                                <input type="hidden" name="previous_photo3" value="<?php echo $adv_photo3[4]; ?>">
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <div class="form-group" id="tabField18">
                                            <label class="col-sm-3 control-label">New Photo</label>
                                            <div class="col-sm-7" style="padding-top:5px;">
                                            <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="file" name="photo" accept="image/*"><br><span style="font-size:12px;font-weight:normal;">(Recommended Width: 1170 pixels and Height: any size (If selecting only one.))</span>
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                        <input type="file" name="photo2" accept="image/*">
                                                        <br><span style="font-size:12px;font-weight:normal;">(Recommended Width: 566 pixels and Height: any size (If selecting two then both recommended size.))</span>
                                                        
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                        <input type="file" name="photo3" accept="image/*">
                                                        <br><span style="font-size:12px;font-weight:normal;">(Recommended Width: 466 pixels and Height: any size (If selecting three then both recommended size.))</span>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" id="tabField19">
                                            <label class="col-sm-3 control-label">URL<br><span style="font-size:12px;font-weight:normal;">(Insert Url comma(,) seperated)</span></label>
                                            <div class="col-sm-5">
                                                <input type="text" name="adv_url" class="form-control" value="<?php echo $adv_url[4]; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group" id="tabField20">
                                            <label class="col-sm-3 control-label">Adsense Code</label>
                                            <div class="col-sm-8">
                                                <textarea name="adv_adsense_code" class="form-control" cols="30" rows="10" style="height:280px;"><?php echo $adv_adsense_code[4]; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="col-sm-3 control-label"></label>
                                            <div class="col-sm-6">
                                                <button type="submit" class="btn btn-success pull-left" name="form5">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>


                        <div class="tab-pane" id="tab_6">
                            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                                <div class="box box-info">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="" class="col-sm-3 control-label">Type</label>
                                            <div class="col-sm-6" style="width:auto;">
                                                <select name="adv_type" class="form-control"onchange="funcTab6(this)">
                                                    <?php
                                                    if($adv_type[5] == 'Image Advertisement') {
                                                        ?>
                                                            <option value="Image Advertisement" selected>Image Advertisement</option>
                                                            <option value="Adsense Code">Adsense Code</option>
                                                        <?php
                                                    } else {
                                                        ?>
                                                            <option value="Image Advertisement">Image Advertisement</option>
                                                            <option value="Adsense Code" selected>Adsense Code</option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if($adv_type[5] == 'Image Advertisement'): ?>
                                        <div class="form-group" id="tabField21">
                                            <label class="col-sm-3 control-label">Existing Photo</label>
                                            <div class="col-sm-5" style="padding-top:5px;">
                                                <img src="../public_files/uploads/<?php echo $adv_photo[5]; ?>" style="width:200px;">
                                                <input type="hidden" name="previous_photo" value="<?php echo $adv_photo[5]; ?>">
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <div class="form-group" id="tabField22">
                                            <label class="col-sm-3 control-label">New Photo<br><span style="font-size:12px;font-weight:normal;">(Recommended Width: 260 pixels and Height: any size)</span></label>
                                            <div class="col-sm-5" style="padding-top:5px;">
                                                <input type="file" name="photo">
                                            </div>
                                        </div>
                                        <div class="form-group" id="tabField23">
                                            <label class="col-sm-3 control-label">URL</label>
                                            <div class="col-sm-5">
                                                <input type="text" name="adv_url" class="form-control" value="<?php echo $adv_url[5]; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group" id="tabField24">
                                            <label class="col-sm-3 control-label">Adsense Code</label>
                                            <div class="col-sm-8">
                                                <textarea name="adv_adsense_code" class="form-control" cols="30" rows="10" style="height:280px;"><?php echo $adv_adsense_code[5]; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="col-sm-3 control-label"></label>
                                            <div class="col-sm-6">
                                                <button type="submit" class="btn btn-success pull-left" name="form6">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</section>

<?php require_once('footer.php'); ?>