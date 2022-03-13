<?php
$statement = $pdo->prepare("SELECT * FROM tbl_advertisement WHERE adv_id=6");
$statement->execute();
$result = $statement->fetchAll();                            
foreach ($result as $row) {
    $adv_type = $row['adv_type'];
    $adv_photo = $row['adv_photo'];
    $adv_url = $row['adv_url'];
    $adv_adsense_code = $row['adv_adsense_code'];
}
?>
<h3><?php echo CATEGORIES; ?></h3>
    <div id="left" class="span3">

        <ul id="menu-group-1" class="nav menu">
            <?php
                $i=0;
                $statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE show_on_menu=1");
                $statement->execute();
                $result = $statement->fetchAll();
                foreach ($result as $row) {
                    $i++;
                    ?>
                    <li class="cat-level-1 deeper parent">
                        <a class="" href="product-category.php?id=<?php echo $row['tcat_id']; ?>&type=top-category">
                            <span data-toggle="collapse" data-parent="#menu-group-1" href="#cat-lvl1-id-<?php echo $i; ?>" class="sign"><i class="fa fa-plus"></i></span>
                            <span class="lbl"><?php echo $row['tcat_name']; ?></span>                      
                        </a>
                        <ul class="children nav-child unstyled small collapse" id="cat-lvl1-id-<?php echo $i; ?>">
                            <?php
                            $j=0;
                            $statement1 = $pdo->prepare("SELECT * FROM tbl_mid_category WHERE tcat_id=?");
                            $statement1->execute(array($row['tcat_id']));
                            $result1 = $statement1->fetchAll();
                            foreach ($result1 as $row1) {
                                $j++;
                                ?>
                                <li class="deeper parent">
                                    <a class="" href="product-category.php?id=<?php echo $row1['mcat_id']; ?>&type=mid-category">
                                        <span data-toggle="collapse" data-parent="#menu-group-1" href="#cat-lvl2-id-<?php echo $i.$j; ?>" class="sign"><i class="fa fa-minus"></i></span>
                                        <span class="lbl lbl1"><?php echo $row1['mcat_name']; ?></span> 
                                    </a>
                                    
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </li>
                    <?php
                }
            ?>
        </ul>

        <?php
        $statement = $pdo->prepare("SELECT * FROM tbl_setting_advertisement WHERE id=1");
        $statement->execute();
        $result = $statement->fetchAll();                           
        foreach ($result as $row) {
            $ads_category_sidebar_on_off = $row['ads_category_sidebar_on_off'];
        }
        ?>

        
        <?php if($ads_category_sidebar_on_off == 1): ?>
        <div class="ad-section pt_50">
            <?php 
                if($adv_type == 'Adsense Code') {
                    echo $adv_adsense_code;
                } else {
                    if($adv_url=='') {
                        echo '<img src="public_files/uploads/'.$adv_photo.'" alt="Advertisement">';
                    } else {
                        echo '<a href="'.$adv_url.'"><img src="public_files/uploads/'.$adv_photo.'" alt="Advertisement"></a>';
                    }                               
                }
            ?>
        </div>
        <?php endif; ?>

    </div>