<?php

require_once __DIR__.'/../src/Controller/HomeController.php';
require_once __DIR__.'/../src/Controller/AdminController.php';


$app->get('/', "HomeController::indexAction");
$app->match('/keyword', "HomeController::keywordAction");
$app->get('/keyword/add/{word}', "HomeController::keywordAddAction");
$app->match('/keyword/update/{id}', "HomeController::keywordUpdateAction");
$app->get('/keyword/delete/{id}', "HomeController::keywordDeleteAction");
$app->match('/scrapper', "HomeController::scrapperFormAction");
$app->get('/scrapper/{id}/{research}', "HomeController::scrapperAction");
$app->match('/archives', "HomeController::archivesAction");
$app->match('/script', "HomeController::actionScript");
$app->get('/login', "HomeController::loginAction")->bind('login');
$app->get('/admin', "AdminController::adminAction")->bind('admin');
// Add a user
$app->match('/admin/user/add', "AdminController::addUserAction")->bind('admin_user_add');
// Edit an existing user
$app->match('/admin/user/{id}/edit', "AdminController::editUserAction")->bind('admin_user_edit');
// Remove a user
$app->get('/admin/user/{id}/delete', "AdminController::deleteUserAction")->bind('admin_user_delete');