<?php
require_once 'db_config.php'; 
require_once 'functions.php'; 
$is_admin = (authGetUserLevel($user) >= 0);

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Joomla Content 2 GetGrav</title>
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
	
		while($nr = mysql_fetch_array($result)){

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
			
			echo $textMD . '<hr>';

			safeFileMD($catName,$fileName,$textMD,$imageMD,$fileLanguage);
		
		}

		$result = mysql_query($query) or die(mysql_error());
	
	} else {
	    echo "0 resultaten";
		$report = "-- 0 resultaten --";
	}
	

} ?>

</body>
