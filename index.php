<?php
require_once('JSMin.php');
require_once('combine.class.php');

$jsFiles    = array('jquery', 'js');
$combineJs  = CombineFiles::Fetch('scripts', 'cache', $jsFiles, 'js');

$cssFiles    = array('reset', 'css');
$combineCss  = CombineFiles::Fetch('css', 'cache', $cssFiles, 'css');

?>
<html>
<head>
  <link href='<?=$combineCss?>' type='text/css' rel='stylesheets' />
  <script src="<?=$combineJs?>"></script>
  <title>Lorem...</title>
</head>
<body>

</body>
</html>