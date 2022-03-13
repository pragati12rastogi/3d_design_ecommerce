<?php

ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();


if(isset($_REQUEST['currency_delete'])){

    $check_default = $pdo->prepare("SELECT * FROM tbl_setting_currency WHERE default_currency=1 and id=".$_REQUEST['currency_delete']);
    $check_default->execute();
    $default_arr = $check_default->fetchAll(PDO::FETCH_ASSOC);
    if(count($default_arr)>0){
        setcookie('admin_currency_error_alert', 'Cannot delete default currency', time() + 5);
        
    }else{
        $statement = $pdo->prepare("DELETE FROM tbl_setting_currency WHERE id=?");
        $statement->execute(array($_REQUEST['currency_delete']));

        setcookie('admin_currency_error_success', 'Currency deleted successfully', time() + 5);
        
    }
    
    header('location: setting-currency-list.php');
    exit();
    
}

if(isset($_REQUEST['advtab'])){
    $get_adv = $pdo->prepare("SELECT * from tbl_advertisement where adv_id=".$_REQUEST['advtab']);
    $get_adv->execute();
    $get_adv =$get_adv->fetch(PDO::FETCH_ASSOC);
    
    if(count($get_adv)>0){
        if($_REQUEST['advimg'] == 2){
            if(!empty($get_adv['adv_photo2'])) {
                unlink('../public_files/uploads/'.$get_adv['adv_photo2']);    
            }

            $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_photo2=? WHERE adv_id=?");
            $statement->execute(array(null,$_REQUEST['advtab']));
        }elseif($_REQUEST['advimg'] == 3){
            if(!empty($get_adv['adv_photo3'])) {
                unlink('../public_files/uploads/'.$get_adv['adv_photo3']);    
            }

            $statement = $pdo->prepare("UPDATE tbl_advertisement SET adv_photo3=? WHERE adv_id=?");
            $statement->execute(array(null,$_REQUEST['advtab']));
        }
    }

    header('location: advertisement.php');
    exit();
    
}

?>