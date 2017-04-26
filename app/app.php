<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Silex\Provider\FormServiceProvider;

require_once __DIR__.'/../src/DAO/AnnonceDAO.php';
require_once __DIR__.'/../src/DAO/KeywordDAO.php';
require_once __DIR__.'/../src/DAO/ScoreDAO.php';
require_once __DIR__.'/../src/DAO/LienAnnonceDAO.php';
require_once __DIR__.'/../src/DAO/MiniAnnonceDAO.php';
require_once __DIR__.'/../src/DAO/ExtraDAO.php';


// Register global error and exception handlers

ErrorHandler::register();

ExceptionHandler::register();


// Register service providers.

$app->register(new SwiftmailerServiceProvider());

$app['swiftmailer.options'] = array(
    'host'       => 'smtp.gmail.com',
    'port'       => 465,
    'username'   => 'mailokkitest@gmail.com',
    'password'   => 'testokkimail',
    'encryption' => 'ssl',
    'auth_mode'  => 'login'
);	
$app['swiftmailer.use_spool'] = false;

$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(

    'twig.path' => __DIR__.'/../views',
));
$app->register(new Silex\Provider\AssetServiceProvider(), array(

    'assets.version' => 'v1'

));
$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
// Register services.

$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('fr'),
));
$app['dao.annonce'] = function ($app) {

    return new AnnonceDAO($app['db']);

};
$app['dao.keyword'] = function ($app) {

    return new KeywordDAO($app['db']);

};
$app['dao.lienannonce'] = function ($app) {

    return new LienAnnonceDAO($app['db']);

};
$app['dao.miniannonce'] = function ($app) {

    return new MiniAnnonceDAO($app['db']);

};
$app['dao.score'] = function ($app) {

    return new ScoreDAO($app['db']);

};
$app['dao.extra'] = function ($app) {

    return new ExtraDAO($app['db']);

};
$app['dao.keyword'] = function ($app) {

    return new KeywordDAO($app['db']);

};


