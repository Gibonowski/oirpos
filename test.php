<?php
include 'wideimage/lib/WideImage.php';
	WideImage::load('tusk.jpg')->resize(250)->output('jpg');
?>
