<?php
require_once 'db_config.php'; 
require_once 'functions.php'; 
$is_admin = (authGetUserLevel($user) >= 2);

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Joomla Content 2 GetGrav</title>

<style>
.btn{background:#3498db;background-image:-webkit-linear-gradient(top, #3498db, #2980b9);background-image:-moz-linear-gradient(top, #3498db, #2980b9);background-image:-ms-linear-gradient(top, #3498db, #2980b9);background-image:-o-linear-gradient(top, #3498db, #2980b9);background-image:linear-gradient(to bottom, #3498db, #2980b9);-webkit-border-radius:28;-moz-border-radius:28;border-radius:28px;font-family:Arial;color:#ffffff;font-size:32px;padding:10px 20px 10px 20px;text-decoration:none}
.btnUrl{-moz-box-shadow:inset 0px 1px 0px 0px #a4e271;-webkit-box-shadow:inset 0px 1px 0px 0px #a4e271;box-shadow:inset 0px 1px 0px 0px #a4e271;background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #89c403), color-stop(1, #77a809));background:-moz-linear-gradient(top, #89c403 5%, #77a809 100%);background:-webkit-linear-gradient(top, #89c403 5%, #77a809 100%);background:-o-linear-gradient(top, #89c403 5%, #77a809 100%);background:-ms-linear-gradient(top, #89c403 5%, #77a809 100%);background:linear-gradient(to bottom, #89c403 5%, #77a809 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#89c403', endColorstr='#77a809',GradientType=0);background-color:#89c403;-moz-border-radius:6px;-webkit-border-radius:6px;border-radius:6px;border:1px solid #74b807;display:inline-block;cursor:pointer;color:#ffffff;font-family:Arial;font-size:15px;font-weight:bold;padding:6px 24px;text-decoration:none;text-shadow:0px 1px 0px #528009}.btnUrl:hover{background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #77a809), color-stop(1, #89c403));background:-moz-linear-gradient(top, #77a809 5%, #89c403 100%);background:-webkit-linear-gradient(top, #77a809 5%, #89c403 100%);background:-o-linear-gradient(top, #77a809 5%, #89c403 100%);background:-ms-linear-gradient(top, #77a809 5%, #89c403 100%);background:linear-gradient(to bottom, #77a809 5%, #89c403 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#77a809', endColorstr='#89c403',GradientType=0);background-color:#77a809}.btnUrl:active{position:relative;top:1px}
h2{color: #3498db;text-transform: uppercase;}
footer{margin-top: 20px;margin-bottom: 20px;float: right;}
</style>

</head>
<body>


<?php if(!$is_admin) { ?>
	<div>
		<p class='login_reg'>
		Login as super user in the frond-end. Then try again.
		<a class='btn' href='../index.php?option=com_users&amp;view=login' TARGET='_parent' id='btnLogin'>Login</a>
		</p>
	</div>
<? }



if($is_admin) {  // virtuemart_products_nl_nl
	
	$table = $dbprefix . 'content';
	$query = "SELECT * FROM $table ";
	
	$result = mysql_query($query) or die(mysql_error());


	if ($result > 0) {

		if ($_GET["type"]) {
			$fileType = $_GET["type"];
		} else {
			$fileType = 'default';
		}

    	?>
    	
    	<h1>Joomla 2 Grav CMS</h1>
        <h3 style='margin-bottom:0px;'>Options</h3><div style='width:800px; height:120px; border:1px solid #000;'>
			<a class='btnOption' href='markdown.php?type=blog' id='btnLogin'>markdown.php?type=blog      -> blog.md</a><br>
			<a class='btnOption' href='markdown.php?type=default' id='btnLogin'>markdown.php?type=blog      -> default.md</a><br>
			<a class='btnOption' href='markdown.php?type=internal' id='btnLogin'>markdown.php?type=blog      -> internal.md</a><br>
			<a class='btnOption' href='markdown.php?type=whatever' id='btnLogin'>markdown.php?type=blog      -> whatever.md</a><br>
			<p>If you don't use **?type=** the output file is default.md</p>
        </div>
    	<h2>Build markdown files</h2>
    	
    	
    	<?php
	    	
        echo "<h3 style='margin-bottom:0px;'>Added Files, Count(" . count($oFiles) . ")</h3><div style='overflow:auto; width:800px; height:120px; border:1px solid #000;'>";

		rrmdir('./markdown');


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
					$catFileName = $nrCat['title']; 
					$catDesc = removeTags( $nrCat['description'] );
					$catMetadesc = $nrCat['metadesc'];
					$catMetakey = $nrCat['metakey'];
					$catAlias = $nrCat['path'];
					
					$catImage = imageTags( $nrCat['params'] );
					$catCategory_layout = $catImage[0];
					$catImage_intro = $catImage[1];
					$catImage_intro_alt = $catImage[2];
					
					$catImageMD = $catImage[1];
					
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
---
published: $published
title: $title
date: '$created'
publish_date: '$created'
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
			

$catTextMD = <<<"EOD"
---
published: $published
title: $catName
date: '$created'
publish_date: '$created'
metadata:
     description: '$catMetadesc'
     keywords: '$catMetakey'
taxonomy:
     tag:
         - $catName
         $tagMD
slug: $catAlias
---
        
#$catName

$catDesc

$catCategory_layout
$catImage_intro
$catImage_intro_alt
$catImage_intro_caption
	
EOD;

			//echo $textMD . '<hr>';
			echo $title . ', ';

			safeFileMD($catName,$fileName,$textMD,$imageMD,$fileLanguage,$fileType);

			safeCatMD($catName,$catFileName,$catTextMD,$catImage_intro,$fileLanguage,$fileType);
		
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

<footer class="footer">
	
<a class='btnUrl' href='https://github.com/Syndes/Joomla2Grav' target="_blank" id='url'>GitHub</a>
</footer>


</body>
