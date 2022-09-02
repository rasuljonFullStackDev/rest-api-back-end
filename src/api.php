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
// $db->dbName = 'rest-api'; //// db name 
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
$params = $_GET ?? '';
if ($header->auth($server_request, $auth) === true) {
    switch ($method) {
        case 'POST': {
                switch ($server_request) {
                    case '/rest-api/v1/api/register/': {
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
                    case '/rest-api/v1/api/logout/': {
                            $tokenCount = $auth->filter('token', apache_request_headers()['token__'] ?? '');
                            if (count($tokenCount) > 0) {
                                $json->json(200, [...$auth->delete($tokenCount[0]['id']), 'xabar' => 'siz profildan chiqdingiz',]);
                            } else {
                                $json->json(401, array('xabar' => "siz ro'yhatdan o'tmagansiz"));
                            }
                        }
                        break;
                    case '/rest-api/v1/api/login/': {
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
                    case '/rest-api/v1/api/blog/': {
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
                    case '/rest-api/v1/api/cars/': {
                            $validation = new Validation;
                            $validation->key = ['name', 'description',  'price', 'img'];
                            $validation->request =  $request;
                            $cars->request = $request;
                            $cars->requestDate();
                            $required = $cars->required(['name' => $request['name'] ?? '']);
                            $valid = $validation->valid();
                            if ($valid === true && $required === true) {
                                $json->json(201, $cars->create());
                            } else {
                                $json->json(403, array('validation' => $valid, 'required' => $required));
                            }
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
                    case '/rest-api/v1/api/users/': {
                            if ($params['id'] ?? '') {
                                $json->json(200, array( 'users' => $user->showId($params['id'])));
                            } else {
                                $json->json(200, array( 'users' => $user->all()));
                            }
                        }
                        break;
                    case '/rest-api/v1/api/cars/': {
                            if ($params['id'] ?? '') {
                                $json->json(200, array( 'cars' => $cars->showId($params['id'])));
                            } else {
                                $json->json(200, array( 'cars' => $cars->all()));
                            }
                        }
                        break;
                        case '/rest-api/v1/api/blog/': {
                            if ($params['id'] ?? '') {
                                $json->json(200, array( 'blog' => $blogs->showId($params['id'])));
                            } else {
                                $json->json(200, array( 'blog' => $blogs->all()));
                            }
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
                    case '/rest-api/v1/api/users/':
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
                                $json->json(403, ['required' => $res]);
                            }
                        } else {
                            $json->json(400, array('xabar' => 'Malumot kiritishda xatolik bor'));
                        }
                        break;
                    case '/rest-api/v1/api/cars/':
                        if ($params['id'] ?? '' && count($user->showId($params['id'])) === 1) {
                            $carsId = $cars->showId($params['id'])[0];
                            $request['name'] = $request['name']  ??  $carsId['name'];
                            $request['description'] = $request['description']  ??  $carsId['description'];
                            $request['price'] = $request['price']  ??  $carsId['price'];
                            $request['img'] = $request['img']  ??  $carsId['img'];
                            $cars->request = $request;
                            $cars->requestDate();
                            $res  = $cars->required(['name' => $request['name'] ?? '']);
                            if ($res === true) {
                                $json->json(200, array('status' => 200, ...$cars->update($params['id'])));
                            } else {
                                $json->json(403, ['required' => $res]);
                            }
                        } else {
                            $json->json(400, array('xabar' => 'Malumot kiritishda xatolik bor'));
                        }
                        break;
                        case '/rest-api/v1/api/blog/':
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
                    case '/rest-api/v1/api/users/': {
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
                    case '/rest-api/v1/api/cars/': {
                            if ($params['id'] ?? '') {
                                if ($cars->delete($params['id'])) {
                                    $json->json(200, array('xabar' => 'malumot ochirildi'));
                                } else {
                                    $json->json(400, array('xabar' => 'Malumot kiritishda xatolik bor'));
                                }
                            } else {
                                $json->json(400, array('xabar' => 'Malumot kiritishda xatolik bor'));
                            }
                        }
                        break;
                        case '/rest-api/v1/api/blog/': {
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
                            $json->json(404, array('xabar' => "api url topilmadi"));
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
