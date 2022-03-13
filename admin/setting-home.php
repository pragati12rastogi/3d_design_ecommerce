<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1']))
{
    $statement = $pdo->prepare("UPDATE tbl_setting_home SET home_service_on_off=?, home_welcome_on_off=?, home_featured_product_on_off=?, home_latest_product_on_off=?, home_popular_product_on_off=?, home_testimonial_on_off=? WHERE id=1");
    $statement->execute(array($_POST['home_service_on_off'],$_POST['home_welcome_on_off'],$_POST['home_featured_product_on_off'],$_POST['home_latest_product_on_off'],$_POST['home_popular_product_on_off'],$_POST['home_testimonial_on_off']));

    $success_message = 'Home (Section On Off) setting is updated successfully.';
}

if(isset($_POST['form2']))
{
    $statement = $pdo->prepare("UPDATE tbl_setting_home SET meta_title_home=?, meta_keyword_home=?, meta_description_home=? WHERE id=1");
    $statement->execute(array($_POST['meta_title_home'],$_POST['meta_keyword_home'],$_POST['meta_description_home']));

    $success_message = 'Home (Meta Information) setting is updated successfully.';
}

if(isset($_POST['form3'])) {

    $valid = 1;

    if(empty($_POST['cta_title'])) {
        $valid = 0;
        $error_message .= 'Call to Action Title can not be empty<br>';
    }

    if(empty($_POST['cta_content'])) {
        $valid = 0;
        $error_message .= 'Call to Action Content can not be empty<br>';
    }

    if(empty($_POST['cta_read_more_text'])) {
        $valid = 0;
        $error_message .= 'Call to Action Read More Text can not be empty<br>';
    }

    if(empty($_POST['cta_read_more_url'])) {
        $valid = 0;
        $error_message .= 'Call to Action Read More URL can not be empty<br>';
    }

    $path = $_FILES['cta_photo']['name'];
    $path_tmp = $_FILES['cta_photo']['tmp_name'];

    if($path != '') {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }

    if($valid == 1) {

        if($path != '') {
            // removing the existing photo
            $statement = $pdo->prepare("SELECT * FROM tbl_setting_home WHERE id=1");
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
            foreach ($result as $row) {
                $cta_photo = $row['cta_photo'];
                unlink('../public_files/uploads/'.$cta_photo);
            }

            // updating the data
            $final_name = 'cta'.time().'.'.$ext;
            move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );

            // updating the database
            $statement = $pdo->prepare("UPDATE tbl_setting_home SET cta_title=?,cta_content=?,cta_read_more_text=?,cta_read_more_url=?,cta_photo=? WHERE id=1");
            $statement->execute(array($_POST['cta_title'],$_POST['cta_content'],$_POST['cta_read_more_text'],$_POST['cta_read_more_url'],$final_name));
        } else {
            // updating the database
            $statement = $pdo->prepare("UPDATE tbl_setting_home SET cta_title=?,cta_content=?,cta_read_more_text=?,cta_read_more_url=? WHERE id=1");
            $statement->execute(array($_POST['cta_title'],$_POST['cta_content'],$_POST['cta_read_more_text'],$_POST['cta_read_more_url']));
        }

        $success_message = 'Home (Call to Action) setting is updated successfully.';
        
    }
}



if(isset($_POST['form4'])) 
{
    $valid = 1;

    if(empty($_POST['featured_product_title'])) {
        $valid = 0;
        $error_message .= 'Featured Product Title can not be empty<br>';
    }

    if(empty($_POST['featured_product_subtitle'])) {
        $valid = 0;
        $error_message .= 'Featured Product SubTitle can not be empty<br>';
    }

    if($valid == 1) {
        $statement = $pdo->prepare("UPDATE tbl_setting_home SET featured_product_title=?,featured_product_subtitle=? WHERE id=1");
        $statement->execute(array($_POST['featured_product_title'],$_POST['featured_product_subtitle']));

        $success_message = 'Home (Featured Product) setting is updated successfully.';
    }
}



if(isset($_POST['form5']))
{
    $valid = 1;

    if(empty($_POST['latest_product_title'])) {
        $valid = 0;
        $error_message .= 'Latest Product Title can not be empty<br>';
    }

    if(empty($_POST['latest_product_subtitle'])) {
        $valid = 0;
        $error_message .= 'Latest Product SubTitle can not be empty<br>';
    }

    if($valid == 1) {
        $statement = $pdo->prepare("UPDATE tbl_setting_home SET latest_product_title=?,latest_product_subtitle=? WHERE id=1");
        $statement->execute(array($_POST['latest_product_title'],$_POST['latest_product_subtitle']));

        $success_message = 'Home (Latest Product) setting is updated successfully.';
    }
}


if(isset($_POST['form6'])) 
{
    $valid = 1;

    if(empty($_POST['popular_product_title'])) {
        $valid = 0;
        $error_message .= 'Popular Product Title can not be empty<br>';
    }

    if(empty($_POST['popular_product_subtitle'])) {
        $valid = 0;
        $error_message .= 'Popular Product SubTitle can not be empty<br>';
    }

    if($valid == 1) {
        $statement = $pdo->prepare("UPDATE tbl_setting_home SET popular_product_title=?,popular_product_subtitle=? WHERE id=1");
        $statement->execute(array($_POST['popular_product_title'],$_POST['popular_product_subtitle']));

        $success_message = 'Home (Popular Product) setting is updated successfully.';
    }
}


if(isset($_POST['form7']))
{
    $valid = 1;

    if(empty($_POST['testimonial_title'])) {
        $valid = 0;
        $error_message .= 'Testimonial Title can not be empty<br>';
    }

    if(empty($_POST['testimonial_subtitle'])) {
        $valid = 0;
        $error_message .= 'Testimonial SubTitle can not be empty<br>';
    }

    $path = $_FILES['testimonial_photo']['name'];
    $path_tmp = $_FILES['testimonial_photo']['tmp_name'];

    if($path != '') {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }

    if($valid == 1) {


        if($path != '') {
            // removing the existing photo
            $statement = $pdo->prepare("SELECT * FROM tbl_setting_home WHERE id=1");
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
            foreach ($result as $row) {
                $testimonial_photo = $row['testimonial_photo'];
                unlink('../public_files/uploads/'.$testimonial_photo);
            }

            // updating the data
            $final_name = 'testimonial'.time().'.'.$ext;
            move_uploaded_file( $path_tmp, '../public_files/uploads/'.$final_name );

            // updating the database
            $statement = $pdo->prepare("UPDATE tbl_setting_home SET testimonial_title=?,testimonial_subtitle=?, testimonial_photo=? WHERE id=1");
            $statement->execute(array($_POST['testimonial_title'],$_POST['testimonial_subtitle'],$final_name));
        } else {
            // updating the database
            $statement = $pdo->prepare("UPDATE tbl_setting_home SET testimonial_title=?,testimonial_subtitle=? WHERE id=1");
            $statement->execute(array($_POST['testimonial_title'],$_POST['testimonial_subtitle']));
        }

        $success_message = 'Home (Testimonial) setting is updated successfully.';
        
    }
}


if(isset($_POST['form8']))
{
    $valid = 1;

    if(empty($_POST['blog_title'])) {
        $valid = 0;
        $error_message .= 'Blog Title can not be empty<br>';
    }

    if(empty($_POST['blog_subtitle'])) {
        $valid = 0;
        $error_message .= 'Blog SubTitle can not be empty<br>';
    }

    if($valid == 1) {
        $statement = $pdo->prepare("UPDATE tbl_setting_home SET blog_title=?,blog_subtitle=? WHERE id=1");
        $statement->execute(array($_POST['blog_title'],$_POST['blog_subtitle']));

        $success_message = 'Home (Blog) setting is updated successfully.';
    }
}

if(isset($_POST['form9']))
{
    $statement = $pdo->prepare("UPDATE tbl_setting_home SET newsletter_text=? WHERE id=1");
    $statement->execute(array($_POST['newsletter_text']));
    
    $success_message = 'Home (Newsletter Text) is updated successfully.';
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Setting - Home</h1>
    </div>
</section>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_home WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
    $home_service_on_off          = $row['home_service_on_off'];
    $home_welcome_on_off          = $row['home_welcome_on_off'];
    $home_featured_product_on_off = $row['home_featured_product_on_off'];
    $home_latest_product_on_off   = $row['home_latest_product_on_off'];
    $home_popular_product_on_off  = $row['home_popular_product_on_off'];
    $home_testimonial_on_off      = $row['home_testimonial_on_off'];
  

    $meta_title_home       = $row['meta_title_home'];
    $meta_keyword_home     = $row['meta_keyword_home'];
    $meta_description_home = $row['meta_description_home'];

    $cta_title          = $row['cta_title'];
    $cta_content        = $row['cta_content'];
    $cta_read_more_text = $row['cta_read_more_text'];
    $cta_read_more_url  = $row['cta_read_more_url'];
    $cta_photo          = $row['cta_photo'];

    $featured_product_title    = $row['featured_product_title'];
    $featured_product_subtitle = $row['featured_product_subtitle'];

    $latest_product_title    = $row['latest_product_title'];
    $latest_product_subtitle = $row['latest_product_subtitle'];

    $popular_product_title    = $row['popular_product_title'];
    $popular_product_subtitle = $row['popular_product_subtitle'];

    $testimonial_title    = $row['testimonial_title'];
    $testimonial_subtitle = $row['testimonial_subtitle'];
    $testimonial_photo    = $row['testimonial_photo'];

    $blog_title    = $row['blog_title'];
    $blog_subtitle = $row['blog_subtitle'];

    $newsletter_text = $row['newsletter_text'];
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

            <h3 class="s-title">Sections On and Off</h3>
            <form class="form-horizontal" action="" method="post">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Service Section </label>
                            <div class="col-sm-4">
                                <select name="home_service_on_off" class="form-control" style="width:auto;">
                                    <option value="1" <?php if($home_service_on_off == 1) {echo 'selected';} ?>>On</option>
                                    <option value="0" <?php if($home_service_on_off == 0) {echo 'selected';} ?>>Off</option>
                                </select>
                            </div>
                        </div>      
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Welcome Section </label>
                            <div class="col-sm-4">
                                <select name="home_welcome_on_off" class="form-control" style="width:auto;">
                                    <option value="1" <?php if($home_welcome_on_off == 1) {echo 'selected';} ?>>On</option>
                                    <option value="0" <?php if($home_welcome_on_off == 0) {echo 'selected';} ?>>Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Featured Product Section </label>
                            <div class="col-sm-4">
                                <select name="home_featured_product_on_off" class="form-control" style="width:auto;">
                                    <option value="1" <?php if($home_featured_product_on_off == 1) {echo 'selected';} ?>>On</option>
                                    <option value="0" <?php if($home_featured_product_on_off == 0) {echo 'selected';} ?>>Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Latest Product Section </label>
                            <div class="col-sm-4">
                                <select name="home_latest_product_on_off" class="form-control" style="width:auto;">
                                    <option value="1" <?php if($home_latest_product_on_off == 1) {echo 'selected';} ?>>On</option>
                                    <option value="0" <?php if($home_latest_product_on_off == 0) {echo 'selected';} ?>>Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Popular Product Section </label>
                            <div class="col-sm-4">
                                <select name="home_popular_product_on_off" class="form-control" style="width:auto;">
                                    <option value="1" <?php if($home_popular_product_on_off == 1) {echo 'selected';} ?>>On</option>
                                    <option value="0" <?php if($home_popular_product_on_off == 0) {echo 'selected';} ?>>Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Testimonial Section </label>
                            <div class="col-sm-4">
                                <select name="home_testimonial_on_off" class="form-control" style="width:auto;">
                                    <option value="1" <?php if($home_testimonial_on_off == 1) {echo 'selected';} ?>>On</option>
                                    <option value="0" <?php if($home_testimonial_on_off == 0) {echo 'selected';} ?>>Off</option>
                                </select>
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



            
            <h3 class="s-title">Meta Section</h3>
            <form class="form-horizontal" action="" method="post">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Meta Title </label>
                            <div class="col-sm-8">
                                <input type="text" name="meta_title_home" class="form-control" value="<?php echo $meta_title_home ?>">
                            </div>
                        </div>      
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Meta Keyword </label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="meta_keyword_home" style="height:100px;"><?php echo $meta_keyword_home ?></textarea>
                            </div>
                        </div>  
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Meta Description </label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="meta_description_home" style="height:200px;"><?php echo $meta_description_home ?></textarea>
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





            <h3 class="s-title">Call to Action Section</h3>
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">                                          
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Title<span>*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="cta_title" value="<?php echo $cta_title; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Content<span>*</span></label>
                            <div class="col-sm-8">
                                <textarea name="cta_content" class="form-control" cols="30" rows="10" style="height:120px;"><?php echo $cta_content; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Read More Text<span>*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="cta_read_more_text" value="<?php echo $cta_read_more_text; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Read More URL<span>*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="cta_read_more_url" value="<?php echo $cta_read_more_url; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Existing Call to Action Background</label>
                            <div class="col-sm-6" style="padding-top:6px;">
                                <img src="../public_files/uploads/<?php echo $cta_photo; ?>" class="existing-photo" style="height:80px;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">New Background</label>
                            <div class="col-sm-6" style="padding-top:6px;">
                                <input type="file" name="cta_photo">
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





            <h3 class="s-title">Featured Product Section</h3>
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">                                          
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Featured Product Title<span>*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="featured_product_title" value="<?php echo $featured_product_title; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Featured Product SubTitle<span>*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="featured_product_subtitle" value="<?php echo $featured_product_subtitle; ?>">
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


            <h3 class="s-title">Latest Product Section</h3>
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">                                          
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Latest Product Title<span>*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="latest_product_title" value="<?php echo $latest_product_title; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Latest Product SubTitle<span>*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="latest_product_subtitle" value="<?php echo $latest_product_subtitle; ?>">
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


            <h3 class="s-title">Popular Product Section</h3>
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">                                          
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Popular Product Title<span>*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="popular_product_title" value="<?php echo $popular_product_title; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Popular Product SubTitle<span>*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="popular_product_subtitle" value="<?php echo $popular_product_subtitle; ?>">
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


            
            <h3 class="s-title">Testimonial Section</h3>
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">                                          
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Testimonial Section Title<span>*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="testimonial_title" value="<?php echo $testimonial_title; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Testimonial Section SubTitle<span>*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="testimonial_subtitle" value="<?php echo $testimonial_subtitle; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Existing Testimonial Background</label>
                            <div class="col-sm-6" style="padding-top:6px;">
                                <img src="../public_files/uploads/<?php echo $testimonial_photo; ?>" class="existing-photo" style="height:80px;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">New Background</label>
                            <div class="col-sm-6" style="padding-top:6px;">
                                <input type="file" name="testimonial_photo">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success pull-left" name="form7">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>


            <h3 class="s-title">Newsletter Section</h3>
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">                                          
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Newsletter Text</label>
                            <div class="col-sm-8">
                                <textarea name="newsletter_text" class="form-control" cols="30" rows="10" style="height: 120px;"><?php echo $newsletter_text; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success pull-left" name="form9">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>


                
        </div>
    </div>

</section>

<?php require_once('footer.php'); ?>