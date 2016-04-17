<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/01/16
 * Time: 21:26
 */

namespace Maxcraft\DefaultBundle\Controller;


use Maxcraft\DefaultBundle\Entity\Album;
use Maxcraft\DefaultBundle\Entity\Bug;
use Maxcraft\DefaultBundle\Entity\MP;
use Maxcraft\DefaultBundle\Entity\Notification;
use Maxcraft\DefaultBundle\Form\AlbumType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */
    public function myalbumsAction(Request $request){ //TODO fnr quand registerAction(), et images faites

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $albums = $em->getRepository('MaxcraftDefaultBundle:Album')->findAlbums($user, true);

        $album = new Album();
        $albumForm = $this->get('form.factory')->create(new AlbumType(), $album);

        if ($request->isMethod('POST')){
            $albumForm->handleRequest($request);

            if ($albumForm->isValid()){
                $album->setUser($user);

                $em->persist($album);
                $em->flush();
                $this->get('session')->getFlashBag()->add('info', "Album '".$album->getName()."' créé !");

                return $this->redirect($this->generateUrl('maxcraft_album_edit', array('albumId' => $album->getId())));
            }

            $validator = $this->get('validator');
            $errorList = $validator->validate($album);

            foreach($errorList as $error)
            {
                $this->get('session')->getFlashBag()->add('alert', $error->getMessage());
            }
        }

        return $this->render('MaxcraftDefaultBundle:User:myalbums.html.twig', array(
            'albums' => $albums,
            'form' => $albumForm->createView()
        ));
    }

    /**
     * @param $albumId
     * @param Request $request
     * @return Response
     * @Security("has_role('ROLE_USER')")
     */
    public function editAlbumAction($albumId, Request $request){
        $em = $this->getDoctrine()->getManager();

        if ( !($album = $em->getRepository('MaxcraftDefaultBundle:Album')->findOneById($albumId))) throw $this->createNotFoundException('Cet album n\'existe pas !');
        if($album->getUser() != $this->getUser() AND $this->getUser()->getRole() != 'ROLE_ADMIN')
        {
            throw $this->createNotFoundException('Vous n\'avez pas la permission d\'éditer cet album.');
        }

        $albumForm = $this->createFormBuilder($album)
            ->add('name', 'text')
            ->add('description', 'textarea')
            ->add('display', 'choice', array(
                'choices' => array('1' => 'Oui', '0' => 'Non'),
            ))
            ->add('Enregistrer', new SubmitType())
            ->getForm();

        if ($request->isMethod('POST')){
            $albumForm->handleRequest($request);

            if ($albumForm->isValid()){

                $em->persist($album);
                $em->flush();
                $this->get('session')->getFlashBag()->add('info', "Album '".$album->getName()."' modifié !");

                return $this->redirect($this->generateUrl('maxcraft_album_edit', array('albumId' => $album->getId())));
            }

            $validator = $this->get('validator');
            $errorList = $validator->validate($album);

            foreach($errorList as $error)
            {
                $this->get('session')->getFlashBag()->add('alert', $error->getMessage());
            }
        }

        return $this->render('MaxcraftDefaultBundle:User:albumedit.html.twig', array(
            'album' => $album,
            'form' =>$albumForm->createView()
        ));
    }

    public function profilAction($pseudo){
        $em = $this->getDoctrine()->getManager();

        if ( !($user = $em->getRepository('MaxcraftDefaultBundle:User')->findOneByUsername($pseudo))) throw $this->createNotFoundException('Joueur introuvable avec le pseudo donné !');
        if($user == NULL) {
            throw $this->createNotFoundException('Le joueur "'.$pseudo.'" n\'est pas inscrit !');
        }

        if(!($this->get('security.context')->isGranted('ROLE_USER'))) {
            $visitor = true;
            $myprofil = false;
        }
        else {
            $visitor = false;

            if($user == $this->getUser()) {
                $myprofil = true;
            }
            else {
                $myprofil = false;
            }
        }

        //ALBUMS
        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Album');
        $albums = $rep->findAlbums($user);

        //Zones
        $zones = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User')->findZones($user);

        //argent
        $balance = $user->getPlayer()->getBalance();

        //Gametime
        $GT = $user->getPlayer()->getGametime();
        $mGT = $GT%60;
        $hGT = floor($GT/60);

        //last co
        $lastco = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Session')->getLastCo($user);

        return $this->render('MaxcraftDefaultBundle:User:profil.html.twig', array(
            'user' => $user,
            'zones' => $zones,
            'balance' => $balance,
            'mGT' => $mGT,
            'hGT' => $hGT,
            'nbzones' => count($zones),
            'myprofil' => $myprofil,
            'albums' => $albums,
            'visitor' => $visitor,
            'lastco' =>$lastco
        ));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function parametresAction(Request $request){

        $user = $this->getUser();
        $oldemail = $user->getEmail();


        //form
        $passwordForm = $this->createFormBuilder($user)


            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'Vous avez mal recopié votre mot de passe !',

            ))
            ->add('Sauvegarder', new SubmitType())
            ->getForm();

        $paramForm = $this->createFormBuilder($user)
            ->add('email', 'text')
            ->add('Sauvegarder', new SubmitType())
            ->getForm();


        //recuperation form

        if ($request->isMethod('POST')) {

            if($request->request->has('email'))
            {
                $paramForm->handleRequest($request);

                if($paramForm->isValid() )
                {
                    $notif = new Notification();
                    $notif->setContent('Vous avez modifié votre adresse email ('.$oldemail.' => '.$user->getEmail().' )');
                    $notif->setView(false);
                    $notif->setUser($user);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->persist($notif);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add('info', "Votre adresse email à été changée !");

                    return $this->redirect($this->generateUrl('maxcraft_parametres'));

                }
            }

            if($request->request->has('password'))
            {
                $passwordForm->handleRequest($request);

                if($passwordForm->isValid() )
                {


                    $factory = $this->get('security.encoder_factory');
                    $encoder = $factory->getEncoder($user);
                    $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                    $user->setPassword($password);

                    $notif = new Notification();
                    $notif->setContent('Vous avez modifié votre mot de passe avec l\'ip '.$this->container->get('request')->getClientIp().'.');
                    $notif->setView(false);
                    $notif->setUser($user);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->persist($notif);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add('info', "Votre nouveau mot de passe à été enregistré !");

                    return $this->redirect($this->generateUrl('maxcraft_parameters'));
                }
            }

            $validator = $this->get('validator');
            $errorList = $validator->validate($user);

            foreach($errorList as $error)
            {
                $this->get('session')->getFlashBag()->add('alert', $error->getMessage());
            }

        }

        return $this->render('MaxcraftDefaultBundle:User:parametres.html.twig', array(
            'passwordform' => $passwordForm->createView(),
            'paramform' => $paramForm->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Security("has_role('ROLE_USER')")
     */
    public function bugReportAction(Request $request){
        $bug = new Bug();

        //FORM
        $form = $this->createFormBuilder($bug)
            ->add('type', 'choice', array(
                'choices' => array('SITE' => 'Bug sur le site', 'ORTH' => 'Faute d\'orthographe', 'PLUGIN' => 'Bug d\'un plugin','FORUM'=>'Forum','BUILD' => 'Problème avec le build', 'EVENTS/DONJONS'=>'Problème dans un évent ou un donjon', 'AUTRE' => 'Autre bug')
            ))
            ->add('content', 'textarea')
            ->add('Signaler !', new SubmitType())
            ->getForm();

        //POST FORM

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if($form->isValid() )
            {
                $bug->setUser($this->getUser());

                $notif = new Notification($this);
                $notif->setContent('Vous avez reporté le bug suivant : <br>'.$bug->getContent());
                $notif->setView(false);
                $notif->setUser($this->getUser());

                $em = $this->getDoctrine()->getManager();
                $em->persist($bug);
                $em->persist($notif);
                $em->flush();

                if($this->getUser()->getUsername() == 'Subversif')
                {
                    //easteregg ! TROLOLOOL (baba)
                    $this->get('session')->getFlashBag()->add('info', "Merci ma couille !");
                }
                elseif($this->getUser()->getUsername() == 'Babawy'){
                    //easter egg de Lu
                    $this->get('session')->getFlashBag()->add('alert', "Vous avez reporté un bug, le Staff ne peut pas accepter ça ! Non mais oh! !");
                }
                else
                {
                    $this->get('session')->getFlashBag()->add('info', "Merci d'avoir reporté ce bug, notre équipe le traitera au plus vite.");
                }


                return $this->redirect($this->generateUrl('maxcraft_profil', array('pseudo' => $this->getUser()->getUsername())));

            }


        }

        return $this->render('MaxcraftDefaultBundle:User:bugreport.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function mpAction(){

        $user = $this->getUser();

        $mps = $this->getDoctrine()->getManager()
            ->createQuery('SELECT m FROM MaxcraftDefaultBundle:MP m WHERE m.target = '.$user->getId().' ORDER BY m.date DESC')
            ->setMaxResults(50)
            ->getResult();

        $mpsends = $this->getDoctrine()->getManager()
            ->createQuery('SELECT m FROM MaxcraftDefaultBundle:MP m WHERE m.sender = '.$user->getId().' ORDER BY m.date DESC')
            ->setMaxResults(50)
            ->getResult();

        $render = $this->render('MaxcraftDefaultBundle:User:mp.html.twig', array(
            'mps' => $mps,
            'mpsends' => $mpsends,

        ));

        $em = $this->getDoctrine()->getManager();
        foreach($mps as $mp)
        {


            if($mp->getView() == false)
            {
                $mp->setView(true);
                $em->persist($mp);
            }
        }

        $em->flush();

        return $render;
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function notifsAction(){

        $notifs = $this->getDoctrine()->getManager()
            ->createQuery('SELECT n FROM MaxcraftDefaultBundle:Notification n WHERE n.user = '.$this->getUser()->getId().' ORDER BY n.date DESC')
            ->setMaxResults(50)
            ->getResult();

        $render = $this->render('MaxcraftDefaultBundle:User:notifs.html.twig', array(
            'notifs' => $notifs,
        ));

        //VUE
        $em = $this->getDoctrine()->getManager();
        foreach($notifs as $notif)
        {

            if($notif->getView() == false)
            {
                $notif->setView(true);
                $em->persist($notif);
            }
        }

        $em->flush();

        return $render;
    }

    /**
     * @param $mpId
     * @param $pseudo
     * @param $type
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Security("has_role('ROLE_USER')")
     */
    public function newMpAction($mpId, $pseudo, $type, Request $request){

        $mp = new MP();

        if($pseudo != null)
        {
            $mp->setPseudo($pseudo);
        }

        if($type == 'requestallie')
        {
            $mp->setType('REQUESTALLIE');
            $mp->setTopic('Proposition d\'alliance avec '.$this->getUser()->getFaction()->getTag().'');
        }

        if($mpId != null)
        {
            $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:MP');
            $message = $rep->findOneById($mpId);
            if($message != null )
            {
                if($message->getTarget() == $this->getUser())
                {
                    $mp->setTopic('Re : '.$message->getTopic());
                    $mp->setContent($message->getContent()."\n\n---------- /\\ Réponse de ".$this->getUser()->getUsername()." /\\ ----------\n\n");
                    $mp->setPseudo($message->getSender()->getUsername());


                }
            }
        }

        //form
        $form = $this->createFormBuilder($mp)
            ->add('pseudo', 'text')
            ->add('topic', 'text')
            ->add('content', 'froala', array('required' => false))
            ->getForm();


        //recuperation form


        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User');
            $user = $rep->findOneByUsername($mp->getPseudo());

            if($user == null)
            {
                $valid = false;
                $this->get('session')->getFlashBag()->add('alert', 'Ce destinataire n\'existe pas !');
            }
            else
            {
                $valid = true;
                $mp->setTarget($user);
            }

            if($mp->getPseudo() == $this->getUser()->getUsername())
            {
                $valid = false;
                $this->get('session')->getFlashBag()->add('alert', 'Vous ne pouvez pas vous envoyer un mp !');
            }

            if($form->isValid() AND $valid)
            {
                $mp->setSender($this->getUser());

                if($type == 'requestallie')
                {
                    $mp->setType('REQUESTALLIE');
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($mp);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', "Votre MP à été envoyé !");

                return $this->redirect($this->generateUrl('maxcraft_messages'));
            }

            $validator = $this->get('validator');
            $errorList = $validator->validate($mp);

            foreach($errorList as $error)
            {
                $this->get('session')->getFlashBag()->add('alert', $error->getMessage());
            }

        }

        return $this->render('MaxcraftDefaultBundle:User:newmp.html.twig', array(
            'form' => $form->createView(),
            'type' => $type,
        ));

    }

    /**
     * @param $pseudo
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Security("has_role('ROLE_USER')")
     */
    public function editProfilAction($pseudo, Request $request){


        if($pseudo != $this->getUser()->getUsername())
        {
            throw $this->createAccessDeniedException('Ce n\'est pas votre profil !');
        }

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User');
        $user= $rep->findOneByUsername($pseudo);


        //form
        $form = $this->createFormBuilder($user)
            ->add('profil', 'froala')
            ->add('Enregistrer', new SubmitType())
            ->getForm();

        //recuperation form

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);


            if($form->isValid())
            {
                $notif = new Notification($this);
                $notif->setContent('Vous avez modifié votre message de profil.');
                $notif->setView(false);
                $notif->setUser($user);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->persist($notif);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', "Votre profil à été enregistré !");

                return $this->redirect($this->generateUrl('profil', array('pseudo' => $user->getUsername())));
            }

            $validator = $this->get('validator');
            $errorList = $validator->validate($user);

            foreach($errorList as $error)
            {
                $this->get('session')->getFlashBag()->add('alert', $error->getMessage());
            }

        }

        return $this->render('MaxcraftDefaultBundle:User:editprofil.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}