<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();                           
foreach ($result as $row) {
    $banner_forget_password = $row['banner_forget_password'];
}
?>

<div class="page-banner" style="background-color:#444;background-image: url(public_files/uploads/<?php echo $banner_forget_password; ?>);">
    <div class="inner">
        <h1><?php echo CHANGE_PASSWORD; ?></h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <?php echo PASSWORD_RESET_SUCCESS_MESSAGE; ?><br><br>
                    <a href="<?php echo BASE_URL; ?>login.php" style="color:#e4144d;font-weight:bold;"><?php echo CLICK_HERE_TO_LOGIN; ?></a>
                </div>                
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>