<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $tnc_title = $row['tnc_title'];
    $tnc_banner = $row['tnc_banner'];
    $tnc_content = $row['tnc_content'];
}
?>

<div class="page-banner" style="background-image: url(public_files/uploads/<?php echo $tnc_banner; ?>);">
	<div class="inner">
		<h1><?php echo $tnc_title; ?></h1>
	</div>
</div>

<div class="page">
	<div class="container">
		<div class="row">            
            <div class="col-md-7">
                <div class="tnc">
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo $tnc_content; ?>
                        </div>
                    </div>
                </div>
			</div>
            <div class="col-md-5">
                <img src="public_files/img/tnc.jpg" width="100%" id="side-img">
            </div>
		</div>
	</div>
</div>
<style>
    #side-img {
    -webkit-animation: mover 2s infinite  alternate;
    animation: mover 2s infinite  alternate;
    }
    @-webkit-keyframes mover {
        0% { transform: translateY(80px); }
        100% { transform: translateY(0); }
    }
    @keyframes mover {
        0% { transform: translateY(80px); }
        100% { transform: translateY(0); }
    }
</style>
<?php require_once('footer.php'); ?>