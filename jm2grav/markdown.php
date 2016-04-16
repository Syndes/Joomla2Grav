<?php
require_once 'db_config.php'; 
require_once 'functions.php'; 
$is_admin = (authGetUserLevel($user) >= 0);

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Joomla Content 2 GetGrav</title>

<style>
.btn{background:#3498db;background-image:-webkit-linear-gradient(top, #3498db, #2980b9);background-image:-moz-linear-gradient(top, #3498db, #2980b9);background-image:-ms-linear-gradient(top, #3498db, #2980b9);background-image:-o-linear-gradient(top, #3498db, #2980b9);background-image:linear-gradient(to bottom, #3498db, #2980b9);-webkit-border-radius:28;-moz-border-radius:28;border-radius:28px;font-family:Arial;color:#ffffff;font-size:32px;padding:10px 20px 10px 20px;text-decoration:none}
h2 {color: #3498db;text-transform: uppercase;}

</style>

</head>
<body>


<?php if(!$is_admin) { ?>
	<div>
		<p class='login_reg'>
		Login en open deze pagina opnieuw
		<a class='login' href='../index.php?option=com_users&amp;view=login' TARGET='_parent' id='btnLogin'>Login</a>
		</p>
	</div>
<? }



if($is_admin) {  // virtuemart_products_nl_nl
	
	$table = $dbprefix . 'content';
	$query = "SELECT * FROM $table ";
	
	$result = mysql_query($query) or die(mysql_error());


	if ($result > 0) {

    	echo '<h2>Build markdown files</h2>';
        echo "<h3 style='margin-bottom:0px;'>Added Files, Count(" . count($oFiles) . ")</h3><div style='overflow:auto; width:800px; height:120px; border:1px solid #000;'>";

		rrmdir('./markdown');

		if ($_GET["type"]) {
			$fileType = $_GET["type"];
		} else {
			$fileType = 'default';
		}

		while($nr = mysql_fetch_array($result)){


			//domain.com/markdown.php?type=blog
			//domain.com/markdown.php?type=default
			//domain.com/markdown.php?type=internal
			

			
			$tableCat = $dbprefix . 'categories';
			$catID = $nr['catid'];
			$queryCat = "SELECT * FROM $tableCat  WHERE `id` = $catID ";
			$resultCat = mysql_query($queryCat) or die(mysql_error());

			if ($resultCat > 0) {
				while($nrCat = mysql_fetch_array($resultCat)){
					$catName = $nrCat['title'];
				}
				$resultCat = mysql_query($queryCat) or die(mysql_error());
			}

			if (!$nr['state']) {$published = 'false';} else {$published = 'true';}
			$title = $nr['title'];
			$fileName = $nr['title']; 
			$created = $nr['created'];
			$metadesc = $nr['metadesc'];
			$metakey = $nr['metakey'];
			$alias = $nr['alias'];
			$tagMD = getTag($metakey);

			$language = substr($nr['language'], 0, strpos($nr['language'], '-'));
			if ($language == '*') {$fileLanguage = '';}
			elseif ($language == '') {$fileLanguage = '';}
			else {$fileLanguage = '.' . $language;}
			
			$introtext = removeTags( $nr['introtext'] );
			$fulltext = removeTags( $nr['fulltext'] );
			
			$image = imageTags( $nr['images'] );
			$image_intro = $image[0];
			$float_intro = $image[1];
			$image_intro_alt = $image[2];
			$image_intro_caption = $image[3];
			$image_fulltext = $image[4];
			$float_fulltext = $image[5];
			$image_fulltext_alt = $image[6];
			$image_fulltext_caption = $image[7];
			
			$imageMD = $image[4];

$textMD = <<<"EOD"
published: $published
title: $title
date: $created
publish_date: $created
metadata:
     description: '$metadesc'
     keywords: '$metakey'
taxonomy:
     tag:
         - joomla
         - wordpress
         - getgrav
         - cms
         - $catName
         $tagMD
     category:
         - $catName
slug: $alias
---        
        
#$title
$introtext $fulltext

$image_intro
$float_intro
$image_intro_alt
$image_intro_caption
$image_fulltext
$float_fulltext
$image_fulltext_alt
$image_fulltext_caption
	
EOD;
			
			//echo $textMD . '<hr>';
			echo $title . ', ';

			safeFileMD($catName,$fileName,$textMD,$imageMD,$fileLanguage,$fileType);
		
		}
        echo "</div>";

		$result = mysql_query($query) or die(mysql_error());

		// Backup folder
		include 'backup.php';

		echo '<h2>You may download the markdown zip file.</h2>';

		echo '<a class="btn" href="' . $sFileZip . '">Download file</a>'; //$eFile		
		
	
	} else {
	    echo "0 resultaten";
		$report = "-- 0 resultaten --";
		echo '<h2>Sorry now content. Something went wrong maybe.</h2>';

	}
	

} ?>

</body>
