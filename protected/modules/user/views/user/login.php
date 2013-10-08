<?php
$this->layout='none';
$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Login");
$this->breadcrumbs=array(
	UserModule::t("Login"),
);
?>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="backend">
    <meta name="author" content="brook">
    <link rel="shortcut icon" href="<?php echo yii::app()->theme->baseUrl ?>/assets/bootstrap/ico/favicon.png">

    <title><?php Yii::app()->name . ' - '.UserModule::t("Login")?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo yii::app()->theme->baseUrl ?>/assets/bootstrap/css/bootstrap.css" rel="stylesheet">
    
    <!-- Custom styles for this template -->
    <link href="<?php echo yii::app()->theme->baseUrl ?>/assets/css/signin.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="<?php echo yii::app()->theme->baseUrl ?>/assets/js/html5shiv.js"></script>
      <script src="<?php echo yii::app()->theme->baseUrl ?>/assets/js/respond.min.js"></script>
    <![endif]-->
  </head>
  
  
  
<?php if(Yii::app()->user->hasFlash('loginMessage')): ?>
<div class="success">
	<?php echo Yii::app()->user->getFlash('loginMessage'); ?>
</div>
<?php endif; ?>


  <body>
    <div class="container">
    <?php echo CHtml::beginForm('','post',array(
    		'class'=>"form-signin"
    )); ?>
        <h2 class="form-signin-heading">请登陆</h2>
        <?php echo CHtml::activeTextField($model,'username',array(
        		'class'=>"form-control",
        		'onfocus' => 'if(this.value=="邮箱/手机/用户名"){this.value=""};',
        		'type'=>"text",
        		'placeholder'=>"邮箱/手机/用户名",
        		'required'=>"required",
        		"autofocus"
        )) ?>
        <?php echo CHtml::activePasswordField($model,'password',array(
        		'type'=>"password",
        		'class'=>"form-control",
        		'placeholder'=>"密码：",
        		'required'=>"required",
        )) ?>
        <label class="checkbox">
          <?php echo CHtml::activeCheckBox($model,'rememberMe',array(
          		'type'=>"checkbox",
          		'value'=>"remember-me"
          )); ?>				
		  		<?php echo CHtml::activeLabelEx($model,'rememberMe'); ?>
        </label>
        <label><?php echo CHtml::errorSummary($model); ?></label>
        <?php echo CHtml::submitButton(UserModule::t("Login"),array(
        		'class'=>"btn btn-lg btn-primary btn-block",
        )); ?>
	<?php echo CHtml::endForm(); ?>
    </div> <!-- /container -->
<?php
$form = new CForm(array(
    'elements'=>array(
        'username'=>array(
            'type'=>'text',
			'class'=>'login_btn',
            'maxlength'=>32,
        ),
        'password'=>array(
            'type'=>'password',
            'maxlength'=>32,
        ),
        'rememberMe'=>array(
            'type'=>'checkbox',
        )
    ),

    'buttons'=>array(
        'login'=>array(
            'type'=>'submit',
            'label'=>'Login',
        ),
    ),
), $model);
?>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  </body>
</html>