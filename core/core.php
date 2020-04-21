<?php   
    cors();
    // session_id('onin7ujkh3mnbktko5npb90uvmv8lpc2');
    session_start();
    
    require "../vendor/autoload.php";
   
    error_reporting(E_ALL);//show all errors

    // use ColorCore;
    use ColorCore\Connect;
    use ColorCore\Render;
    use ColorCore\ToDo;
    use ColorCore\Like;
    use ColorCore\Cart;
    use ColorCore\Categories;
    use ColorCore\Good;
    use ColorCore\Chat;
    use ColorCore\Login;
    use ColorCore\Registration;
    use ColorCore\gEditor;

    // auto load classes
    // spl_autoload_register(function ($class) {
    //     include 'classes/' . $class . '.php';
    // });

    // what to choose 
    if(isset($_POST['action'])) $action   = $_POST['action']; 

    // choose goods from startPos to startPos+n
    if(isset($_POST['startPos'])) $startPos = $_POST['startPos'];

    if(isset($_GET['registration'])) $action = $_GET['registration'];

    $render = new Render($action);
    $do     = new ToDo();
    $like   = new Like();
    $cart   = new Cart();
    $cat    = new Categories();
    $good   = new Good();

    switch ($action) {
        case 'goods':
            $render->renderGoods($startPos);
            break;
        case 'profile':
            if(isset($_SESSION['authorization'])){
                $action = 'prof';
                echo $action;
            }else{
                $action = 'login';
                echo $action;
            }
            break;
        case 'registration':
            $email   = $_POST['email'];
            $pass    = $_POST['epass'];
            $name    = $_POST['name'];
            $surname = $_POST['surname'];
            $registration = new Registration($email, $pass, $name, $surname);
            try {
                // Если true
                $registration->checkUser();
                // Если true
                $registration->register();
                // Если true
                echo 'true';
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            break;
        case 'login':
            $emailLogin = $_POST['email'];
            $passLogin  = $_POST['epass'];
            $doLogin    = new Login($emailLogin, $passLogin);
            try {
                $doLogin->login();
                var_dump($_SESSION);
                // Если true
                // echo session_id();   
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            break;
        case 'getMyChats':
            if (isset($_SESSION['authorization'])){
                $chat = new Chat();
                $chat->getMyChats();
            }else echo json_encode('login');
            break;
        case 'logout':
            unset($_SESSION['userId']);
            unset($_SESSION['userName']);
            unset($_SESSION['authorization']);
            session_unset();
            
            echo true;
            break;
        case 'avatar':
            $render->renderAvatar();
            break;
        case 'orders':
            $render->renderMyOrd();
            break;
        case 'sell':
            $name     = $_POST['name'];
            $descr    = $_POST['descr'];
            $cost     = $_POST['cost'];
            $cat      = $_POST['categorie'];
            $location = $_POST['location'];
            $number   = $_POST['number'];
            if(isset($_POST['specs'])) $specs = $_POST['specs'];
            else $specs = false;

            $do->doSell($_FILES, $name, $descr, $cost, $cat, $location, $number, $specs);
            break;
        case 'search':
            unset($_SESSION['search']);
            unset($_SESSION['cat']);
            
            $query = $_POST['query'];
            $_SESSION['search'] = $query;
            
            echo true;
            break;
        case 'deleteSessionSearch':
            unset($_SESSION['search']);
            unset($_SESSION['cat']);
            echo 'udal';
            break;
        case 'addLike':
            if(isset($_SESSION['authorization'])){
                $likedId = $_POST['liked'];
                $like->addLike($likedId);
            }else echo 'login';
            break;
        case 'delLike':
            if(isset($_SESSION['authorization'])){
                $likedId = $_POST['liked'];
                $like->delLike($likedId);
            }else echo 'login';
            break;
        case 'deleteLikeItem':
            $likedId = $_POST['liked'];
            $like->delLike($likedId);
            break;
        case 'like':
            echo $like->echoLike();
            break;
        case 'showCat':
            $cat->showCategories();
            break;
        case 'searchByCat':
            unset($_SESSION['search']);
            unset($_SESSION['cat']);

            $query = $_POST['cat'];
            $_SESSION['cat'] = $query;
            
            echo true;
            break;
        case 'cart':
            echo $cart->echoCart();
            break;
        case 'addToCart':
            if(isset($_SESSION['authorization'])){
                $goodId = $_POST['goodId'];
                $cart->addToCart($goodId);
            }else echo 'login';
            break;
        case 'deleteCartItem':
            $deltedId = $_POST['goodId'];
            $cart->deleteCartItem($deltedId);
            break;
        case 'openGood':
            $openable = $_POST['openable'];
            $good->open($openable);
            break;
        case 'isAdded':
            if(isset($_SESSION['authorization'])){
                $id = $_POST['id'];
                $add = array();
                $add['cart'] = $cart->isAdded($id);
                $add['like'] = $like->isAdded($id);
                
                echo json_encode($add);
            }
            else echo 'login';
            break;
        case 'openChat':
            $buyer = $_POST['buyer'];
            $seller = $_POST['seller'];
            $id = $_POST['goodId'];

            if (isset($_SESSION['authorization'])){
                $chat = new Chat();
                $chat->openChat($buyer, $seller, $id);
            }else json_encode('login');

            break;
        case 'openChatById':
            $chatId = $_POST['chatId'];
            if (isset($_SESSION['authorization'])){
                $chat = new Chat();
                $chat->issetChat($chatId);
            }else echo json_encode('login');
            break;
        case 'deleteChat':
            $chatId = $_POST['chatId'];
            if (isset($_SESSION['authorization'])){
                $chat = new Chat();
                $chat->deleteChat($chatId);
            }else json_encode('login');
            break;
        case 'chatList':
            if (isset($_SESSION['authorization'])) {
                $chat = new Chat();
                $chat->chatList(); 
            }
            break;
        case 'getMyId':
            if (isset($_SESSION['authorization'])) { echo $_SESSION['userId']; }
            break;
        case 'savePushs':
            $_SESSION['pushs'] = $_POST['pushs'];

            if ( isset($_POST['id']) ){
                $chat = new Chat();
                $chat->readed($_POST['id'], $_POST['sender']);
            }
            break;
        case 'getPushs':
            if (isset($_SESSION['pushs'])) 
                echo $_SESSION['pushs'];
            else echo json_encode('empty');
            break;
        case 'deleteMyGood':
            if (isset($_SESSION['authorization'])){
                $goodEditor = new gEditor();
                $goodEditor->gDelete($_POST['id']);
            }
            break;
        case 'editMyGood':
            if (isset($_SESSION['authorization'])){
                $goodEditor = new gEditor();
                $goodEditor->gEdit($_POST['id']);
            }
            break; 
        case 'updateSell':
            $gEditor = new gEditor();
            $gEditor->gUpdate($do);
            break;
        case 'soldGood':
            if (isset($_SESSION['authorization'])){
                $goodEditor = new gEditor();
                $goodEditor->gDelete($_POST['id']);
                $goodEditor->gSales($_POST['id']);
            }
            break;
    } 

    function cors() { 	
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }
    }
