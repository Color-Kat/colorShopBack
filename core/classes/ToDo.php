<?php namespace ColorCore;
    require_once('connect.php');
       
    class ToDo extends Connect{
        // получить соединение с бд
        protected $conn;

        public function doSell ($file, $name, $descr, $cost, $cat, $location, $number, $specs) {
            $conn = parent::conn();

            $tmp_path = 'tmp/';
            $file_name = $file['file']['name'];
            $file_size = $file['file']['size'];
            $file_type = '.'.substr($file['file']['type'], strlen("image/"));
            $file_type = $file_type=='.svg+xml' ? '.svg' : $file_type;
            $file_tmp  = $file['file']['tmp_name'];
            $seller    = $_SESSION['userName'].':'.$_SESSION['userId'];
            $fileName  = md5(microtime().$file_name).$file_type;
            $types     = array('image/gif', 'image/png', 'image/jpeg');
            $size      = 3145728;

            // Проверяем тип файла
            if (in_array($file['file']['type'], $types)){
                if ($file_size < $size){
                    $fname = $this->resize($file['file'], 1, 75, $tmp_path);

                    if (!copy($tmp_path . $fname, parent::GOODSPATH . $fileName))
                        echo 'err';
                    
                    unlink($tmp_path . $fname);

                    $specList = array();
                    foreach ($specs as $spec){
                        $specList[] = $spec['name'].'---'.$spec['value'];
                    }
                    $specList = implode(",", $specList);

                    $conn->query("INSERT INTO goods (`goodName`, `descr`, `cost`, `img`, `seller`, `sellerAdress`, `sellerNumber`, `likes`, `categorie`, `specList`) VALUES ('$name', '$descr', '$cost', '$fileName', '$seller', '$location', '$number', 0, '$cat', '$specList')");
                    
                    $goodId = $conn->insert_id;
                    $userId = $_SESSION['userId'];
                    $conn->query("UPDATE users SET myOrders = CONCAT(myOrders, ',$goodId') WHERE userId = '$userId'");

                    echo true;
                }else echo 'size';
            }else echo 'type';
        }

        private function resize($file, $type = 1, $quality = 75, $tmp_path){
            $max_mini_size = 1000;
            $max_size = 800;

            if ($file['type'] == 'image/jpeg') $src = imagecreatefromjpeg ($file['tmp_name']);
            else if ($file['type'] == 'image/png') $src = imagecreatefrompng ($file['tmp_name']);
            else if ($file['type'] == 'image/gif') $src = imagecreatefromgif ($file['tmp_name']);
            else return false;

            $width_src = imagesx($src); 
            $height_src = imagesy($src);

            if ($type == 1)
                $width = $max_mini_size;
            else if ($type == 2)
                $width = $max_size;

            if ($width_src < $width) {
                $ratio       = $width_src/$width;
                $width_dest  = round($width_src/$ratio)+10;
                $height_dest = round($height_src/$ratio)+10;
 
                $dest = imagecreatetruecolor($width_dest, $height_dest);    
                
                imagealphablending($dest, false);
                imagesavealpha($dest, true);

                imagealphablending($src, false);
                imagesavealpha($src, true);

                imagecopyresampled($dest, $src, 0, 0, 0, 0, $width_dest, $height_dest, $width_src, $height_src);

                if($file['type'] == 'image/png'){
                    $quality = round($quality/10);
                    imagepng($src, $tmp_path . $file['name'], $quality);

                    imagedestroy($dest);
                    imagedestroy($src);

                    return $file['name'];
                }else{
                    imagejpeg($src, $tmp_path . $file['name'], $quality);

                    imagedestroy($dest);
                    imagedestroy($src);

                    return $file['name'];
                }
            }else {
                imagejpeg($src, $tmp_path . $file['name'], $quality);

                imagedestroy($src);
                
                return $file['name'];
            }
        }
    }
?>