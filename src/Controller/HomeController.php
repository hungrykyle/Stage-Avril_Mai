<?php

//Composants nécessaires au formulaire
require_once __DIR__.'/../Scrapper/Scrapper.php';
require_once __DIR__.'/../Domain/Annonce.php';
require_once __DIR__.'/../DAO/DAO.php';
require_once __DIR__.'/../Form/Type/UserType.php';
require_once __DIR__.'/../Form/Type/ImageType.php';
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


class HomeController  {

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
    /**
     * Edit user controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function editUserAction(Request $request, Application $app) {
        $user = $app['user'];
        $userForm = $app['form.factory']->create(UserType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $plainPassword = $user->getPassword();
            // find the encoder for the user
            $encoder = $app['security.encoder_factory']->getEncoder($user);
            // compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password); 
            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'L\'utilisateur a bien été modifié.');
        }
        return $app['twig']->render('user_form.html.twig', array(
            'title' => 'Modifier son compte',
            'userForm' => $userForm->createView()));
    }
    /**
     * Edit image user.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function editImageAction(Request $request, Application $app) {
        $user = $app['user'];
        $userForm = $app['form.factory']->create(ImageType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isValid()) {
            $chemin = '/srv/data/web/vhosts/www.okki-prod.fr/htdocs/projetStageMaxime2017/img/';
            $userForm['avatar']->getData()->move($chemin, $user->getId().'.png');
            $user->setAvatar('/../../../img/'.$user->getId().'.png'); 
            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'L\'image a bien été modifié.');
        }
        return $app['twig']->render('user_form_avatar.html.twig', array(
            'title' => 'Modifier l\'avatar',
            'userForm' => $userForm->createView()));
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
            $newkeyword = $form['keyword']->getData() ;
            return $app->redirect('keyword/add/'.$newkeyword);

        }
       
        // display the form
        return $app['twig']->render('keyword.html.twig', array('keywords' => $keywords,'form' => $form->createView(),'formtest' => $form->createView()));
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
        $keyword->setUserId($app['user']->getId());
        $allkeywords = $app['dao.keyword']->allwordByUser($app['user']);
        if (in_array($word, $allkeywords)){
                $app['session']->getFlashBag()->add('error', 'Ce mot est déjà dans votre liste.');
                return $app->redirect('../../keyword');
            }else{
                $keyword->setKeyword($word);
                $keyword->setUserId($app['user']->getId());
                $app['dao.keyword']->save($keyword);
                return $app->redirect('../../keyword');
        }
    
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
        if ($form_modif->isValid() and $app['dao.keyword']->idKeywordByUser($id,$app['user'])) {
            $data = $form_modif->getData();
            $allkeywords = $app['dao.keyword']->allwordByUser($app['user']);
            $word = current($data);
            if (in_array($word, $allkeywords)){
                $app['session']->getFlashBag()->add('error', 'Ce mot est déjà dans votre liste.');
                return $app->redirect('../../keyword');
            }else{
                $keyword->setKeyword($word);
                $keyword->setUserId($app['user']->getId());
                $app['dao.keyword']->update($keyword);
                return $app->redirect('../../keyword');
            }
            
        }
        return $app['twig']->render('update.html.twig', array('keyword' => $keyword,'form_modif' => $form_modif->createView()));
    }
    /**
    * Delete Keyword.
    *
    * @param $id Id du mot supprimé
    */
    public function keywordDeleteAction($id, Application $app) {
        if ($app['dao.keyword']->idKeywordByUser($id,$app['user'])){
            $keyword = $app['dao.keyword']->idKeyword($id);
            $keyword->setUserId($app['user']->getId());
            $app['dao.keyword']->delete($keyword);
            $app['session']->getFlashBag()->add('success', 'L\'utilisateur a bien été supprimé.');
            return $app->redirect('../../keyword');
        }else{
            return $app->redirect('../../');
        }
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
        return $app['twig']->render('scrapper.html.twig', array('form' => $form->createView()));
    }
    /**
    * Scrapping demandé par l'utilisateur de l'interface.
    *
    * @param $id Id du mot supprimé
    * @param $research Moteur de recherche utilisé (Google,Bing)
    */
    public function scrapperAction($id, $research, Application $app) {
            if ($app['dao.keyword']->idKeywordByUser($id,$app['user'])){
                $keyword = $app['dao.keyword']->idKeyword($id);
                $annonces = $this->scrapper($keyword,$app['user'],$research,$app);
                 //On affiche
                return $app['twig']->render('annonce.html.twig', array('annonces_google' => $annonces[0],'annonces_bing' => $annonces[1]));
            }else{
                return $app->redirect('../../');
            }
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
                $all_annonces = array();
                foreach ($annonces as $key => $value) {
                    if ($value->getTitle() !== 'Aucune annonce'){
                        $all_annonces[] = $value;
                    }
                }
                //On recherche toutes les classes annexes à cette annonce reliées à elle par son Id :
                $all_annonces = $this->buildAnnonce($all_annonces, $app);
                $annonces_google = array();
                $annonces_bing = array();
                foreach ($all_annonces as $key => $value) {
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
    * Affiche les annonces d'un mot clé.
    *
    * @param $id Id d'un mot
    * @param Application $app Silex application
    */
    public function historyAction($id, Application $app) {
            if ($app['dao.keyword']->idKeywordByUser($id,$app['user'])){
                $annonces = $app['dao.annonce']->allAnnonce($id);
                $all_annonces = array();
                foreach ($annonces as $key => $value) {
                    if ($value->getTitle() !== 'Aucune annonce'){
                        $all_annonces[] = $value;
                    }
                }
                //On recherche toutes les classes annexes à cette annonce reliées à elle par son Id :
                $all_annonces = $this->buildAnnonce($all_annonces, $app);
                $annonces_google = array();
                $annonces_bing = array();
                foreach ($all_annonces as $key => $value) {
                    if ($value->getResearch()=='Google')
                    {
                        $annonces_google[] = $value;
                    }else{
                        $annonces_bing[] = $value;
                    }
                    
                }
                echo count($annonces_google);
                echo '<br>';
                echo count($annonces_bing);
                echo '<br>';

                if (!empty($annonces_google)){
                    $filterannonces_google = $this->deleteDoublon(array_reverse($annonces_google),$app);
                }
                if (!empty($annonces_bing)){
                    $filterannonces_bing = $this->deleteDoublon(array_reverse($annonces_bing),$app);
                }
                echo count($filterannonces_google);
                echo '<br>';
                echo count($filterannonces_bing);
                echo '<br>';
                return $app['twig']->render('annonce.html.twig', array('annonces_google' => $filterannonces_google,'annonces_bing' => $filterannonces_bing));
            }else{
                return $app->redirect('../');
            }
    }
    
    /**
    * Génération de pdf puis envoie d'e-mail.
    *
    * @param User $user
    * @param $allAnnonce Array of Annonce
    * @param Application $app Silex application
    */
    public function actionPdf(User $user ,$allAnnonce, Application $app) {
    require_once __DIR__.'/../Pdf/PDF.php';   
        //Création d'un instance de classe PDF
        $pdf = new PDF();
        $titre = utf8_decode('Rapport d\'activité');
        $pdf->SetTitle($titre); //Titre
        $pdf->SetAuthor('Okki'); // Auteur
        $pdf->AddPage(); //On ouvre un page
        $today = new DateTime(strftime("%y-%m-%d", mktime(0,0,0, date('m'), date('d'), date('y'))));
        $today->setTime(date('H'), date('i'),date('s'));
        $pdf->InfoRapport($today->format("y-m-d H:i:s"), $user); // On rajoute des informations en début de page
        foreach ($allAnnonce as $key => $value) { //On rajoute chaque annonce une par une 
            $pdf->AjouterAnnonceKeyword($key, $value);
        }
        $chemin = '/srv/data/web/vhosts/www.okki-prod.fr/htdocs/projetStageMaxime2017/pdf/'.$user->getId().'/rapport/pdf/'.$today->format("y-m-d").'.pdf';
        $pdf->Output($chemin, "F"); 
        //On édite le pdf
        $messagebody = 'Voici votre rapport, '.$user->getUsername();
        $name        = "Okki";
        $subject = "Message from ".$name;
        $rapport = New Rapport();
        $rapport->setDate($today);
        $rapport->setLinkRapport($chemin);
        $rapport->setIdUser($user->getId());
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
        // Envoie un mail aux admin qui le souhaitent
        $watchs = $app['dao.watch']->allWatchByUser($user);
        $admins = $app['dao.user']->findAllAdmin();
        foreach ($watchs as $key => $watch) {
            foreach ($admins as $key => $admin) {
                if ($admin->getId()===$watch->getAdminId()) {
                    $messagebodyadmin = 'Voici le rapport de : '.$user->getUsername();
                    $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom(array('mailokkitest@gmail.com')) // replace with your own
                    ->setTo(array('mailokkitest@gmail.com'))   // replace with email recipient
                    ->attach(\Swift_Attachment::fromPath($chemin), "Un pdf attaché")
                    ->setBody($app['twig']->render('email.html.twig',   // email template
                        array('name'      => $name,
                        'message'  => $messagebodyadmin,
                        )),'text/html');
                    $app['mailer']->send($message);
                }
            }
        }  
    }
      /**
    * Génération de pdf puis envoie d'e-mail.
    *
    * @param User $user
    * @param $allAnnonce Array of Annonce²
    * @param Application $app Silex application
    */
    public function actionNotifPdf(User $user , $allAnnonce, Application $app) {
    require_once __DIR__.'/../Pdf/PDF.php';   
        //Création d'un instance de classe PDF
        $pdf = new PDF();
        $titre = utf8_decode('De nouvelles annonces ont été trouvé');
        $pdf->SetTitle($titre); //Titre
        $pdf->SetAuthor('Okki'); // Auteur
        $pdf->AddPage(); //On ouvre un page
        $today = new DateTime(strftime("%y-%m-%d", mktime(0,0,0, date('m'), date('d'), date('y'))));
        $today->setTime(date('H'), date('i'),date('s'));
        $pdf->InfoRapport($today->format("y-m-d H:i:s"), $user); // On rajoute des informations en début de page
        foreach ($allAnnonce as $key => $value) { //On rajoute chaque annonce une par une 
            $pdf->AjouterAnnonceKeyword($key, $value);
        }
        $chemin = '/srv/data/web/vhosts/www.okki-prod.fr/htdocs/projetStageMaxime2017/pdf/'.$user->getId().'/newannonce/pdf/'.$today->format("y-m-d_H-i-s").'.pdf';
        $pdf->Output($chemin, "F"); 
        //On édite le pdf
        $messagebody = 'De nouvelles annonces ont été trouvé, '.$user->getUsername();
        $name        = "Okki";
        $subject = "Message from ".$name;
        $notif = New Notif();
        $notif->setDate($today);
        $notif->setLinkNotif($chemin);
        $notif->setIdUser($user->getId());
        $app['dao.notif']->save($notif);
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
    public function deleteDoublon($annonces, Application $app) {
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
        if ($annonce_1->getTitle() === $annonce_2->getTitle() and  $annonce_1->getLink() === $annonce_2->getLink() and  $annonce_1->getDesc() === $annonce_2->getDesc()) {
               if ($nbr_lienannonce_1 === $nbr_lienannonce_2) {
                   if ($nbr_miniannonce_1 === $nbr_miniannonce_2) {
                       if ($nbr_lienannonce_1 !==0){
                            foreach ($annonce_1->getLienAnnonce() as $key => $lien) {
                                $lienannoncestemp1[$lien->getTitle()] = $lien;
                            }
                            foreach ($annonce_2->getLienAnnonce() as $key => $lien) {
                                $lienannoncestemp2[$lien->getTitle()] = $lien;
                            }
                            ksort($lienannoncestemp1);
                            ksort($lienannoncestemp2);
                            foreach ($lienannoncestemp1 as $key => $lienannonce) {
                                $lienannonces1[] = $lienannonce;
                            }
                            foreach ($lienannoncestemp2 as $key => $lienannonce) {
                                $lienannonces2[] = $lienannonce;
                            }
                            $cmpt = 0;
                            while ($cmpt < $nbr_lienannonce_1) {
                                $res = $this->compareLienAnnonce($lienannonces1[$cmpt],$lienannonces2[$cmpt],$app);
                                if (!$res){
                                    return FALSE;
                                }
                                $cmpt = $cmpt +1;
                            }
                       }
                       if ($nbr_miniannonce_1 !==0){
                            foreach ($annonce_1->getMiniAnnonce() as $key => $mini) {
                                $miniannoncestemp1[$mini->getTitle()] = $mini;
                            }
                            foreach ($annonce_2->getMiniAnnonce() as $key => $mini) {
                                $miniannoncestemp2[$mini->getTitle()] = $mini;
                            }
                            ksort($miniannoncestemp1);
                            ksort($miniannoncestemp2);
                            foreach ($miniannoncestemp1 as $key => $miniannonce) {
                                $miniannonces1[] = $miniannonce;
                            }
                            foreach ($miniannoncestemp2 as $key => $miniannonce) {
                                $miniannonces2[] = $miniannonce;
                            }
                            $cmpt = 0;
                            while ($cmpt < $nbr_miniannonce_1) {
                                $res = $this->compareMiniAnnonce($miniannonces1[$cmpt],$miniannonces2[$cmpt],$app);
                                if (!$res){
                                    return FALSE;
                                }
                                $cmpt = $cmpt +1;
                            }
                       }
                   }else{
                       return FALSE;
                   }
               }else{
                   return FALSE;
               }
        }else{
            return FALSE;
        }
        return TRUE;            
    }
    /**
    * Compare deux LienAnnonce
    *
    * @param $lienannonce_1 premier LienAnnonce
    * @param $lienannonce_2 second LienAnnonce
    * @param Application $app Silex application
    */
    public function compareLienAnnonce(LienAnnonce $lienannonce_1, LienAnnonce $lienannonce_2, Application $app) {
        if ($lienannonce_1->getTitle() === $lienannonce_1->getTitle()){
            if ($lienannonce_1->getLink() === $lienannonce_1->getLink()){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }

    }
    /**
    * Compare deux MiniAnnonce
    *
    * @param $miniannonce_1 premier MiniAnnonce
    * @param $miniannonce_2 second MiniAnnonce
    * @param Application $app Silex application
    */
    public function compareMiniAnnonce(MiniAnnonce $miniannonce_1, MiniAnnonce $miniannonce_2, Application $app) {
        if ($miniannonce_1->getTitle() === $miniannonce_2->getTitle()){
            if ($miniannonce_1->getLink() === $miniannonce_2->getLink()){
                if ($miniannonce_1->getDesc() === $miniannonce_2->getDesc()){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    /**
    * Script lancé par le cron pour envoyer les rapports d'activités
    *
    * @param Application $app Silex application
    */
    public function actionScript(Application $app) {
        $users = $app['dao.user']->findAll();
        foreach ($users as $key => $user) {
            if ($user->getFrequency() === 'Quotidien'){
                $user_quotidien[] = $user;
            }else if ($user->getFrequency() === 'Hebdomadaire'){
                $user_hebdo[] = $user;
            }else{
                $user_mensuel[] = $user;
            }
        }
        $date_today = new DateTime(strftime("%y-%m-%d", mktime(0,0,0, date('m'), date('d'), date('y'))));
        $date_today->setTime(date('H'), date('i'),date('s'));
        //Vérifies qu'aucun rapport n'a été envoyé aujourd'hui
        $nbr = count($app['dao.rapport']->allRapportByDate($date_today));
        if ($nbr === 0) {
            $date_before = new DateTime(strftime("%y-%m-%d", mktime(0,0,0, date('m'), date('d')-1, date('y'))));
            $date_before->setTime(date('H'), date('i'),date('s'));
            foreach ($user_quotidien as $key => $quotidien) {
                $keywords = $app['dao.keyword']->allKeywordByUser($quotidien);
                $allAnnonce = $this->newAnnonce($keywords, $quotidien,$date_today,$date_before, $app);
                $this->actionPdf($quotidien,$allAnnonce, $app);
            }
            // Vérifie si le jour est lundi
            if ($date_today->format("D") === 'Mon'){
                $date_before = new DateTime(strftime("%y-%m-%d", mktime(0,0,0, date('m'), date('d')-7, date('y'))));
                $date_before->setTime(date('H'), date('i'),date('s'));
                foreach ($user_hebdo as $key => $hebdo) {
                    $keywords = $app['dao.keyword']->allKeywordByUser($hebdo);
                    $allAnnonce = $this->newAnnonce($keywords, $hebdo,$date_today,$date_before, $app);
                    $this->actionPdf($hebdo,$allAnnonce, $app);
                }
            }
            //Vérifie si le jour est le premier du mois
            if ($date_today->format("d") === '01'){
                $date_before = new DateTime(strftime("%y-%m-%d", mktime(0,0,0, date('m')-1, date('d'), date('y'))));
                $date_before->setTime(date('H'), date('i'),date('s'));
                foreach ($user_mensuel as $key => $mensuel) {
                    $keywords = $app['dao.keyword']->allKeywordByUser($mensuel);
                    $allAnnonce = $this->newAnnonce($keywords, $mensuel,$date_today,$date_before, $app);
                    $this->actionPdf($mensuel,$allAnnonce, $app);
                }
            }
        }
      return new Response('Thank you for your feedback!', 201);
    }
    /**
    * Script lancé par le cron qui envoit un email si de nouveaux mails ont été trouvé
    *
    * @param Application $app Silex application
    */
    public function notifAction(Application $app) {
        $users = $app['dao.user']->findAll();
        foreach ($users as $key => $user) {
            $keywords = $app['dao.keyword']->allKeywordByUser($user);
                foreach ($keywords as $key => $keyword) {
                    $this->scrapper($keyword, $user, 'All', $app);
            }
            $date_today = new DateTime(strftime("%y-%m-%d", mktime(0,0,0, date('m'), date('d'), date('y'))));
            $date_today->setTime(date('H')+1, date('i'),date('s'));
            $date_before = new DateTime(strftime("%y-%m-%d", mktime(0,0,0, date('m'), date('d'), date('y'))));
            $date_before->setTime(date('H')-2, date('i'),date('s'));
            $allAnnonce = $this->newAnnonce($keywords, $user,$date_today,$date_before, $app);
            if (!empty($allAnnonce)){
                $this->actionNotifPdf($user, $allAnnonce, $app);
            }else{
                echo 'EMPTY';
            }    
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
            $date = new DateTime(strftime("%y-%m-%d", mktime(0,0,0, date('m'), date('d'), date('y'))));
            $date->setTime(date('H'), date('i'),date('s'));
            $value->setDate($date->format('Y-m-d H:m:s'));
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
            if ($value->getResearch()==='Google'){
                    $annonces_google[] = $value;
                }else{
                    $annonces_bing[] = $value;
                }
        }
        $annonces [0] = $annonces_google;
        $annonces [1] = $annonces_bing;
        return $annonces;
    }
    /**
    * Cherche si une nouvelle annonce a été trouvé
    *
    * @param $keyword tableau d'objet keywords
    * @param Application $app Silex application
    */
    public function newAnnonce($keywords, $user, $date_today, $date_before, Application $app) {
            $allAnnonce = array();
            foreach ($keywords as $key => $keyword) {
            $annonces_today = $app['dao.annonce']->allAnnonceByDates($keyword->getId(),$date_today,$date_before,$user);
            $annonces_before = $app['dao.annonce']->allAnnonceBeforeADate($keyword->getId(),$date_before,$user);
            if (!empty($annonces_today)){
                $annonces_today = $this->buildAnnonce($annonces_today, $app);
                $annonces_before = $this->buildAnnonce($annonces_before, $app);
                $annonces_today = $this->deleteDoublon($annonces_today,$app);
            }
            $newAnnonce = array();
            foreach ($annonces_today as $key => $today) {
                $cmpt = 0;
                foreach ($annonces_before as $key => $before) {
                    if ($this->compareAnnonce($today,$before,$app)) {
                        $cmpt = $cmpt +1;
                    }
                }
                if ($cmpt === 0) {
                        $newAnnonce[] = $today;
                }
            }
            if (!empty($newAnnonce)){
                $allAnnonce[$keyword->getKeyword()] = $newAnnonce;
            }
        }
        return $allAnnonce;
    }
}