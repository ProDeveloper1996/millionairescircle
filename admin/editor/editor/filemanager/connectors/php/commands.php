<?php
/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2009 Frederico Caldeira Knabben
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 * This is the File Manager Connector for PHP.
 */

function GetFolders( $resourceType, $currentFolder )
{
	// Map the virtual path to the local server path.
	$sServerDir = ServerMapFolder( $resourceType, $currentFolder, 'GetFolders' ) ;

	// Array that will hold the folders names.
	$aFolders	= array() ;

	$oCurrentFolder = opendir( $sServerDir ) ;

	while ( $sFile = readdir( $oCurrentFolder ) )
	{
		if ( $sFile != '.' && $sFile != '..' && is_dir( $sServerDir . $sFile ) )
			$aFolders[] = '<Folder name="' . ConvertToXmlAttribute( $sFile ) . '" />' ;
	}

	closedir( $oCurrentFolder ) ;

	// Open the "Folders" node.
	echo "<Folders>" ;

	natcasesort( $aFolders ) ;
	foreach ( $aFolders as $sFolder )
		echo $sFolder ;

	// Close the "Folders" node.
	echo "</Folders>" ;
}

function GetFoldersAndFiles( $resourceType, $currentFolder )
{
	// Map the virtual path to the local server path.
	$sServerDir = ServerMapFolder( $resourceType, $currentFolder, 'GetFoldersAndFiles' ) ;

	// Arrays that will hold the folders and files names.
	$aFolders	= array() ;
	$aFiles		= array() ;

	$oCurrentFolder = opendir( $sServerDir ) ;

	while ( $sFile = readdir( $oCurrentFolder ) )
	{
		if ( $sFile != '.' && $sFile != '..' )
		{
			if ( is_dir( $sServerDir . $sFile ) )
				$aFolders[] = '<Folder name="' . ConvertToXmlAttribute( $sFile ) . '" />' ;
			else//
			{
				@$WH = @getimagesize($sServerDir . $sFile );
		//		if($WH === false) continue;
				$iFileSize = @filesize( $sServerDir . $sFile ) ;
				if ( !$iFileSize ) {
					$iFileSize = 0 ;
				}
				if ( $iFileSize > 0 )
				{
					$iFileSize = round( $iFileSize / 1024 ) ;
					if ( $iFileSize < 1 ) $iFileSize = 1 ;
				}

				$tmpWH = ($WH !== false) ? $WH[0].' x '. $WH[1] : '';
				$aFiles[] = '<File name="' . ConvertToXmlAttribute( $sFile ) . '" size="' . $iFileSize . '" WH="'. $tmpWH .'" />' ;
				//if($WH !== false) exit($aFiles[0]);
			}
		}
	}

	// Send the folders
	natcasesort( $aFolders ) ;
	echo '<Folders>' ;

	foreach ( $aFolders as $sFolder )
		echo $sFolder ;

	echo '</Folders>' ;

	// Send the files
	natcasesort( $aFiles ) ;
	echo '<Files>' ;

	foreach ( $aFiles as $sFiles )
		echo $sFiles ;

	echo '</Files>' ;
}

function CreateFolder( $resourceType, $currentFolder )
{
	if (!isset($_GET)) {
		global $_GET;
	}
	$sErrorNumber	= '0' ;
	$sErrorMsg		= '' ;

	if ( isset( $_GET['NewFolderName'] ) )
	{
		$sNewFolderName = $_GET['NewFolderName'] ;
      $sNewFolderName = mb_convert_encoding("$sNewFolderName", "Windows-1251", "UTF-8");
      $sNewFolderName = preg_replace("/[^-a-z0-9]{1,}/",'',str_replace(' ','-',Translit(str_tolower($sNewFolderName))));
		$sNewFolderName = SanitizeFolderName( $sNewFolderName ) ;

		if ( strpos( $sNewFolderName, '..' ) !== FALSE )
			$sErrorNumber = '102' ;		// Invalid folder name.
		else
		{
			// Map the virtual path to the local server path of the current folder.
			$sServerDir = ServerMapFolder( $resourceType, $currentFolder, 'CreateFolder' ) ;

			if ( is_writable( $sServerDir ) )
			{
				$sServerDir .= $sNewFolderName ;

				$sErrorMsg = CreateServerFolder( $sServerDir ) ;

				switch ( $sErrorMsg )
				{
					case '' :
						$sErrorNumber = '0' ;
						break ;
					case 'Invalid argument' :
					case 'No such file or directory' :
						$sErrorNumber = '102' ;		// Path too long.
						break ;
					default :
						$sErrorNumber = '110' ;
						break ;
				}
			}
			else
				$sErrorNumber = '103' ;
		}
	}
	else
		$sErrorNumber = '102' ;

	// Create the "Error" node.
	echo '<Error number="' . $sErrorNumber . '" originalDescription="' . ConvertToXmlAttribute( $sErrorMsg ) . '" />' ;
}

function FileUpload( $resourceType, $currentFolder, $sCommand )
{
	if (!isset($_FILES)) {
		global $_FILES;
	}
	$sErrorNumber = '0' ;
	$sFileName = '' ;

	if ( isset( $_FILES['NewFile'] ) && !is_null( $_FILES['NewFile']['tmp_name'] ) )
	{
		global $Config ;

		$oFile = $_FILES['NewFile'] ;

		// Map the virtual path to the local server path.
		$sServerDir = ServerMapFolder( $resourceType, $currentFolder, $sCommand ) ;

		// Get the uploaded file name.
		$sFileName = $oFile['name'] ;
		$sFileName = SanitizeFileName( $sFileName ) ;
      $sFileName = preg_replace("/[^-a-z0-9.]{1,}/",'',str_replace(' ','-',Translit(str_tolower($sFileName))));
		$sOriginalFileName = $sFileName ;

		// Get the extension.
		$sExtension = substr( $sFileName, ( strrpos($sFileName, '.') + 1 ) ) ;
		$sExtension = strtolower( $sExtension ) ;

		if ( isset( $Config['SecureImageUploads'] ) )
		{
			if ( ( $isImageValid = IsImageValid( $oFile['tmp_name'], $sExtension ) ) === false )
			{
				$sErrorNumber = '202' ;
			}
		}

		if ( isset( $Config['HtmlExtensions'] ) )
		{
			if ( !IsHtmlExtension( $sExtension, $Config['HtmlExtensions'] ) &&
				( $detectHtml = DetectHtml( $oFile['tmp_name'] ) ) === true )
			{
				$sErrorNumber = '202' ;
			}
		}

		// Check if it is an allowed extension.
		if ( !$sErrorNumber && IsAllowedExt( $sExtension, $resourceType ) )
		{
			$iCounter = 0 ;

			while ( true )
			{
				$sFilePath = $sServerDir . $sFileName ;

				if ( is_file( $sFilePath ) )
				{
					$iCounter++ ;
					$sFileName = RemoveExtension( $sOriginalFileName ) . '(' . $iCounter . ').' . $sExtension ;
					$sErrorNumber = '201' ;
				}
				else
				{
					move_uploaded_file( $oFile['tmp_name'], $sFilePath ) ;

					if ( is_file( $sFilePath ) )
					{
						if ( isset( $Config['ChmodOnUpload'] ) && !$Config['ChmodOnUpload'] )
						{
							break ;
						}

						$permissions = 0777;

						if ( isset( $Config['ChmodOnUpload'] ) && $Config['ChmodOnUpload'] )
						{
							$permissions = $Config['ChmodOnUpload'] ;
						}

						$oldumask = umask(0) ;
						chmod( $sFilePath, $permissions ) ;
						umask( $oldumask ) ;

						if ($_POST['thumb'] && in_array($sExtension, array("gif", "jpg", "jpeg", "png", "wbmp"))) {
							filemanager_thumb((int)$_POST['thumb_q'],$sFilePath, $_POST['thumb_x'], $_POST['thumb_y']);
		            }

					}

					break ;
				}
			}

			if ( file_exists( $sFilePath ) )
			{
				//previous checks failed, try once again
				if ( isset( $isImageValid ) && $isImageValid === -1 && IsImageValid( $sFilePath, $sExtension ) === false )
				{
					@unlink( $sFilePath ) ;
					$sErrorNumber = '202' ;
				}
				else if ( isset( $detectHtml ) && $detectHtml === -1 && DetectHtml( $sFilePath ) === true )
				{
					@unlink( $sFilePath ) ;
					$sErrorNumber = '202' ;
				}
			}
		}
		else
			$sErrorNumber = '202' ;
	}
	else
		$sErrorNumber = '202' ;


	$sFileUrl = CombinePaths( GetResourceTypePath( $resourceType, $sCommand ) , $currentFolder ) ;
	$sFileUrl = CombinePaths( $sFileUrl, $sFileName ) ;

	SendUploadResults( $sErrorNumber, $sFileUrl, $sFileName ) ;

	exit ;
}

//****************************************
function FileDelete($resourceType, $currentFolder, $Command) {
	global $Config;
	if (!unlink($_SERVER['DOCUMENT_ROOT'].$_GET['DelFile'])) echo '<Error number="1" originalDescription="Ошибка при удалении файла" />';
}

function FolderDelete($resourceType, $currentFolder, $Command) {
	global $Config;
	$thumb = 0;
	$sServerDir = ServerMapFolder( $resourceType, $currentFolder, $Command ) ;
	if (
		!filemanager_deldir($_SERVER['DOCUMENT_ROOT'].GetResourceTypePath($resourceType, 'Delete'),
			$currentFolder.$_GET['DelFolder'].'/', $thumb)
		|| !rmdir($sServerDir.$_GET['DelFolder'].'/')
	)
	echo '<Error number="1" originalDescription="Ошибка при удалении папки" />' ;
}
function filemanager_deldir($root, $del, $thumb=0) {
	$cont = glob(CombinePaths($root, $del)."*");
	$rootLen = strlen($root);
	$ok = 1;
	foreach ($cont as $val) {
		if (is_dir($val)) {
			$ok *= filemanager_deldir($root, substr($val, $rootLen-1)."/", $thumb);
			$ok *= rmdir($val)?1:0;
		} else {
			$ok *= unlink($val)?1:0;
		}
	}
	return $ok;
}

function filemanager_thumb($QTY=100,$IMAGE_SOURCE, $THUMB_X, $THUMB_Y, $IMAGE_OUT='') {
	if ($QTY<10) $QTY=70;
	list($width, $height, $type, $attr) = getimagesize($IMAGE_SOURCE);
	if ($THUMB_Y < 0 || $THUMB_X < 0) {
		$THUMB_CUT = 1;
		$THUMB_Y = (int)abs($THUMB_Y);
		$THUMB_X = (int)abs($THUMB_X);
	} else {
		$THUMB_CUT = 0;
		$THUMB_Y = (int)$THUMB_Y;
		$THUMB_X = (int)$THUMB_X;
		$SRC_W = $width;
		$SRC_H = $height;
		$SRC_L = 0;
		$SRC_T = 0;
	}
	if ($THUMB_Y == 0 && $THUMB_X == 0) {
		$THUMB_Y = $height;
		$THUMB_X = $width;
	} elseif ($THUMB_Y == 0) {
		$THUMB_Y = (int)($height * ($THUMB_X / $width));
	} elseif ($THUMB_X == 0) {
		$THUMB_X = (int)($width * ($THUMB_Y / $height));
	} elseif ($THUMB_CUT) {
		// Если заданы оба и вырезать, то вырезаю из изображения прямоугольник
		$zoom_x = $width/$THUMB_X;
		$zoom_y = $height/$THUMB_Y;
		if ($zoom_x <= $zoom_y) {
			$SRC_W = $width;
			$SRC_H = (int)($THUMB_Y * $zoom_x);
			$SRC_L = 0;
			$SRC_T = ($height - $SRC_H) / 2;
		} elseif ($zoom_x > $zoom_y) {
			$SRC_W = (int)($THUMB_X * $zoom_y);
			$SRC_H = $height;
			$SRC_L = ($width - $SRC_W) / 2;
			$SRC_T = 0;
		}
	} else {
		// Если заданы оба, то вписываю в прямоугольник
		$zoom_x = $width/$THUMB_X;
		$zoom_y = $height/$THUMB_Y;
		if ($zoom_x >= $zoom_y) {
			$THUMB_Y = (int)($height * ($THUMB_X / $width));
		} elseif ($zoom_x < $zoom_y) {
			$THUMB_X = (int)($width * ($THUMB_Y / $height));
		}
	}

	// Если картинка меньше по обоим измерениям, то ничего не делаю
	if ($THUMB_X > $width && $THUMB_Y > $height) {
		if ($IMAGE_OUT!=$IMAGE_SOURCE)
			copy($IMAGE_SOURCE, $IMAGE_OUT);
		return true;
	}

	$IMAGE_OUT = $IMAGE_OUT?$IMAGE_OUT:$IMAGE_SOURCE;

	if (filemanager_imagemagick_check()) {
		$filter = (($SRC_L || $SRC_T)?'-crop '.$SRC_W.'x'.$SRC_H.'+'.$SRC_L.'+'.$SRC_T.'! ':'')."-resize ".$THUMB_X."x".$THUMB_Y;
		return exec('convert '.$IMAGE_SOURCE.' '.$filter.' '.$IMAGE_OUT)?false:true;
	} elseif (filemanager_gd2_check()) {
		$img_type = array(
	    1 => array("r"=>"gif", "w"=>"png", "vr"=>"GIF Read Support", "vw"=>"PNG Support"),
	    2 => array("r"=>"jpeg", "w"=>"jpeg", "vr"=>"JPG Support", "vw"=>"JPG Support"),
	    3 => array("r"=>"png", "w"=>"png", "vr"=>"PNG Support", "vw"=>"PNG Support"),
	    15 => array("r"=>"wbmp", "w"=>"wbmp", "vr"=>"WBMP Support", "vw"=>"WBMP Support")
	  );
	  if (!$cmd = $img_type[$type]) {
	  	echo "Thumb - Неизвестный формат - $type ($IMAGE_SOURCE)<br>";
	  	return false;
	 	}
	  $gd = gd_info();
	  if (!$gd[$cmd['vr']] || !$gd[$cmd['vw']]) {
	  	echo "Thumb - Формат не поддерживается PHP - ".$cmd['vr']." или ".$gd[$cmd['vw']]."<br>";
	  	return false;
	  }
	  eval('$SRC_IMAGE = ImageCreateFrom'.$cmd['r'].'($IMAGE_SOURCE);');
	  $DEST_IMAGE = ImageCreateTrueColor ($THUMB_X, $THUMB_Y);
	  $res = imagecopyresampled($DEST_IMAGE, $SRC_IMAGE, 0, 0, $SRC_L, $SRC_T, $THUMB_X, $THUMB_Y, $SRC_W, $SRC_H);
	  if ($res)
	  	eval('$res = Image'.$cmd['w'].'($DEST_IMAGE, $IMAGE_OUT,'.$QTY.');');
	  @imagedestroy($SRC_IMAGE);
	  @imagedestroy($DEST_IMAGE);
	  return $res;
	} else {
		return false;
	}
}
function filemanager_imagemagick_check() {
	return strpos(`convert -version`, 'ImageMagick') !== false ? true : false;
}
function filemanager_gd2_check() {
	return function_exists('gd_info')?true:false;
}

// Перевести Русскую строку в Транслит
function Translit($strText)
{
		$strText=strtr($strText,"абвгдеёзийклмнопрстуфхъыэ", "abvgdeeziyklmnoprstufh'ie");
		$strText=strtr($strText,"АБВГДЕЁЗИЙКЛМНОПРСТУФХЪЫЭ", "ABVGDEEZIYKLMNOPRSTUFH'IE");
		$strText=strtr($strText, array(
							 "ж"=>"zh", "ц"=>"ts", "ч"=>"ch", "ш"=>"sh",
							 "щ"=>"sch","ь"=>"", "ю"=>"yu", "я"=>"ya",
							 "Ж"=>"ZH", "Ц"=>"TS", "Ч"=>"CH", "Ш"=>"SH",
							 "Щ"=>"SCH","Ь"=>"", "Ю"=>"YU", "Я"=>"YA",
							 "ї"=>"i", "Ї"=>"Yi", "є"=>"ie", "Є"=>"Ye"));
		return $strText;
}
// Перевести все символы в строке в нижний регистр
function str_tolower($s)
{
	$s = strtr($s, "АБВГДЕЁЖЗИЙКЛМНОРПСТУФХЦЧШЩЪЬЫЭЮЯ",
	"абвгдеёжзийклмнорпстуфхцчшщъьыэюя");
	return strtolower($s);
}

?>
