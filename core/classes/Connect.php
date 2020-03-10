<?php namespace ColorCore;
    use mysqli;

    class Connect {
        private const HOST = '127.0.0.1';
        private const USERNAME ='root';
        private const PASSWORD = '';
        private const DATABASE = 'colorshop';
        protected const GOODSPATH = 'C:/Users/KatSem/Desktop/colorshop 3/src/goods/';
        // protected const GOODSPATH = 'C:/Users/KatSem/Desktop/colorshop 3/dist/goods/';
        protected $mysqli;
    
        public function conn() {
            $mysqli = new mysqli(self::HOST, self::USERNAME, self::PASSWORD, self::DATABASE);
            $mysqli->query("SET NAMES 'utf8'");
    
            if(!$mysqli) die ("Connection failed: " . mysqli_connect_error( $mysqli));
            return $mysqli;           
        }
    }
?>