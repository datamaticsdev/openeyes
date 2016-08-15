<meta charset="utf-8" />
<?php
//Because the wonderful way the namespace is created means if you don't include your file in the assets template
//the namespace doesn't exist and gets overwritten.
?>
<script type="text/javascript">var OpenEyes = OpenEyes || {};</script>
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<meta name="viewport" content="width=1230, user-scalable=1" />
<meta name="format-detection" content="telephone=no">

<?php if (Yii::app()->params['disable_browser_caching']) {?>
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="pragma" content="no-cache" />
<?php }?>


<link rel="apple-touch-icon" sizes="180x180" href="<?= Yii::app()->createUrl('/apple-touch-icon.png?v=9Bayz8PxAO') ?>">
<link rel="icon" type="image/png" href="<?= Yii::app()->createUrl('/favicon-32x32.png?v=9Bayz8PxAO');?>" sizes="32x32">
<link rel="icon" type="image/png" href="<?= Yii::app()->createUrl('/favicon-16x16.png?v=9Bayz8PxAO');?>" sizes="16x16">
<link rel="manifest" href="<?= Yii::app()->createUrl('/manifest.json?v=9Bayz8PxAO');?>">
<link rel="mask-icon" href="<?= Yii::app()->createUrl('/safari-pinned-tab.svg?v=9Bayz8PxAO');?>" color="#3e4edb">
<link rel="shortcut icon" href="<?= Yii::app()->createUrl('/favicon.ico?v=9Bayz8PxAO');?>">
<meta name="theme-color" content="#ffffff">

<script type="text/javascript">
	var baseUrl = '<?php echo Yii::app()->baseUrl?>';
</script>