<?php

require_once __DIR__.'/../src/Controller/HomeController.php';


$app->get('/', "HomeController::indexAction");
$app->match('/keyword', "HomeController::keywordAction");
$app->get('/keyword/add/{word}', "HomeController::keywordAddAction");
$app->match('/keyword/update/{id}', "HomeController::keywordUpdateAction");
$app->get('/keyword/delete/{id}', "HomeController::keywordDeleteAction");
$app->match('/scrapper', "HomeController::scrapperFormAction");
$app->get('/scrapper/{id}/{research}', "HomeController::scrapperAction");
$app->match('/archives', "HomeController::archivesAction");
$app->match('/send/{id}', "HomeController::scriptAction");

