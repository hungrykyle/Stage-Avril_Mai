<?php

require_once __DIR__.'/../src/Controller/HomeController.php';
require_once __DIR__.'/../src/Controller/AdminController.php';
use Symfony\Component\HttpFoundation\Request;

$app->get('/', "HomeController::indexAction");
$app->match('/keyword', "HomeController::keywordAction");
$app->get('/keyword/add/{word}', "HomeController::keywordAddAction");
$app->match('/keyword/update/{id}', "HomeController::keywordUpdateAction")->bind('update');
$app->get('/keyword/delete/{id}', "HomeController::keywordDeleteAction");
$app->match('/scrapper', "HomeController::scrapperFormAction");
$app->get('/scrapper/{id}/{research}', "HomeController::scrapperAction");
$app->match('/profil', "HomeController::editUserAction")->bind('profil');
$app->match('/avatar', "HomeController::editImageAction")->bind('avatar');
$app->match('/archives', "HomeController::archivesAction");
$app->match('/history/{id}', "HomeController::historyAction");
$app->match('/admin/script', "HomeController::actionScript")->bind('admin_script');
$app->match('/admin/notif', "HomeController::notifAction")->bind('admin_notif');
$app->match('/admin/watch/{id}', "AdminController::watchUserAction")->bind('watch');
$app->match('/admin/unwatch/{id}', "AdminController::unwatchUserAction")->bind('unwatch');
$app->get('/login', "HomeController::loginAction")->bind('login');
$app->get('/admin', "AdminController::adminAction")->bind('admin');
// Add a user
$app->match('/admin/user/add', "AdminController::addUserAction")->bind('admin_user_add');
// Edit an existing user
$app->match('/admin/user/{id}/edit', "AdminController::editUserAction")->bind('admin_user_edit');
// Remove a user
$app->get('/admin/user/{id}/delete', "AdminController::deleteUserAction")->bind('admin_user_delete');
// $app->error(function (\Exception $e, Request $request, $code) use ($app) {
//     switch ($code) {
//         case 403:
//             $message = 'Accès refusé.';
//             break;
//         case 404:
//             $message = 'Page introuvable.';
//             break;
//         default:
//             $message = "Un erreur est apparue.";
//     }
//     return $app['twig']->render('error.html.twig', array('message' => $message));
// });