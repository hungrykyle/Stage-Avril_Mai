<?php


// Home page

$app->get('/', function () use ($app) {

   return $app['twig']->render('index.html.twig');

});

//Composants nécessaires au formulaire
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.domains' => array(),
));
require_once __DIR__.'/../src/Form/Type/KeywordType.php';
require_once __DIR__.'/../src/Domain/Keyword.php';
$app->match('/form', function (Request $request) use ($app) {
    $form = $app['form.factory']->create(KeywordType::class);
    $keywords = $app['dao.keyword']->allKeyword();
    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();
        $word = current($data);
        $keyword = new Keyword();
        $keyword->setKeyword($word);
        $app['dao.keyword']->save($keyword);
        $keywords = $app['dao.keyword']->allKeyword();
        
        
    }

    // display the form
    return $app['twig']->render('form.html.twig', array('form' => $form->createView()));
});


$app->get('/scrapper/{keyword}', function ($keyword) use ($app) {
    

    require_once __DIR__.'/../src/Scrapper/Scrapper.php';
    require_once __DIR__.'/../src/Domain/Annonce.php';
    
    require_once __DIR__.'/../src/DAO/DAO.php';
    
    $scrapper = New Scrapper($keyword);
    //On initialise l'url
    $scrapper->setUrl();
    //On scrappe et on initiliase un objet Annonce
    $annonces = $scrapper->parseKeyword();
   //Enregistrement de chaque annonce
    foreach ($annonces as $value) {
        $app['dao.annonce']->save($value);
        //Enregistrement de chaque lien en dessous des annonces
        if (!empty($value->getLienAnnonce())){
            $arrayLien = $value->getLienAnnonce();
            foreach ($arrayLien as $lien) {
                $lien->setIdAnnonce($value->getId());
                $app['dao.lienannonce']->save($lien);
            }
        }
        //Enregistrement de chaque mini annonce 
        if (!empty($value->getMiniAnnonce())){
            $arrayMini = $value->getMiniAnnonce();
            foreach ($arrayMini as $mini) {
                $mini->setIdAnnonce($value->getId());
                $app['dao.miniannonce']->save($mini);
            }
        }
        //Enregistrement de chaque note 
        if (!empty($value->getScore())){
            $score = $value->getScore();
            $score->setIdAnnonce($value->getId());
            $app['dao.score']->save($score);
        }
        if (!empty($value->getExtra())){
            $arrayExtra = $value->getExtra();
            foreach ($arrayExtra as $extra) {
                $extra->setIdAnnonce($value->getId());
                $app['dao.extra']->save($extra);
            }
        }


    }
    
    //On affiche
    return $app['twig']->render('annonce.html.twig', array('annonces' => $annonces));


});