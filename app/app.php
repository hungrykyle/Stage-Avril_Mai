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
require_once __DIR__.'/../src/DAO/RapportDAO.php';
require_once __DIR__.'/../src/DAO/UserDAO.php';


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


$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('fr'),
));

$app['dao.annonce'] = function ($app) {
    $annonceDAO = new AnnonceDAO($app['db']);
    $annonceDAO->setUserDAO($app['dao.user']);
    return $annonceDAO;
};
$app['dao.keyword'] = function ($app) {
    $keywordDAO = new KeywordDAO($app['db']);
    $keywordDAO->setUserDAO($app['dao.user']);
    return $keywordDAO;
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
$app['dao.rapport'] = function ($app) {

    return new RapportDAO($app['db']);
};
$app['dao.user'] = function ($app) {

    return new UserDAO($app['db']);

};

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\SecurityServiceProvider(), array(

    'security.firewalls' => array(
        'login' => array(
            'pattern' => '^/login$',
        ),
        
        'secured' => array(
            'pattern' => '^.*$',
            'anonymous' => false,
            'logout' => true,
            'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
            'users' => function ($app) {
                return new UserDAO($app['db']);
            },
        ),
    ),
));