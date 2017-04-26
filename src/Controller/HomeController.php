<?php

//Composants nécessaires au formulaire

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HomeController {

    /**
    * Home page controller.
    *
    * @param Application $app Silex application
    */
    public function indexAction(Application $app) {
            return $app['twig']->render('index.html.twig');
    }

    /**
    * Keywords details controller.
    *
    * @param Request $request Incoming request
    * @param Application $app Silex application
    */

    public function keywordAction(Request $request, Application $app) {
        require_once __DIR__.'/../Domain/Keyword.php';
        require_once __DIR__.'/../Form/Type/KeywordType.php';
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
    }

    /**
    * Add Keyword.
    *
    * @param $word Mot ajouté
    * @param Application $app Silex application
    */
    public function keywordAddAction($word, Application $app) {
    
        $keyword = new Keyword();
        $keyword->setKeyword($word);
        $app['dao.keyword']->save($keyword);
        $keywords = $app['dao.keyword']->allKeyword();
        return $app->redirect('../../keyword');
    }
    /**
    * Update Keyword.
    *
    * @param $id Id du mot modifié
    * @param Request $request Incoming request
    * @param Application $app Silex application
    */
    public function keywordUpdateAction($id, Request $request, Application $app) {
        $keyword = $app['dao.keyword']->idKeyword($id);
        require_once __DIR__.'/../Form/Type/KeywordType.php';
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
    }
    /**
    * Delete Keyword.
    *
    * @param $id Id du mot supprimé
    */
    public function keywordDeleteAction($id, Application $app) {
        $keyword = $app['dao.keyword']->idKeyword($id);
        $app['dao.keyword']->delete($keyword);
        return $app->redirect('../../keyword');
    }
    /**
    * Scrapper form.
    *
    * @param Request $request Incoming request
    * @param Application $app Silex application
    */
    public function scrapperFormAction(Request $request, Application $app) {
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
            'Moteur de recherche' => array(
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
            $research = current($data);
            
            return $app->redirect('scrapper/'.$word->getId().'/'.$research);
        }
        // display the form
        return $app['twig']->render('test.html.twig', array('form' => $form->createView()));
    }
    /**
    * Scrapper.
    *
    * @param $id Id du mot supprimé
    * @param $research Moteur de recherche utilisé (Google,Bing)
    */
    public function scrapperAction($id, $research, Application $app) {
        
        require_once __DIR__.'/../Scrapper/Scrapper.php';
        require_once __DIR__.'/../Domain/Annonce.php';
        require_once __DIR__.'/../DAO/DAO.php';
        $keyword = $app['dao.keyword']->idKeyword($id);
        $scrapper = New Scrapper($keyword->getKeyword());
        //On initialise l'url de Google et on scrappe
        if ($research=="Google") {
            $scrapper->setUrl();
            $annonces = $scrapper->parseKeyword();
        //On initialise l'url de Bing et on scrappe
        } elseif ($research=="Bing") {
        $scrapper->setUrlBing();
        $annonces = $scrapper->parseKeywordBing();
        //On initialise l'url de Google et on scrappe puis l'url de Bing, on scrappe et on fusionne les deux tableaux de réponse
        } elseif($research=="All") {
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
            //Enregistrement de chaque instance de classe Extra
            if (null!==($value->getExtra())){
                $arrayExtra = $value->getExtra();
                foreach ($arrayExtra as $extra) {
                    $extra->setIdAnnonce($value->getId());
                    $app['dao.extra']->save($extra);
                }
            }
        }
        $annonces_google = array();
        $annonces_bing = array();
        foreach ($annonces as $key => $value) {
            if ($value->getResearch()=='Google'){
                    $annonces_google[] = $value;
                }else{
                    $annonces_bing[] = $value;
                }
                
            }
        //On affiche
        return $app['twig']->render('annonce.html.twig', array('annonces_google' => $annonces_google,'annonces_bing' => $annonces_bing));
    }
     /**
    * Archives.
    *
    * @param Request $request Incoming request
    * @param Application $app Silex application
    */
    public function archivesAction(Request $request, Application $app) {
        //On cherche tous les mots clés
        $keywords = $app['dao.keyword']->allKeyword();
        foreach ($keywords as $key => $value) {
            $new[$value->getKeyword()] = $value; 
        }
        //On crée un formulaire qui demande un mot clé et une date
        $formBuilder = $app['form.factory']->createBuilder(FormType::class);
        $formBuilder->add('keywords', ChoiceType::class, array(
            'multiple' => false,
            'choices'  => array( 'Mot clé' => $new),
            ));
        $formBuilder->add('dueDate', DateType::class, array(
        // render as a single text box
        'widget' => 'single_text',
        ));
        $form = $formBuilder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Si le formulaire est valide on recherche tous les annonces correspondantes
            $data = $form->getData();
            $word = current($data);
            next($data);
            $date = current($data);
            $annonces = $app['dao.annonce']->allAnnonceByDate($word->getId(), $date);
            //On recherche toutes les classes annexes à cette annonce reliées à elle par son Id :
            $annonces = $this->buildAnnonce($annonces, $app);
            $annonces_google = array();
            $annonces_bing = array();
            foreach ($annonces as $key => $value) {
                if ($value->getResearch()=='Google')
                {
                    $annonces_google[] = $value;
                }else{
                    $annonces_bing[] = $value;
                }
                
            }
            return $app['twig']->render('annonce.html.twig', array('annonces_google' => $annonces_google,'annonces_bing' => $annonces_bing));
        }
        // display the form
        return $app['twig']->render('archives.html.twig', array('form' => $form->createView()));
    }
    /**
    * Script de génération de pdf puis envoie d'e-mail.
    *
    * @param $id id du keyword
    * @param Application $app Silex application
    */
    public function scriptAction($id, Application $app) {
     require_once __DIR__.'/../Pdf/PDF.php';
        $keyword = $app['dao.keyword']->idKeyword($id);
        $date_today = new DateTime(strftime("%y-%m-%d", mktime(0, 0, 0, date('m'), date('d'), date('y'))));
        $date_yesterday = new DateTime(strftime("%y-%m-%d", mktime(0, 0, 0, date('m'), date('d')-1, date('y'))));
        $annonces_today = $app['dao.annonce']->allAnnonceByDate($id,$date_today);
        $annonces_yesterday = $app['dao.annonce']->allAnnonceByDate($id,$date_yesterday);
        $annonces_today = $this->buildAnnonce($annonces_today, $app);
        $annonces_yesterday = $this->buildAnnonce($annonces_yesterday, $app);
        $newAnnonce = array();
        foreach ($annonces_today as $key => $today) {
            $cmpt = 0;
            $getDate_today = $today->getDate();
            $getId_today = $today ->getId();
            $today->setId(0);
            $today->setDate("");
            foreach ($annonces_yesterday as $key => $yesterday) {
                $getDate_yesterday = $yesterday->getDate();
                $getId_yesterday = $yesterday ->getId();
                $yesterday->setId(0);
                $yesterday->setDate("");
                if ($today == $yesterday) {
                    $cmpt = $cmpt +1;
                }
                $yesterday->setId($getId_yesterday);
                $yesterday->setDate($getDate_yesterday); 
            }
            if ($cmpt == 0) {
                    $newAnnonce[] = $today;
            }
            $today->setId($getId_today);
            $today->setDate($getDate_today); 
        }

        //Création d'un instance de classe PDF
        $pdf = new PDF();
        $titre = utf8_decode('Rapport d\'activité');
        $pdf->SetTitle($titre); //Titre
        $pdf->SetAuthor('Okki'); // Auteur
        $pdf->AddPage(); //On ouvre un page
        $pdf->InfoAnnonce($keyword); // On rajoute des informations en début de page
        foreach ($newAnnonce as $value) { //On rajoute chaque annonce une par une 
            $pdf->AjouterAnnonce($value);
        }
        $pdf->Output('/srv/data/web/vhosts/www.okki-prod.fr/htdocs/projetStageMaxime2017/pdf/'.$id.'.pdf','F'); 
        // On édite le pdf
        $messagebody = "Le message type";
        $name        = "Maxime Husslein";
        $subject = "Message from ".$name;

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array('mailokkitest@gmail.com')) // replace with your own
            ->setTo(array('hungrykyle13@gmail.com'))   // replace with email recipient
            ->attach(\Swift_Attachment::fromPath('/srv/data/web/vhosts/www.okki-prod.fr/htdocs/projetStageMaxime2017/pdf/'.$id.'.pdf'), "Un pdf attaché")
            ->setBody($app['twig']->render('email.html.twig',   // email template
                array('name'      => $name,
                'message'  => $messagebody,
                )),'text/html');
        $app['mailer']->send($message);
        return new Response('Thank you for your feedback!', 201);  
    }
    /**
    * Récupération des élèments d'un tableau d'annonces.
    *
    * @param $annonces tableau d'annonce
    * @param Application $app Silex application
    */
    public function buildAnnonce($annonces, Application $app) { 
     foreach ($annonces as $key => $annonce) {
            // Les instances de classes extra
            $allExtra = $app['dao.extra']->idAnnonceExtra($annonce->getId());
            if (!empty($allExtra)){
                foreach ($allExtra as $value) {
                    $annonce->setExtra($value);
                }
             }
            // Les instances de classes lienannonces 
           $liens = $app['dao.lienannonce']->idAnnonceLien($annonce->getId());
            if (!empty($liens)){
                $annonce->setLienAnnonce($liens);
            }
             // Les instances de classes miniannonces 
             $minis = $app['dao.miniannonce']->idAnnonceMini($annonce->getId());
            if (!empty($minis)){
                 $annonce->setMiniAnnonce($minis);
             }
             //Les instances de classes score
             $score = $app['dao.score']->idAnnonceScore($annonce->getId());
            if (!empty($score)){
                  $annonce->setScore($score);
            }
        }
        return $annonces;
    }
}