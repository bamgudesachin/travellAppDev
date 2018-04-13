<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Travel App</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo site_url();?>assets/examples-bootstrap/bootstrap.min.css" rel="stylesheet" />

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo site_url();?>assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="<?php echo site_url();?>assets/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo site_url();?>assets/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="<?php echo site_url();?>assets/font-awesome-4.2.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>

      #background{ // background:url(assets/img/home/m20.PNG) no-repeat ;
        //background:url(assets/img/home/m19.JPEG);
                    background-repeat: no-repeat;
                    //padding:35px;
                    //background-origin: content;
                    width: 100%;
                    height: 100%;
                 }   

    </style>


</head>
<body id="background" >
    <!-- NAVBAR
    ================================================== -->
    <div class="container">
        <!-- Use a container to wrap the slider, the purpose is to enable slider to always fit width of the wrapper while window resize -->
        <div class="container " style="margin-top:30px;">
            <div class="col-md-5 col-md-offset-3" style="">

                <h1 class="text-center" style="font-family:Times New Roman, Times, serif;color:#3399FF;text-shadow: 1px 1px #FF0000;">Travel App</h1>

                    <?php if (validation_errors()) {?>
                        <div class="alert alert-danger">
                              <a class="close" data-dismiss="alert" href="components-popups.html#" aria-hidden="true">×</a>
                              <strong><?= validation_errors();  ?></strong>
                        </div> 
                    <?php } ?>

                        <div class="login-panel panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Password Reset</h3>
                            </div>
                            <div class="panel-body" style="background-color:#A9D0F5">
                            <?php if(!$message){?>
                                <form role="form"  method="post" action="<?php echo site_url();?>admin/PasswordReset/updatePassword">
                                    <fieldset>
                                         <input type="hidden" name="user_id" placeholder="User Id" class="form-control" value="<?php echo $user_id;?>" >

                                        <div class="form-group">
                                            <input class="form-control" placeholder="New password" name="password" type="password" autofocus value="<?php echo set_value('password')?>">
                                        </div>
                                        <div class="form-group">
                                            <input class="form-control" placeholder="Confirm Password" name="cpassword" type="password" value="<?php echo set_value('cpassword')?>">
                                        </div>
                                        <input type="submit" class="btn btn-lg btn-success btn-block" >
                                    </fieldset>
                                </form>
                            <?php }else{?>    
                                <h3><?php echo $message;?></h3>
                            <?php }?>    
                            </div>
                        </div>
            </div>
        </div>

        <!-- Marketing messaging and featurettes
        ================================================== -->
        <!-- Wrap the rest of the page in another container to center all the content. -->

          <!--   <hr class="featurette-divider"> -->

            <!-- /END THE FEATURETTES -->
            <!-- FOOTER -->
            <footer>
                <!--<p class="pull-right"><a href="#">Back to top</a></p>
                <p>&copy; Jssor Slider 2009 - 2014. &middot; <a href="#">Privacy</a> &middot; </p>
            -->
            </footer>

    </div><!-- /.container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="<?php echo site_url();?>assets/js1/jquery-1.9.1.min.js"></script>
    <script src="<?php echo site_url();?>assets/examples-bootstrap/bootstrap.min.js"></script>
    <script src="<?php echo site_url();?>assets/examples-bootstrap/docs.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="<?php echo site_url();?>assets/examples-bootstrap/ie10-viewport-bug-workaround.js"></script>

    <!-- jssor slider scripts-->
    <!-- use jssor.js + jssor.slider.js instead for development -->
    <!-- jssor.slider.mini.js = (jssor.js + jssor.slider.js) -->
    



 

    <!-- jQuery -->
    <script src="<?php echo site_url();?>assets/js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo site_url();?>assets/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?php echo site_url();?>assets/js/plugins/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?php echo site_url();?>assets/js/sb-admin-2.js"></script>

</body>
</html>