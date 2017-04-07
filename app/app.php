<?php


use Symfony\Component\Debug\ErrorHandler;

use Symfony\Component\Debug\ExceptionHandler;

use Silex\Provider\FormServiceProvider;



// Register global error and exception handlers

ErrorHandler::register();

ExceptionHandler::register();


// Register service providers.

$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(

    'twig.path' => __DIR__.'/../views',
));
$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
// Register services.

$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('fr'),
));



