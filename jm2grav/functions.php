<?php

function str_file_filter(
	    $str,
	    $sep = '_',
	    $strict = false,
	    $trim = 248) {
	
	    $str = strip_tags(htmlspecialchars_decode(strtolower($str))); // lowercase -> decode -> strip tags
	    $str = str_replace("%20", ' ', $str); // convert rogue %20s into spaces
	    $str = preg_replace("/%[a-z0-9]{1,2}/i", '', $str); // remove hexy things
	    $str = str_replace("&nbsp;", ' ', $str); // convert all nbsp into space
	    $str = preg_replace("/&#?[a-z0-9]{2,8};/i", '', $str); // remove the other non-tag things
	    $str = preg_replace("/\s+/", $sep, $str); // filter multiple spaces
	    $str = preg_replace("/\.+/", '', $str); // filter multiple periods
	    $str = preg_replace("/^\.+/", '', $str); // trim leading period
	
	    if ($strict) {
	        $str = preg_replace("/([^\w\d\\" . $sep . ".])/", '', $str); // only allow words and digits
	    } else {
	        $str = preg_replace("/([^\w\d\\" . $sep . "\[\]\(\).])/", '', $str); // allow words, digits, [], and ()
	    }
	
	    $str = preg_replace("/\\" . $sep . "+/", $sep, $str); // filter multiple separators
	    $str = substr($str, 0, $trim); // trim filename to desired length, note 255 char limit on windows
	
    return $str;
}


function str_file(
	    $str,
	    $sep = '_',
	    $ext = '',
	    $default = '',
	    $trim = 248) {
	
	    // Run $str and/or $ext through filters to clean up strings
	    $str = str_file_filter($str, $sep);
	    $ext = '' . str_file_filter($ext, '', true);
	
	    // Default file name in case all chars are trimmed from $str, then ensure there is an id at tail
	    if (empty($str) && empty($default)) {
	        $str = 'no_name__' . date('Y-m-d_H-m_A') . '__' . uniqid();
	    } elseif (empty($str)) {
	        $str = $default;
	    }
	
    // Return completed string
    if (!empty($ext)) {
        return $str . $ext;
    } else {
        return $str;
    }
}

function removeTags($str) {  

		$str = str_replace('<ul>', '', $str); 
		$str = str_replace('</ul>', '', $str); 
		$str = str_replace('<li>', '- ', $str); 
		$str = str_replace('</li>', '', $str); 
		$str = str_replace('<p>', '', $str); 
		$str = str_replace('</p>', '', $str); 
	
		$str = str_replace('<i>', '**', $str); 
		$str = str_replace('</i>', '**', $str); 
		$str = str_replace('<b>', '*', $str); 
		$str = str_replace('</b>', '*', $str); 
		
		$str = str_replace('<h1>', '##', $str); 
		$str = str_replace('</h1>', '', $str); 
		$str = str_replace('<h2>', '###', $str); 
		$str = str_replace('</h2>', '', $str); 
		$str = str_replace('<h3>', '####', $str); 
		$str = str_replace('</h3>', '', $str); 
		$str = str_replace('<h4>', '#####', $str); 
		$str = str_replace('</h4>', '', $str); 
		$str = str_replace('<h5>', '######', $str); 
		$str = str_replace('</h5>', '', $str); 
	
		$str = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $str );
		$str = preg_replace("|<a[^>]*href\s*=\s*([\"'])([^\"']*)\\1[^>]*>[^<]*</a>|si", "$2", $str);
		$str = preg_replace("/<img[^>]+\>/i", "-----(image)----- ", $str);

		//Strip all tags from description except these
		$str = strip_tags($str, '');
		
	return $str;
}


function imageTags($image) {  
		$image = str_replace('"', '', $image); 
		$image = str_replace('{', '', $image); 
		$image = str_replace('}', '', $image); 
		$image = str_replace('\/', '/', $image); 
		$image = str_replace(':', ': ', $image); 
		$image=explode(",",$image);

	return $image;
}

function getTag ($metakey) {
	$tags=explode(",",$metakey);
	$i = 0;
		
		foreach($tags as $tag) {
			if($i == 0) {
				$tagMD .= "- " . $tag . "\n";
			} else {
			$tagMD .= "         -" . $tag . "\n";
			}
			$i++;
		}
	
	return $tagMD;
}


function rrmdir($dir) { 
  foreach(glob($dir . '/*') as $file) { 
    if(is_dir($file)) 
      rrmdir($file); 
    else 
      unlink($file); 
  } rmdir($dir); 
}


function safeFileMD($catName,$fileName,$textMD,$imageMD,$fileLanguage,$fileType,$nrFileName) {
	
	$catName = str_file($catName);
	$fileName = $nrFileName . '.' . str_file($fileName);
	$fileType = $fileType;
	$fileLanguage = $fileLanguage;
				
	$folderMD = './markdown/' . $catName;
	$fileFolderMD = './markdown/' . $catName . '/'. $fileName;
	$fileNameMD = $folderMD . '/' . $fileName . '/' . $fileType . $fileLanguage . ".md";	

	$imagePath = pathinfo( $imageMD );
	$imageName = $imagePath['filename'] . '.' . $imagePath['extension'];
	
	$imageSource = str_replace("image_fulltext: ", '../', $imageMD);
	$imageTarget = $fileName = $folderMD . '/' . $fileName . '/' . $imageName;
	
	//make folder
	if (!file_exists($folderMD)) { mkdir($folderMD, 0777, true); }
	if (!file_exists($fileFolderMD)) { mkdir($fileFolderMD, 0777, true); }

	//copy image
	copy($imageSource,$imageTarget);

	//make file
	$textMD = $textMD;
	$Saved_File = fopen($fileNameMD, 'w');
	fwrite($Saved_File, $textMD);
	fclose($Saved_File);

    return;

}



function safeCatMD($catName,$catFileName,$catTextMD,$catImage_intro,$fileLanguage,$fileType) {
	
	$catName = str_file($catName);
	$fileType = $fileType;
	$fileLanguage = $fileLanguage;
				
	$catFolderMD = './markdown/' . $catName;
	$catFileFolderMD = './markdown/' . $catName . '/'. $catFileName;
	$catFileNameMD = $catFolderMD . '/' .  $fileType . $fileLanguage . ".md";	

	$catImagePath = pathinfo( $catImage_intro );
	$catImageName = $catImagePath['filename'] . '.' . $catImagePath['extension'];
	
	$catImageSource = str_replace("image: ", '../', $catImage_intro);
	$catImageTarget = $catFileName = $catFolderMD . '/' . $catImageName;
	
	//copy image
	copy($catImageSource,$catImageTarget);

	//make file
	$catTextMD = $catTextMD;
	$catSaved_File = fopen($catFileNameMD, 'w');
	fwrite($catSaved_File, $catTextMD);
	fclose($catSaved_File);

    return;

}




?>
