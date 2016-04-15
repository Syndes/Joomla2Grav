<?php
	// Thanks for helping : http ://stac koverflow. com/que stions/4914750/how-to-zip-a-whole-folder-using-php/4914785
	
    // DIRECTORY WE WANT TO BACKUP
    $pathBase = './';  // Relate Path

    // ZIP FILE NAMING ... This currently is equal to = sitename_www_YYYY_MM_DD_backup.zip 
    $zipPREFIX = "markdown";
    $zipPOSTFIX = "joomla2markdown";
    $zipEXTENSION = ".zip";

    // SHOW PHP ERRORS... REMOVE/CHANGE FOR LIVE USE
    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(-1);




// ############################################################################################################################
//                                  NO CHANGES NEEDED FROM THIS POINT
// ############################################################################################################################

    // SOME BASE VARIABLES WE MIGHT NEED
    $iBaseLen = strlen($pathBase);
    $iPreLen = strlen($zipPREFIX);
    $iPostLen = strlen($zipPOSTFIX);
    $sFileZip = $pathBase . $zipPOSTFIX . $zipEXTENSION;
    $oFiles = array();
    $oFiles_Error = array();
    $oFiles_Previous = array();

    // SIMPLE HEADER ;)
    echo '<h2>Backup and download</h2>';

    // CHECK IF BACKUP ALREADY DONE
    if (file_exists($sFileZip)) {
        // IF BACKUP EXISTS... SHOW MESSAGE AND THATS IT
        echo "<h3 style='margin-bottom:0px;'>Backup Already Exists</h3><div style='width:800px; border:1px solid #000;'>";
            echo '<b>File Name: </b>',$sFileZip,'<br />';
            echo '<b>File Size: </b>',$sFileZip,'<br />';
        echo "</div>";
    } else {

        // NO BACKUP FOR TODAY.. SO START IT AND SHOW SCRIPT SETTINGS
        echo "<h3 style='margin-bottom:0px;'>Script Settings</h3><div style='width:800px; border:1px solid #000;'>";
            echo '<b>Backup Directory: </b>',$pathBase,'<br /> ';
            echo '<b>Backup Save File: </b>',$sFileZip,'<br />';
        echo "</div>";

        // CREATE ZIPPER AND LOOP DIRECTORY FOR SUB STUFF
        $oZip = new ZipArchive;
        $oZip->open($sFileZip,  ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $oFilesWrk = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pathBase),RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($oFilesWrk as $oKey => $eFileWrk) {
            // VARIOUS NAMING FORMATS OF THE CURRENT FILE / DIRECTORY.. RELATE & ABSOLUTE
            $sFilePath = substr($eFileWrk->getPathname(),$iBaseLen, strlen($eFileWrk->getPathname())- $iBaseLen);
            $sFileReal = $eFileWrk->getRealPath();
            $sFile = $eFileWrk->getBasename();

            // WINDOWS CORRECT SLASHES
            $sMyFP = str_replace('\\', '/', $sFileReal);

            if (file_exists($sMyFP)) {  // CHECK IF THE FILE WE ARE LOOPING EXISTS
                if ($sFile!="."  && $sFile!="..") { // MAKE SURE NOT DIRECTORY / . || ..
                    // CHECK IF FILE HAS BACKUP NAME PREFIX/POSTFIX... If So, Dont Add It,, List It
                    if (substr($sFile,0, $iPreLen)!=$zipPREFIX && substr($sFile,-1, $iPostLen + 4)!= $zipPOSTFIX.$zipEXTENSION) {
                        $oFiles[] = $sMyFP;                     // LIST FILE AS DONE
                        $oZip->addFile($sMyFP, $sFilePath);     // APPEND TO THE ZIP FILE
                    } else {
                        $oFiles_Previous[] = $sMyFP;            // LIST PREVIOUS BACKUP
                    }
                }
            } else {
                $oFiles_Error[] = $sMyFP;                       // LIST FILE THAT DOES NOT EXIST
            }
        }
        $sZipStatus = $oZip->getStatusString();                 // GET ZIP STATUS
        $oZip->close(); // WARNING: Close Required to append files, dont delete any files before this.


        // SHOW BACKUP STATUS / FILE INFO
        echo "<h3 style='margin-bottom:0px;'>Backup Stats</h3><div style='width:800px; height:120px; border:1px solid #000;'>";
            echo "<b>Zipper Status: </b>" . $sZipStatus . "<br />";
            echo "<b>Finished Zip Script: </b>",$sFileZip,"<br />";
            echo "<b>Zip Size: </b>",human_filesize($sFileZip),"<br />";
        echo "</div>";


        // SHOW ANY PREVIOUS BACKUP FILES
        echo "<h3 style='margin-bottom:0px;'>Previous Backups Count(" . count($oFiles_Previous) . ")</h3><div style='overflow:auto; width:800px; height:120px; border:1px solid #000;'>";
        foreach ($oFiles_Previous as $eFile) {
            echo basename($eFile) . ", Size: " . human_filesize($eFile) . "<br />";
        }
        echo "</div>";

        // SHOW ANY FILES THAT DID NOT EXIST??
        if (count($oFiles_Error)>0) {
            echo "<h3 style='margin-bottom:0px;'>Error Files, Count(" . count($oFiles_Error) . ")</h3><div style='overflow:auto; width:800px; height:120px; border:1px solid #000;'>";
            foreach ($oFiles_Error as $eFile) {
                echo $eFile . "<br />";
            }
            echo "</div>";
        }

        // SHOW ANY FILES THAT HAVE BEEN ADDED TO THE ZIP
        echo "<h3 style='margin-bottom:0px;'>Added Files, Count(" . count($oFiles) . ")</h3><div style='overflow:auto; width:800px; height:120px; border:1px solid #000;'>";
        foreach ($oFiles as $eFile) {
            echo $eFile . "<br />";
        }
        echo "</div>";
    }

    // CONVERT FILENAME INTO A FILESIZE AS Bytes/Kilobytes/Megabytes,Giga,Tera,Peta
    function human_filesize($sFile, $decimals = 2) {
        $bytes = filesize($sFile);
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
?>
