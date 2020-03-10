<?php namespace ColorCore;
    require_once('connect.php');
       
    class Categories extends Connect
    {
        // получить соединение с бд
        protected $conn;

        public function showCategories() {
            $conn = parent::conn();//mySql connection

            $sel = $conn->query("SELECT * FROM categories");
            $out = array();
            if($sel->num_rows > 0){
                while($row = $sel->fetch_assoc())
                    $out[] = $row;

                echo json_encode($out);
            }else{
                echo false;
            }
        }
    }
?>