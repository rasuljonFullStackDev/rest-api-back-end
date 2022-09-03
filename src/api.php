<?php

declare(strict_types=1);
try {
    if (stripos($_SERVER['REQUEST_URI'], '?')) {
        $server_request = substr($_SERVER['REQUEST_URI'], 0, stripos($_SERVER['REQUEST_URI'], '?')) ?? $_SERVER['REQUEST_URI'];
    } else {
        $server_request = $_SERVER['REQUEST_URI'];
    }
} catch (Exception $e) {
    echo json_encode(array('xatolik' => $e->getMessage()));
}
spl_autoload_register(function ($class) {
    require __DIR__ . "/controller/$class.php";
});
try {
    $path = explode('/', $server_request);
    $method = $_SERVER['REQUEST_METHOD'];
} catch (Exception $e) {
    echo json_encode(array('xatolik' => $e->getMessage()));
}

require_once __DIR__ . '/config/Db.php'; ///db file cnnected
require_once __DIR__ . '/config/Cors.php'; /// web site haking
require_once __DIR__ . '/config/Validation.php'; ///validation
require_once __DIR__ . '/config/Token.php'; //crf token create
require_once __DIR__ . '/config/Json.php'; //crf token create
$db = new DB; /// new db class start
// $db->dbName = 'rest-api-back-end'; //// db name 
$db->dbName = 'crud'; //// db name 
$db->dbusername = 'root'; //// db username
$db->dbPasword = ''; /// db password
$dbconnect =  $db->connect(); /// db connected start
$user = new UsersController;
$cars = new CarsController;
$blogs = new BlogesController;
$user->db = $db->connect();
$blogs->db = $db->connect();
$cars->db = $db->connect();
$auth = new TokenController;
$auth->db = $db->connect();
$header = new Header; /// srf token
$json = new Response; /// response json 
$token = new Token; /// crf token create


$request = $method==='POST'|| $_FILES ?  json_decode(file_get_contents('php://input'), true) ?? $_POST : $_GET;
// $request = json_decode(file_get_contents('php://input'),true);
// if($method==='POST'){
//     // if(count($_FILES)>0);
//     $request = json_decode(file_get_contents('php://input'),true) ?? $_POST;
// }
// var_dump($request);

$params = $_GET ?? '';
if ($header->auth($server_request, $auth) === true) {
    switch ($method) {
        case 'POST': {
                switch ($server_request) {
                    case '/rest-api-back-end/v1/api/register/': {
                            $validation = new Validation;
                            $validation->key = ['username', 'password',  'email'];
                            $request['img'] = $user->fileUrl($_FILES['img'],['image/png','image/jpg']);
                            $validation->request =  $request;
                            $user->request = $request;
                            $user->requestDate();
                            $required = $user->required($request);
                            $valid = $validation->valid();
                            if ($valid === true && $required === true && $user->create()!==false)  {
                                $tokens = $token->createToken();
                                $user->file($_FILES['img'],['image/png','image/jpg']);
                                $auth->token = $tokens;
                                $auth->create();
                                $json->json(201, ['token' =>   $tokens,'username'=>$user->username,'img'=>$user->img,'email'=>$user->email ]);
                            } else {
                                $json->json(403, array('validation' => $valid, 'required' => $required));
                            }
                        }
                        break;
                    case '/rest-api-back-end/v1/api/logout/': {
                            $tokenCount = $auth->filter('token', apache_request_headers()['token__'] ?? '');
                            if (count($tokenCount) > 0) {
                                $json->json(200, [...$auth->delete($tokenCount[0]['id']), 'xabar' => 'siz profildan chiqdingiz',]);
                            } else {
                                $json->json(401, array('xabar' => "siz ro'yhatdan o'tmagansiz"));
                            }
                        }
                        break;
                    case '/rest-api-back-end/v1/api/login/': {
                        $res = $user->where(['username'=>$request['username'],'password'=>$request['password']],"AND");
                        if (count($res) > 0) {
                            $tokens = $token->createToken();
                            $auth->token = $tokens;
                            $auth->create();
                            if($auth->create()!==false){
                                $json->json(201, ['token' =>   $tokens, ...$user->create()]);
                            }
                            } else {
                                $json->json(401, array('xabar' => "siz ro'yhatdan o'tmagansiz"));
                            }
                        }
                        break;
                    case '/rest-api-back-end/v1/api/blog/': {
                        $validation = new Validation;
                        $validation->key = ['title', 'description',  'author'];
                        $validation->request =  $request;
                        $blogs->request = $request;
                        $blogs->requestDate();
                        $valid = $validation->valid();
                        if ($valid === true) {
                            $json->json(201, [ ...$blogs->create()]);
                        } else {
                            $json->json(403, array('validation' => $valid));
                        }
                    }
                    
                    break;

                    case '/rest-api-back-end/v1/api/cars/': {
                            $validation = new Validation;
                            $validation->key = ['name', 'description',  'price', 'img'];
                            $request['img'] = $cars->fileUrl($_FILES['img'],['image/png','image/jpg','image/JPG','image/jpeg']);
                            $validation->request =  $request;
                            $cars->request = $request;
                            $cars->requestDate();
                            $required = $cars->required(['name' => $request['name'] ?? '']);
                            $valid = $validation->valid();
                            if ($valid === true && $required === true) {
                                $json->json(201, $cars->create());
                                $cars->file($_FILES['img'],['image/png','image/jpg','image/JPG','image/jpeg']);
                            } else {
                                $json->json(400, array('validation' => $valid, 'required' => $required));
                            }
                        }
                    break;
                    case '/rest-api-back-end/v1/api/cars/update/':
                        // var_dump($request);
                        if ($params['id'] ?? '' && count($user->showId($params['id'])) === 1) {
                            $carsId = $cars->showId($params['id'])[0];
                            $request['name'] = $request['name']  ??  $carsId['name'];
                            $request['description'] = $request['description']  ??  $carsId['description'];
                            $request['price'] = $request['price']  ??  $carsId['price'];
                            $request['img'] =  $cars->fileUrl($_FILES['img'],['image/png','image/jpg','image/JPG','image/jpeg'])  ??  $carsId['img'];
                            $cars->request = $request;
                            $cars->requestDate();
                            $json->json(200, array('status' => 200, ...$cars->update($params['id'])));
                            if($cars->fileUrl($_FILES['img'],['image/png','image/jpg','image/JPG','image/jpeg'])!==''){
                                $cars->deleteFile($carsId['img']);
                                $cars->file($_FILES['img'],['image/png','image/jpg','image/JPG','image/jpeg']);
                            }
                        } else {
                            // $json->json(400, array('xabar' => 'Malumot kiritishda xatolik bor'));
                        }
                        break;
                        default: {
                            $json->json(404, array('xabar' => 'api url topilmadi'));
                        }
                        break;
                }
            }
            break;
        case 'GET': {
                switch ($server_request) {
                    case '/rest-api-back-end/v1/api/users/': {
                            if ($params['id'] ?? '') {
                                $json->json(200, array( 'users' => $user->showId($params['id'])));
                            } else {
                                $json->json(200, array( 'users' => $user->all()));
                            }
                        }
                        break;
                    case '/rest-api-back-end/v1/api/cars/': {
                            if ($params['id'] ?? '') {
                                $json->json(200, array( 'cars' => $cars->showId($params['id'])));
                            } else {
                                $json->json(200, $cars->all());
                            }
                        }
                        break;
                        case '/rest-api-back-end/v1/api/blog/': {
                            if ($params['id'] ?? '') {
                                $json->json(200, array( 'blog' => $blogs->showId($params['id'])));
                            } else {
                                $json->json(200, array( 'blog' => $blogs->all()));
                            }
                        }
                        break;
                        case '/rest-api-back-end/v1/api/file/': {
                            // echo 'sasas';
                            // if ($params['id'] ?? '') {
                                $carsImg = $cars->showId($params['id'])[0]['img'];
                                // header('Content-Type: image/png'); 
                            //  echo    file_get_contents(__DIR__.'./src/controller'. $carsImg);
                            //  readfile(__DIR__.'controller/store/00ea2d4c28e848926f1a708b5f1004cb.png');
                                // echo './src/controller'. $carsImg;
                            // } else {
                                // $json->json(200, array( 'blog' => $blogs->all()));
                            // }
                            // $userJson =  file_get_contents("./00ea2d4c28e848926f1a708b5f1004cb.png");

                            // $user = json_decode($userJson,true);
                            // echo $userJson;
                            // $file = '0   0ea2d4c28e848926f1a708b5f1004cb.jpg';

                            header('Content-Type: image/png');
                            // header('Content-Length: ' . filesize($file));
                            $img =  file_get_contents(__DIR__.'\00ea2d4c28e848926f1a708b5f1004cb.png');
                            // $user = json_decode($img,true);
                            
                            // readfile($file);
                            // var_dump(scandir('./src'));
                            // echo __FILE__;
                            // echo __FILE__;
                            // echo __DIR__.'\com.php';
                            // readfile(__DIR__.'\00ea2d4c28e848926f1a708b5f1004cb.png');
                            echo $img;
                        }
                        break;
                    default: {
                            $json->json(404, array('xabar' => 'api url topilmadi'));
                        }
                        break;
                }
            }
            break;
        case 'PUT': {
                switch ($server_request) {
                    case '/rest-api-back-end/v1/api/users/':
                        if ($params['id'] ?? '' && count($user->showId($params['id'])) === 1) {
                            $userId = $user->showId($params['id'])[0];
                            $request['username'] = $request['username']  ??  $userId['username'];
                            $request['password'] = $request['password']  ??  $userId['password'];
                            $request['email'] = $request['email']  ??  $userId['email'];
                            $request['img'] = $request['img']  ??  $userId['img'];
                            $user->request = $request;
                            $user->requestDate();
                            $res  = $user->required($request);
                            if ($res === true) {
                                $json->json(200, array('status' => 200, ...$user->update($params['id'])));
                            } else {
                                // $json->json(403, ['required' => $res]);
                            }
                        } else {

                            // $json->json(400, array('xabar' => 'Malumot kiritishda xatolik bor'));
                        }
                        break;
                    case '/rest-api-back-end/v1/api/cars/':
                        // var_dump($request);
                        if ($params['id'] ?? '' && count($user->showId($params['id'])) === 1) {
                            $carsId = $cars->showId($params['id'])[0];
                            $request['name'] = $request['name']  ??  $carsId['name'];
                            $request['description'] = $request['description']  ??  $carsId['description'];
                            $request['price'] = $request['price']  ??  $carsId['price'];
                            $request['img'] = $request['img']  ??  $carsId['img'];
                            $cars->request = $request;
                            $cars->requestDate();
                            $json->json(200, array('status' => 200, ...$cars->update($params['id'])));
                        } else {
                            // $json->json(400, array('xabar' => 'Malumot kiritishda xatolik bor'));
                        }
                        break;
                        case '/rest-api-back-end/v1/api/blog/':
                            if ($params['id'] ?? '' && count($user->showId($params['id'])) === 1) {
                                $blogsId = $blogs->showId($params['id'])[0];
                                $request['title'] = $request['title']  ??  $blogsId['title'];
                                $request['author'] = $request['author']  ??  $blogsId['author'];
                                $request['description'] = $request['description']  ??  $blogsId['description'];
                                $blogs->request = $request;
                                $blogs->requestDate();
                                $json->json(200, array('status' => 200, ...$blogs->update($params['id'])));
                            } else {
                                $json->json(400, array('xabar' => 'Malumot kiritishda xatolik bor'));
                            }
                            break;
                    default: {
                            $json->json(400, array('xabar' => 'Malumot kiritishda xatolik bor'));
                        }
                        break;
                }
            }
            break;
        case "DELETE": {
                switch ($server_request) {
                    case '/rest-api-back-end/v1/api/users/': {
                            if ($params['id'] ?? '') {
                                if ($user->delete($params['id'])) {
                                    $json->json(200, array('xabar' => 'malumot ochirildi'));
                                } else {
                                    $json->json(400, array('xabar' => 'Malumot kiritishda xatolik bor'));
                                }
                            } else {
                                $json->json(400, array('xabar' => 'Malumot kiritishda xatolik bor'));
                            }
                        }
                        break;
                    case '/rest-api-back-end/v1/api/cars/': {
                            if ($params['id'] ?? '') {
                                $carsDel = $cars->showId($params['id'])[0];
                                if ($cars->delete($params['id']) ) {
                                    $cars->deleteFile($carsDel['img']);
                                    $json->json(200, array('xabar' => 'malumot ochirildi'));
                                } else {
                                    $json->json(400, array('xabar' => 'Malumot kiritishda xatolik bor'));
                                }
                            } else {
                                $json->json(400, array('xabar' => 'Malumot kiritishda xatolik bor'));
                            }
                        }
                        break;
                        case '/rest-api-back-end/v1/api/blog/': {
                            if ($params['id'] ?? '') {
                                if ($blogs->delete($params['id'])) {
                                    $json->json(200, array('xabar' => 'malumot ochirildi'));
                                } else {
                                    $json->json(400, array('xabar' => 'Malumot kiritishda xatolik bor'));
                                }
                            } else {
                                $json->json(400, array('xabar' => 'Malumot kiritishda xatolik bor'));
                            }
                        }
                        break;
                    default: {
                            // $json->json(404, array('xabar' => "api url topilmadi"));
                        }
                        break;
                }
            }
            break;
        default: {
                // $json->json(401, array('xabar' => "siz ro'yhatdan o'tmagansiz"));
            }
            # code...
            break;
    }
} else {
    $json->json(401, array('xabar' => "siz ro'yhatdan o'tmagansiz"));
}
