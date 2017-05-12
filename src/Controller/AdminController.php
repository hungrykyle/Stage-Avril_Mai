<?php



use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
require_once __DIR__.'/../Domain/User.php';
require_once __DIR__.'/../Domain/Annonce.php';
require_once __DIR__.'/../Domain/Watch.php';
require_once __DIR__.'/../Form/Type/AdminType.php';
require_once __DIR__.'/../Form/Type/AdminEditType.php';


class AdminController {

    /**
    * Admin home page controller.
    *
    * @param Application $app Silex application
    */

    public function adminAction(Application $app) {
        $users = $app['dao.user']->findAll();
        $watchs = $app['dao.watch']->allWatchByAdmin($app['user']);
        foreach ($users as $key => $user) {
            foreach ($watchs as $key => $watch) {
                if ($user->getId() === $watch->getUserId()){
                    $user->setWatch($watch); 
                }
            }
        }
        return $app['twig']->render('admin.html.twig', array(
            'users' => $users));
    }

    /**
     * Add user controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */

    public function addUserAction(Request $request, Application $app) {
        $user = new User();
        $userForm = $app['form.factory']->create(AdminType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // generate a random salt value
            $salt = substr(md5(time()), 0, 23);
            $user->setSalt($salt);
            $plainPassword = $user->getPassword();
            // find the default encoder 
            $encoder = $app['security.encoder.bcrypt'];
            // compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password);
            $app['dao.user']->save($user);
            mkdir('/srv/data/web/vhosts/www.okki-prod.fr/htdocs/projetStageMaxime2017/pdf/'.$user->getId().'/', 0700);
            mkdir('/srv/data/web/vhosts/www.okki-prod.fr/htdocs/projetStageMaxime2017/pdf/'.$user->getId().'/'.'newannonce/', 0700);
            mkdir('/srv/data/web/vhosts/www.okki-prod.fr/htdocs/projetStageMaxime2017/pdf/'.$user->getId().'/newannonce'.'/'.'pdf/', 0700);
            mkdir('/srv/data/web/vhosts/www.okki-prod.fr/htdocs/projetStageMaxime2017/pdf/'.$user->getId().'/'.'rapport/', 0700);
            mkdir('/srv/data/web/vhosts/www.okki-prod.fr/htdocs/projetStageMaxime2017/pdf/'.$user->getId().'/rapport'.'/'.'pdf/', 0700);
            $app['session']->getFlashBag()->add('success', 'L\'utilisateur a bien été crée.');
        }
        return $app['twig']->render('admin_form.html.twig', array(
            'title' => 'Créer un utilisateur',
            'userForm' => $userForm->createView()));
    }

    /**
     * Edit user controller.
     *
     * @param integer $id User id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */

    public function editUserAction($id, Request $request, Application $app) {
        $user = $app['dao.user']->find($id);
        $userForm = $app['form.factory']->create(AdminEditType::class, $user);
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
        return $app['twig']->render('admin_form_edit.html.twig', array(
            'title' => 'Modifier un utilisateur',
            'userForm' => $userForm->createView()));
    }

    /**
     * Delete user controller.
     *
     * @param integer $id User id
     * @param Application $app Silex application
     */

    public function deleteUserAction($id, Application $app) {
        // Delete all associated comments
        //$app['dao.annonce']->deleteAllByUser($id);
        // Delete the user
        $app['dao.user']->delete($id);
        $app['session']->getFlashBag()->add('success', 'L\'utilisateur a bien été supprimé.');
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }

    /**
     * Créer un objet watch pour recevoir les rapports d'un utilisateur.
     *
     * @param integer $id User id
     * @param Application $app Silex application
     */

    public function watchUserAction($id, Application $app) {
        $watch = new Watch();
        $user = $app['user'];
        $watch->setUserId($id);
        $watch->setAdminId($user->getId());
        $app['dao.watch']->save($watch);
        $app['session']->getFlashBag()->add('success', 'Vous suivez maintenant les rapports de l\'utilisateur.');
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }
    /**
     * Annule la surveillance des rapports d'un utilisateur.
     *
     * @param integer $id User id
     * @param Application $app Silex application
     */

    public function unwatchUserAction($id, Application $app) {
        $app['dao.watch']->delete($id);
        $app['session']->getFlashBag()->add('error', 'Vous ne suivez plus les rapports de cet utilisateur.');
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }
}

