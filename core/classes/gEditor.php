<?php namespace ColorCore;
    require_once('connect.php');
    use ColorCore\ToDo;
       
    class gEditor extends Connect{
        // получить соединение с бд
        protected $conn;

        public function gDelete ($id) {
            $conn = parent::conn();//db connection
            $userId = $_SESSION['userId'];
            
            // my goods as a string
            $myGoods = $conn->query("SELECT myOrders FROM users WHERE userId = '$userId'");
            $myGoods = $myGoods->fetch_assoc();

            // my goods as a array
            $myGoods = explode(",", $myGoods['myOrders']);
            foreach($myGoods as $key => $item){
                if ($item == $id){
                    unset($myGoods[$key]);
                }
            }
            // my goods as a string
            $myGoods = implode(",", $myGoods);

            // refresh the list of like in the user table
            $conn->query("UPDATE users SET myOrders = '$myGoods' WHERE userId = '$userId'");

            // delete good
            $conn->query("DELETE FROM goods WHERE id='$id'");
        }

        public function gEdit ($id) {
            $conn = parent::conn();//db connection
            $userId = $_SESSION['userId'];
            
            // good data
            $good = $conn->query("SELECT * FROM goods WHERE id = '$id'");
            $good = $good->fetch_assoc();

           echo json_encode($good);
        }

        public function gUpdate ($do) {
            $conn = parent::conn();//db connection
            $name     = $_POST['name'];
            $descr    = $_POST['descr'];
            $cost     = $_POST['cost'];
            $cat      = $_POST['categorie'];
            $location = $_POST['location'];
            $number   = $_POST['number'];
            $id       = $_POST['id'];
            // try {} catch (Exception $e) {}
            
            
            if(isset($_POST['specs']))$specs=$_POST['specs'];
            else $specs=false;

            // array to string
            if($specs){
                foreach ($specs as $spec){
                    $specList[] = $spec['name'].'---'.$spec['value'];
                }
                $specList = implode(",", $specList);
            }else $specList='';
            
            if (!$_FILES['file']['name']) {
                
                // image file from db -- file isn't edited
                $conn->query("UPDATE goods SET goodName='$name', descr='$descr', cost='$cost', sellerAdress='$location', sellerNumber='$number', categorie='$cat', specList='$specList' WHERE id = '$id'");

                echo true;
            }else{
                $do->doSell($_FILES, $name, $descr, $cost, $cat, $location, $number, $specs, $id);
            }
        }

        public function gSales ($id) {
            $conn = parent::conn();//db connection
            $userId = $_SESSION['userId'];
            
            $conn->query("UPDATE users SET reputation = reputation + 10 WHERE userId = '$userId'");
            echo json_encode(true);
        }
    }
?>