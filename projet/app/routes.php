<?php


// Home page

$app->get('/', function () use ($app) {

   

    $oui = "OUI";

    return $oui;


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

$app->match('/form', function (Request $request) use ($app) {
$form = $app['form.factory']->createBuilder(FormType::class)
    ->add('Keyword', TextType::class, array(
        //Gestion des erreurs
        'constraints' => array(new Assert\NotBlank())
    ))
    ->getForm();
    
    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();
        $word = current($data);

        // do something with the data

        // redirect somewhere
        return $app->redirect('scrapper/'.$word);
    }

    // display the form
    return $app['twig']->render('form.html.twig', array('form' => $form->createView()));
});


$app->get('/scrapper/{keyword}', function ($keyword) use ($app) {
    

    require_once __DIR__.'/../src/Scrapper/Scrapper.php';
    require_once __DIR__.'/../src/Domain/Annonce.php';
    
    $scrapper = New Scrapper($keyword);
    //On initialise l'url
    $scrapper->setUrl();
    //On scrappe et on initiliase un objet Annonce
    $annonces = $scrapper->parseKeyword();
    //On affiche
    return $app['twig']->render('annonce.html.twig', array('annonces' => $annonces));


});