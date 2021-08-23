<?php
	// Уменьшение размеров картинки и запись в файл
	if(!isset($_GET['name_oldfile']) || empty($_GET['name_oldfile'])) return;

	$name_oldfile = $_SERVER['DOCUMENT_ROOT'].'/'.$_GET['name_oldfile'];

  // Определяем размеры старого
  $info_img=@GetImageSize($name_oldfile);
  if($info_img === false) return;

  $width_old  = $info_img[0];
  $height_old = $info_img[1];
  $image_type = $info_img[2];

	$width_new  = !empty($_GET['width_new'])  ? (int)$_GET['width_new']  : $width_old;
	$height_new = !empty($_GET['height_new']) ? (int)$_GET['height_new'] : $height_old;
	$resample   = isset($_GET['resample'])   ? (bool)$_GET['resample']        : false;
	$quality    = isset($_GET['quality'])    ? (int)$_GET['quality']         : 75;

  // Определяем новый размер исходя из данных: "не более $width_new и $height_new", но ПРОПОРЦИОНАЛЬНО!!
  $zoom_x = $width_old/$width_new; // Масштаб по ширине
  $zoom_y = $height_old/$height_new; // Масштаб по высоте

  // Если ширина исходного больше необходимой
  if($width_old>$width_new)
  {
    if(($height_old/$zoom_x)>$height_new)
    {
      $width_my = $width_old/$zoom_y;
      $height_my = $height_new;
    }
    else
    {
      $width_my = $width_new;
      $height_my = $height_old/$zoom_x;
    }
	}
  else // Если ширина исходного меньше или равна необходимой
  {
    if($height_old>$height_new)
    {
      $width_my = $width_old/$zoom_y;
      $height_my = $height_new;
    }
    else
    {
      $width_my = $width_old;
      $height_my = $height_old;
    }
  }

  // Открываем старый файл
  switch($image_type)
  {
    case IMG_GIF:
      $img_old=imagecreatefromGIF($name_oldfile);
      $header_type='gif';
      break;
    case IMG_JPG:
      $img_old=ImageCreateFromJPEG($name_oldfile);
      $header_type='jpg';
      break;
    case IMG_PNG:
      $img_old=imagecreatefromPNG($name_oldfile);
      $header_type='png';
      break;
    case 3:
      $img_old=imagecreatefromPNG($name_oldfile);
      $header_type='png';
      break;
  }

  // Создаём заголовок для вывода картинки в браузер
	header("Content-type: image/".$header_type);

  // Создаём новое изображение с нужными размерами (пока без типа)
  if(function_exists('imagecreatetruecolor')&&$resample) $img_new=imagecreatetruecolor($width_my, $height_my);
  else $img_new=ImageCreate($width_my, $height_my);

  // Копируем из старого файла в новое изображение весь прямоугольник
  if(function_exists('imagecopyresampled')&&$resample) imagecopyresampled($img_new, $img_old, 0, 0, 0, 0, $width_my, $height_my, $width_old, $height_old);
  else ImageCopyResized($img_new, $img_old, 0, 0, 0, 0, $width_my, $height_my, $width_old, $height_old);

	// Выводим в браузер (или сожраняем в файл в формате JPEG)
	$result=ImageJPEG($img_new, null, $quality);

	// Освобождаем память
	ImageDestroy($img_old);
	ImageDestroy($img_new);
?>