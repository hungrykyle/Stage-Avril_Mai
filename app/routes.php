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
$app->match('/keyword', function (Request $request) use ($app) {
    $form = $app['form.factory']->create(KeywordType::class);
    $keywords = $app['dao.keyword']->allKeyword();
    $form->handleRequest($request);
    if ($form->isValid()) {
        $data = $form->getData();
        $word = current($data);
        return $app->redirect('keyword/add/'.$word);
        
    }
    

    // display the form
    return $app['twig']->render('keyword.html.twig', array('keywords' => $keywords,'form' => $form->createView()));
});
$app->get('/keyword/add/{word}', function ($word) use ($app) {
    $keyword = new Keyword();
    $keyword->setKeyword($word);
    $app['dao.keyword']->save($keyword);
    $keywords = $app['dao.keyword']->allKeyword();
    return $app->redirect('../../keyword');
});
$app->match('/keyword/update/{id}', function ($id,Request $request) use ($app) {
    
    $keyword = $app['dao.keyword']->idKeyword($id);
    $form_modif = $app['form.factory']->create(KeywordType::class);
    $form_modif->handleRequest($request);
    if ($form_modif->isValid()) {
        $data = $form_modif->getData();
        $word = current($data);
        $keyword->setKeyword($word);
        $app['dao.keyword']->update($keyword);
        return $app->redirect('../../keyword');
    }
    return $app['twig']->render('update.html.twig', array('keyword' => $keyword,'form_modif' => $form_modif->createView()));
});
$app->get('/keyword/delete/{id}', function ($id) use ($app) {
    $keyword = $app['dao.keyword']->idKeyword($id);
    $app['dao.keyword']->delete($keyword);
    return $app->redirect('../../keyword');
});

$app->match('/scrapper', function (Request $request) use ($app) {
    $keywords = $app['dao.keyword']->allKeyword();
    foreach ($keywords as $key => $value) {
        $new[$value->getKeyword()] = $value; 
    }
    $formBuilder = $app['form.factory']->createBuilder(FormType::class);
    $formBuilder->add('keywords', ChoiceType::class, array(
        'multiple' => false,
        'choices'  => array( 'Mot clé' => $new),
        ));
    $formBuilder->add('nav', ChoiceType::class, array(
        'multiple' => false,
        'choices'  => array(
        'Navigateur' => array(
            'Google' => 'Google',
            'Bing' => 'Bing',
            'Les deux' => 'All'),
        ),));
    $form = $formBuilder->getForm();
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $word = current($data);
        next($data);
        $nav = current($data);
        
        return $app->redirect('scrapper/'.$word->getId().'/'.$nav);
    }
    // display the form
    return $app['twig']->render('test.html.twig', array('form' => $form->createView()));
});

$app->get('/scrapper/{id}/{nav}', function ($id,$nav) use ($app) {
    

    require_once __DIR__.'/../src/Scrapper/Scrapper.php';
    require_once __DIR__.'/../src/Domain/Annonce.php';
    
    require_once __DIR__.'/../src/DAO/DAO.php';
    $keyword = $app['dao.keyword']->idKeyword($id);
    $scrapper = New Scrapper($keyword->getKeyword());
    //On initialise l'url et on scrappe
    if ($nav=="Google") {
        $scrapper->setUrl();
        $annonces = $scrapper->parseKeyword();
    } elseif ($nav=="Bing") {
       $scrapper->setUrlBing();
       $annonces = $scrapper->parseKeywordBing();
    } elseif($nav=="All") {
        $scrapper->setUrl();
        $annoncesGoogle = $scrapper->parseKeyword();
        $scrapper->setUrlBing();
        $annoncesBing = $scrapper->parseKeywordBing();
        $annonces = array_merge($annoncesGoogle,$annoncesBing);
    }
    
   //Enregistrement de chaque annonce
    foreach ($annonces as $value) {
        $value->setIdKeyword($id);   
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
        if (null!==($value->getExtra())){
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