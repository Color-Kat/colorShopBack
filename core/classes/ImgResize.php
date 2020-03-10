<?php namespace ColorCore;

    class ImgResize
    {
        protected $path;
        protected $tmp_path;
        protected $types;
        protected $size;
    
    // Функция изменения размера
    // Изменяет размер изображения в зависимости от type:
    //  type = 1 - эскиз
    //  type = 2 - большое изображение
    //  rotate - поворот на количество градусов (желательно использовать   значение 90, 180, 270)
    //  quality - качество изображения (по умолчанию 75%)
    public function prepare($file)
    {
        //Извлекаем путь к папке сайта
        $path = $_SERVER['SCRIPT_FILENAME'];
        //Обрубаем ипсолняемый файл в строке
        $path = str_replace("index.php", '', $path);
        $path .= "куда грузим";
        $path0 = $_SERVER['DOCUMENT_ROOT'];
    
        function resize($file, $type = 1, $rotate = null, $quality = null){
            global $tmp_path;
            $w ='';
    
            // Ограничение по ширине в пикселях
            $max_thumb_size = 200;
            $max_size = 600;
    
            if ($quality == null){
                $quality = 0;
            }
    
            // Cоздаём исходное изображение на основе исходного файла
            if ($file['type'] == 'image/jpeg'){
                $source = imagecreatefromjpeg($file['tmp_name']);
            }elseif ($file['type'] == 'image/png'){
                $source = imagecreatefrompng($file['tmp_name']);
            }elseif ($file['type'] == 'image/gif'){
                $source = imagecreatefromgif($file['tmp_name']);
            }else{
                return false;
            }
    
            // Поворачиваем изображение
            if ($rotate != null){
                $src = imagerotate($source, $rotate, 0);
            }else{
                $src = $source;
            }
    
            $w_src = imagesx($src);
            $h_src = imagesy($src);
    
            imagepng($src, $path.$name, $quality, PNG_NO_FILTER);
            imagedestroy($src);
             return $file['name'];
        }
    
        // Массив допустимых значений типа файла
        $types = array('image/jpg', 'image/png', 'image/jpeg');
        // Максимальный размер файла 5 Мб
        $size = 1024*1024*5;
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
                // Проверяем тип файла
                if (!in_array($file['picture']['type'], $types)){
                    $err[7] = 'Запрещённый тип файла.';
                }
                if ($file['picture']['size'] > $size){
                    $err[8] = 'Максимальный размер файла 5 Мб.';
                }
    
                $name = resize($file['picture'], $file['picture']['type'],'0');
                $tmp_name = $file["picture"]["tmp_name"];
                $name = $file["picture"]["name"];
    
                if (!move_uploaded_file( $tmp_name, $path.$name)) {
                    $err[9] = 'Не удалось загрузить картинку';
                }
                Переменная $web загружает в бд путь к картинке
                $web = $path.$name;
                $web = str_replace('полный путь к папке ','',$webpath);
        }
    
        if (count($err) != 0)
        {
            //Выдает ошибку
            return $err;
        }
        else
        {
            //Возвращаем путь
            return $web;
        }
    }
?>