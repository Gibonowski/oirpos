<?php
include 'wideimage/lib/WideImage.php';
$w = 200;
$folder = 'img/tuski/';
$prefix = 'miniaturki/m_';

# ./tmp/ directory must have web server write access ( chmod 777 ./tmp )
$obrazek = TRUE;
$wiadomosc;
$output_image_path = null;
$img = null;
if ( isset( $_POST['generate'] ) ) { 

	include 'Meme_generator.php';
	include 'ImageResize.php';
	
	$mg = new Meme_generator();

	// OR YOU CAN LOAD WHOLE CONFIG FROM ONE ARRAY
	// possible keys: 
        // * meme_font / meme_output_dir / meme_font_to_image_ratio / meme_margins_to_image_ratio / meme_image_path / meme_top_text / 
        // * meme_bottom_text / meme_font
        // * meme_watermark_file / meme_watermark_margins / meme_watermark_opacity
	// $config['meme_image_path'] = './tmp/philosoraptor.jpg';
	// $config['meme_top_text'] = 'What if I\'m just a meme generating other memes?';
	// $config['meme_bottom_text'] = 'We need to go deeper.. (UTF test: £ zażółćg ęślą jaźń ‹›';
	// $config['meme_font'] = './fonts/DejaVuSansMono-Bold.ttf'; 
	// $config['meme_output_dir'] = './tmp/';
	// $mg->load_config( $config );
	if (isset($_POST['plik'])){

		$example_image_path = $_POST['plik'];

	}
		
	elseif(file_exists($_FILES['myfile']['tmp_name']) || is_uploaded_file($_FILES['myfile']['tmp_name'] ) ) {

		$example_image_path = $_FILES['myfile']['tmp_name'];

	}
	
	else {
		if((!file_exists($_FILES['myfile']['tmp_name']) || !is_uploaded_file($_FILES['myfile']['tmp_name'] )) && strlen($_POST['adres']) == 0) {
			$obrazek = FALSE;
			$wiadomosc = "Nie podano adresu ani nie wybrano obrazka!";
		}

		elseif(filter_var($_POST['adres'], FILTER_VALIDATE_URL) === FALSE){
			$obrazek = FALSE;
			$wiadomosc = "Adres ma nieprawidłowy format!";
		}

		else{
			if(getimagesize($_POST['adres'])){
				$img = WideImage::load($_POST['adres']);
				$img->saveToFile('url.png');
				$example_image_path = 'url.png';
			}
			else {
				$obrazek = FALSE;
				$wiadomosc = "Nie ma obrazka!";
			}
		}
	}

	if ($obrazek == TRUE){
		$size = getimagesize($example_image_path);
		$size[1] = $size[1] * 0.04;

		$image = new ImageResize('img/znak wodny.png');
		$image->resizeToHeight($size[1]);
		$image->save('img/watermark.png', IMAGETYPE_PNG);

		$ratio = $_POST['points'];
		$ratio /= 1000;

		$_POST['top_text'] = mb_convert_case($_POST['top_text'], MB_CASE_UPPER, "UTF-8");
		$_POST['bottom_text'] = mb_convert_case($_POST['bottom_text'], MB_CASE_UPPER, "UTF-8");
	
		// Output image
		$mg->set_top_text( $_POST['top_text'] );
		$mg->set_bottom_text( $_POST['bottom_text'] );
		$mg->set_output_dir( './tusk/' ); // default to ./ if not set
		$mg->set_image( $example_image_path );
		$mg->set_watermark( 'img/watermark.png' );
		$mg->set_watemark_opacity(30);
		$mg->set_watermark_margins(3);
		$mg->set_font_ratio( $ratio );
		$mg->set_margins_ratio( 0.04 );
		$output_image_path = $mg->generate();
		if($example_image_path == "url.png")
			unlink('url.png');
	}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Generator Tusków</title>
<link rel="Shortcut icon" href="img/ico.jpeg" />
<meta name="author" content="Michał Gibas" />
<meta name="Expires" content="0" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta name="description" content="Generator memów &quot;Angielski z Tuskiem&quot;" /> 
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
#usuwanie starych obrazków
{
$dir = "tusk/"; 
$dzis=time();
foreach (glob($dir."*") as $file) {
	$czas = ($dzis-filemtime($file))/60/60/24;
	if($czas > 7) unlink($file);
	}
}
?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/pl_PL/sdk.js#xfbml=1&version=v2.5&appId=165979920117811";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<script type="text/javascript">
function showHidden(obj){
obj = document.getElementById(obj);
obj.style.display == 'none' ? obj.style.display = '' : obj.style.display = 'none';
}
</script>

<center>
<h2>GENERATOR TUSKÓW</h2>
<h3>Użyj gotowego obrazka:</h3>
<form method="post" action="" enctype="multipart/form-data">
<a id="link1_1" href="javascript: showHidden('hidden1');showHidden('link1_1');showHidden('link1_2');">Rozwiń</a>
<a id="link1_2" style="display:none;" href="javascript: showHidden('hidden1');showHidden('link1_1');showHidden('link1_2');">Zwiń</a>
<div id="hidden1" style="display:none">
<table>
<?php
	$files = glob($folder.'*.{jpg,png,gif}', GLOB_BRACE);
	$i = 1;
	foreach($files as $file)
	{
		if($i % 4 == 1)
		{
			echo '<tr align="center">'."\n";
		}		
		echo '<td>'."\n";
		$path = $folder.''.$prefix.''.$i.'.jpg';
		$normal = $folder.''.$i.'.jpg';
		echo '<label for="'.$i.'"><img src="'.$path.'" /></label><br><input type="radio" name="plik"	value="'.$normal.'" id="'.$i.'">'."\n";
		echo '</td>'."\n";
		if($i % 4 == 0)
		{
			echo '</tr>'."\n";
		}
		$i++;
	}
?>
</table>
</div>

<form method="post" action="" enctype="multipart/form-data">
<p>
<h3>Albo użyj własnego:</h3>
<input type="text" name="adres" placeholder="podaj adres URL" />
 lub 
<input type="file" name="myfile" />
</p>
<p>
Górny tekst: 
<input type="text" name="top_text" style="width:400px;  text-transform:uppercase" />
</p>
<p>
Dolny tekst:
<input type="text" name="bottom_text" style="width:400px; text-transform:uppercase;" onblur="caps(this.id)" />
</p>
<p>
Rozmiar czcionki:
</p>
<p>
<input type="range" name="points" min="20" max="130" value="65" step="1" style="width: 300px; height: 25px;">
</p>
<p>
<input type="submit" name="generate" style="font-size:2.5em;" value="Wygeneruj Tuska! &raquo;" />
</p>
</form>

<?php
if ($obrazek == FALSE) echo '<h1><font color="red">' . $wiadomosc . '</font></h1>';

if ( isset( $output_image_path ) && strlen( $output_image_path ) )  {
	$url = "tusk/$output_image_path";
	echo '<h2>Twój Tusk:</h2>';
	echo '<p><img src="'.$url.'" alt="" height="200"/></p>';
	echo '<table><tr align="right"><td>';
	echo 'Link bezpośredni: ';
	echo '</td><td>';
	echo '<a href="'.$url.'">http://donek.tk/'.$url.'</a>';
	echo '</td></tr><tr><td>';
	echo 'Pobiez na komputer: ';
	echo '</td><td>';
	echo '<a href="'.$url.'" download="'.$output_image_path.'">Klik</a>';
	echo '</td></tr></table>';
	echo '<br><br>';
}
?>

<div class="fb-page" data-href="https://www.facebook.com/englishwithtusk/" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true">
<div class="fb-xfbml-parse-ignore">
<blockquote cite="https://www.facebook.com/englishwithtusk/">
<a href="https://www.facebook.com/englishwithtusk/">Angielski z Tuskiem</a>
</blockquote>
</div>
</div>

<address>
&copy; Gib Gibon skład joł!&trade;&reg;
</address>
</center>
</body>
</html>