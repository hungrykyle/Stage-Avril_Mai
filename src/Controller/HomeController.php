<?php

//Composants nécessaires au formulaire
require_once __DIR__.'/../Scrapper/Scrapper.php';
require_once __DIR__.'/../Domain/Annonce.php';
require_once __DIR__.'/../DAO/DAO.php';
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
    * Login.
    *
    * @param Request $request Incoming request
    * @param Application $app Silex application
    */
    public function loginAction(Request $request,Application $app) {
        return $app['twig']->render('login.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
            ));
    }

    
    public function adminAction(Application $app) {
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
        $keywords = $app['dao.keyword']->allKeywordByUser($app['user']);
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
        $keyword->setUserId($app['user']);
        $app['dao.keyword']->save($keyword);
        $keywords = $app['dao.keyword']->allKeywordByUser($app['user']);
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
            $keyword->setUserId($app['user']);
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
        $keyword->setUserId($app['user']);
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
        $keywords = $app['dao.keyword']->allKeywordByUser($app['user']);
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
    * Scrapping demandé par l'utilisateur de l'interface.
    *
    * @param $id Id du mot supprimé
    * @param $research Moteur de recherche utilisé (Google,Bing)
    */
    public function scrapperAction($id, $research, Application $app) {
        
            $keyword = $app['dao.keyword']->idKeyword($id);
            $annonces = $this->$this->scrapper($keyword,$app['user'],$research,$app);
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
        $keywords = $app['dao.keyword']->allKeywordByUser($app['user']);
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
            $annonces = $app['dao.annonce']->allAnnonceByDate($word->getId(), $date,$app['user']);
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
            if (!empty($annonces_google)){
                $annonces_google = $this->deleteDoublon($annonces_google,$app);
            }
            if (!empty($annonces_bing)){
                $annonces_bing = $this->deleteDoublon($annonces_bing,$app);
            }
           
            return $app['twig']->render('annonce.html.twig', array('annonces_google' => $annonces_google,'annonces_bing' => $annonces_bing));
        }
        // display the form
        return $app['twig']->render('archives.html.twig', array('form' => $form->createView()));
    }
    /**
    * Génération de pdf puis envoie d'e-mail.
    *
    * @param User $user
    * @param Application $app Silex application
    */
    public function actionPdf(User $user , Application $app) {
    require_once __DIR__.'/../Pdf/PDF.php';
        $date_today = new DateTime(strftime("%y-%m-%d", mktime(0, 0, 0, date('m'), date('d'), date('y'))));
        $date_yesterday = new DateTime(strftime("%y-%m-%d", mktime(0, 0, 0, date('m'), date('d')-1, date('y'))));
        $keywords = $app['dao.keyword']->allKeywordByUser($user);
        $allAnnonce = array();
        foreach ($keywords as $key => $keyword) {
            $str_keyword .= $keyword->getKeyword().' ';
            $annonces_today = $app['dao.annonce']->allAnnonceByDate($keyword->getId(),$date_today,$user);
            $annonces_yesterday = $app['dao.annonce']->allAnnonceByDate($keyword->getId(),$date_yesterday,$user);
            if (!empty($annonces_today)){
                $annonces_today = $this->deleteDoublon($annonces_today,$app);
            }
            foreach ($annonces_today as $key => $today) {
                $newAnnonce = array();
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
                    if ($this->compareAnnonce($today,$yesterday,$app)) {
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
            $allAnnonce[$keyword->getKeyword()] = $newAnnonce;
        }
        

        //Création d'un instance de classe PDF
        $pdf = new PDF();
        $titre = utf8_decode('Rapport d\'activité');
        $pdf->SetTitle($titre); //Titre
        $pdf->SetAuthor('Okki'); // Auteur
        $pdf->AddPage(); //On ouvre un page
        $today  = new DateTime(strftime("%y-%m-%d", mktime(0, 0, 0, date('m'), date('d'), date('y'))));
        $pdf->InfoRapport($str_keyword, $today->format("y-m-d"), $user); // On rajoute des informations en début de page
        foreach ($allAnnonce as $key => $value) { //On rajoute chaque annonce une par une 
            $pdf->AjouterAnnonceKeyword($key, $value);
        }
        $chemin = '/srv/data/web/vhosts/www.okki-prod.fr/htdocs/projetStageMaxime2017/pdf/'.$user->getId().'/'.$today->format("y-m-d").'.pdf';
        $pdf->Output($chemin, "F"); 
        //On édite le pdf
        $messagebody = 'Voici votre rapport '.$user->getUsername();
        $name        = "Maxime Husslein";
        $subject = "Message from ".$name;
        $rapport = New Rapport();
        $rapport->setDate($date_today);
        $rapport->setLinkRapport($chemin);
        $rapport->setIdUser($user);
        $app['dao.rapport']->save($rapport);
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array('mailokkitest@gmail.com')) // replace with your own
            ->setTo(array('mailokkitest@gmail.com'))   // replace with email recipient
            ->attach(\Swift_Attachment::fromPath($chemin), "Un pdf attaché")
            ->setBody($app['twig']->render('email.html.twig',   // email template
                array('name'      => $name,
                'message'  => $messagebody,
                )),'text/html');
        $app['mailer']->send($message);
          
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
    /**
    * Supprime les doublons dans un tableau d'annonce.
    *
    * @param $annonces tableau d'annonce
    * @param Application $app Silex application
    */
    public function deleteDoublon($annonce, Application $app) {
        $annonces = array();
        foreach ($annonce as $key => $value) {
            if ($value->getTitle() !== 'Aucune annonce'){
                $annonces[] = $value;
            }
        }
        $count = count($annonces);
        $cmpt = 0;
        $index_array= array();
        foreach ($annonces as $key_1 => $annonce_1) {
            $i = $cmpt;
            while ($i < $count) {
                if ($this->compareAnnonce($annonce_1,$annonces[$i],$app)){
                    if ($i !== $cmpt and (!in_array($i,$index_array)) ){
                        $index_array[] = $i;
                    }
                }
                $i = $i+1;
            }
            $cmpt = $cmpt + 1;
        }
        arsort($index_array);
        foreach ($index_array as $key => $value) {
            unset($annonces[$value]);
        }
        return $annonces;
    }
    /**
    * Compare deux annonces
    *
    * @param $annonce_1 première annonce
    * @param $annonce_2 seconde annonce
    * @param Application $app Silex application
    */
    public function compareAnnonce(Annonce $annonce_1, Annonce $annonce_2, Application $app) {
        $nbr_lienannonce_1 = count($annonce_1->getLienAnnonce());
        $nbr_lienannonce_2 = count($annonce_2->getLienAnnonce());
        $nbr_miniannonce_1 = count($annonce_1->getMiniAnnonce());
        $nbr_miniannonce_2 = count($annonce_2->getMiniAnnonce());
        if ($annonce_1->getTitle() === $annonce_2->getTitle() and  $annonce_1->getLink() === $annonce_2->getLink() and  $annonce_1->getDesc() === $annonce_2->getDesc() and $annonce_1->getStringExtra() === $annonce_2->getStringExtra() ) {
           if ($annonce_1->getScore() === $annonce_2->getScore()){
               if ($nbr_lienannonce_1 === $nbr_lienannonce_2) {
                   if ($nbr_miniannonce_1 === $nbr_miniannonce_2) {
                       if ($nbr_lienannonce_1 !==0){
                            foreach ($annonce_1->getLienAnnonce() as $key => $lien) {
                                $lienannonces1[$lien->getTitle()] = $lien->getLink();
                            }
                            foreach ($annonce_2->getLienAnnonce() as $key => $lien) {
                                $lienannonces2[$lien->getTitle()] = $lien->getLink();
                            }
                            ksort($lienannonces1);
                            ksort($lienannonces2);
                            $cmpt = 0;
                            while ($cmpt < $nbr_lienannonce_1) {
                                if ((current($lienannonces1) !== current($lienannonces2)) Or (key($lienannonces1) !== (key($lienannonces2)))) {
                                    return FALSE;
                                }
                                next($lienannonces1);
                                next($lienannonces2);
                                $cmpt = $cmpt + 1;
                            }
                       }
                       if ($nbr_miniannonce_1 !==0){
                            foreach ($annonce_1->getMiniAnnonce() as $key => $mini) {
                                $miniannonces1[$mini->getTitle()] = $mini->getLink() + ' ' + $mini->getDesc();
                            }
                            foreach ($annonce_2->getMiniAnnonce() as $key => $mini) {
                                $miniannonces2[$mini->getTitle()] = $mini->getLink() + ' ' + $mini->getDesc();
                            }
                            ksort($miniannonces1);
                            ksort($miniannonces2);
                            $cmpt = 0;
                            while ($cmpt < $nbr_miniannonce_1) {
                                if ((current($miniannonces1) !== current($miniannonces2)) Or (key($miniannonces1) !== (key($miniannonces2)))) {
                                    return FALSE;
                                }
                                next($miniannonces1);
                                next($miniannonces1);
                                $cmpt = $cmpt + 1;
                            }
                       }
                   }
               }
           } 
       }
       return TRUE;
    }
    /**
    * Script lancé par le cron
    *
    * @param Application $app Silex application
    */
    public function actionScript(Application $app) {
        $users = $app['dao.user']->findAll();
        foreach ($users as $key => $user) {
            $keywords = $app['dao.keyword']->allKeywordByUser($user);
                foreach ($keywords as $key => $keyword) {
                    $this->scrapper($keyword, $user, 'All', $app);
                }
            echo $user->getId();
            $this->actionPdf($user, $app);
      }
      return new Response('Thank you for your feedback!', 201);
    }
    /**
    * Scrappe un mot clé et enregistre les résultats
    *
    * @param $Keyword $keyword première annonce
    * @param $User $user seconde annonce
    * @param $research
    * @param Application $app Silex application
    */
    public function scrapper(Keyword $keyword, User $user, $research, Application $app) {
     $scrapper = New Scrapper($keyword->getKeyword());
        //On initialise l'url de Google et on scrappe
        if ($research=="Google") {
            $scrapper->setUrl();
            $annonces = $scrapper->parseKeyword();
            if (!empty($annonces)){
                $annonces = $this->deleteDoublon($annonces,$app);
            }
        //On initialise l'url de Bing et on scrappe
        } elseif ($research=="Bing") {
        $scrapper->setUrlBing();
        $annonces = $scrapper->parseKeywordBing();
        if (!empty($annonces)){
            $annonces = $this->deleteDoublon($annonces,$app);
        }
        //On initialise l'url de Google et on scrappe puis l'url de Bing, on scrappe et on fusionne les deux tableaux de réponse
        } elseif($research=="All") {
            $scrapper->setUrl();
            $annoncesGoogle = $scrapper->parseKeyword();
            $scrapper->setUrlBing();
            $annoncesBing = $scrapper->parseKeywordBing();
            if (!empty($annoncesGoogle)){
                $annoncesGoogle = $this->deleteDoublon($annoncesGoogle,$app);
            }
            if (!empty($annoncesBing)){
                $annoncesBing = $this->deleteDoublon($annoncesBing,$app);
            }
            $annonces = array_merge($annoncesGoogle,$annoncesBing);
        }
        
    //Enregistrement de chaque annonce
        foreach ($annonces as $value) {
            $value->setIdKeyword($keyword->getId());   
            $value->setIdUser($user);
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
        $annonces [] = $annonces_google;
        $annonces [] = $annonces_bing;
        return $annonces;

    }
}