<?php

require_once __DIR__.'/CrudController.php';

$crudController = new CrudController;


$crudController->reset();
$crudController->tableName = 'users';//// database table name
$crudController->tableKey = ['username','password','email','img'];//// database table key
$crudController->ControllerName = 'UsersController'; ///// crud class function name
$crudController->CrudCreate(); //// crud function create

// auth
$crudController->reset();
$crudController->tableName = 'auth_token';//// database table name
$crudController->tableKey = ['token'];//// database table key
$crudController->ControllerName = 'TokenController'; ///// crud class function name
$crudController->CrudCreate(); //// crud function create



$crudController->reset();
$crudController->tableName = 'cars';//// database table name
$crudController->tableKey = ['name','description','img','price'];//// database table key
$crudController->ControllerName = 'CarsController'; ///// crud class function name
$crudController->CrudCreate(); //// crud function create

$crudController->reset();
$crudController->tableName = 'blogs';//// database table name
$crudController->tableKey = ['title','description','author'];//// database table key
$crudController->ControllerName = 'BlogesController'; ///// crud class function name
$crudController->CrudCreate(); //// crud function create