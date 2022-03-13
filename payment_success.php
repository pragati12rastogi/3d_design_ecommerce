<?php require_once('header.php'); ?>
<html>
  <head>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
  </head>
    <style>
      body {
        text-align: center;
        background: #EBF0F5;
      }
        h1 {
          color: #88B04B;
          font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
          font-weight: 900;
          font-size: 40px;
          margin-bottom: 10px;
        }
        p {
          color: #404F5E;
          font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
          font-size:20px;
          margin: 0;
        }
        .checkmark-success {
        color: #9ABC66;
        font-size: 100px;
        line-height: 200px;
        margin-left:-15px;
      }
      .card-success {
        background: white;
        padding: 60px;
        border-radius: 4px;
        box-shadow: 0 2px 3px #C8D0D8;
        display: inline-block;
        margin: 50px auto;
      }
    </style>
    
      <div class="card-success">
      <div style="border-radius:200px; height:200px; width:200px; background: #F8FAF5; margin:0 auto;">
        <i class="checkmark-success">✓</i>
      </div>
        <h1>Success</h1>
        <h3 style="margin-top:20px;"><?php echo CONGRATULATION_PAYMENT_IS_SUCCESSFULL; ?></h3>
                     
        <p>We received your purchase request;<br/> we'll be in touch shortly!</p>
        <br>
        <br>
        <a href="dashboard.php" class="btn btn-success btn-block btn-lg"><?php echo BACK_TO_DASHBOARD; ?></a>
      </div>
   



<?php require_once('footer.php'); ?>