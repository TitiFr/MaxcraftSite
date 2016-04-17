<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/01/16
 * Time: 21:25
 */

namespace Maxcraft\DefaultBundle\Controller;


use Maxcraft\DefaultBundle\Entity\AlbumRepository;
use Maxcraft\DefaultBundle\Entity\Faction;
use Maxcraft\DefaultBundle\Entity\FactionRole;
use Maxcraft\DefaultBundle\Entity\MP;
use Maxcraft\DefaultBundle\Entity\Notification;
use Maxcraft\DefaultBundle\Websocket\Requests\LoadFactionRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class FactionController extends Controller {

    public function infosAction($factionTag){

        $faction = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Faction')->findOneBy(array('tag' => strtoupper($factionTag)));
        if ($faction == null){

            $message = 'La Faction '. strtoupper($factionTag).' n\'a pas été trouvée';

            throw $this->createNotFoundException('La Faction '. strtoupper($factionTag).' n\'a pas été trouvée');

            /*return $this->render('MaxcraftDefaultBundle:Others:error.html.twig', array(
                'content' => $message
            ));*/
        }

        if(!($this->get('security.context')->isGranted('ROLE_USER'))) {

            $visitor = true;
            $ismine = false;
        }
        else {
            $visitor = false;
            if($faction == $this->getUser()->getFaction())
            {
                $ismine = true;

            }
            else
            {
                $ismine = false;
            }
        }

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User');
        $members= $rep->findByFaction($faction);

        //ALLIES ET ENEMIES
        $allies = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:FactionRole')->findAllies($faction);
        $enemies = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:FactionRole')->findEnemies($faction);

        if( !$ismine AND !$visitor AND $this->getUser()->getFaction())
        {
            $status = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:FactionRole')->findStateObject($faction, $this->getUser()->getFaction());
        }
        else
        {
            $status = null;
        }

        return $this->render('MaxcraftDefaultBundle:Faction:factioninfo.html.twig', array(
            'faction' => $faction,
            'ismine' => $ismine,
            'nbmembers' => count($members),
            'members' => $members,
            'visitor' => $visitor,
            'allies' => $allies,
            'enemies' => $enemies,
            'status' => $status,
            'tag' => strtoupper($faction->getTag())
        ));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createFactionAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $faction = new Faction($this);

        if($this->getUser()->isFactionOwner())
        {
            return $this->render('MaxcraftDefaultBundle:Others:error.html.twig', array(
                'content' => 'Vous êtes déjà fondateur de faction !'
            ));
        }

        if($this->getUser()->getFaction())
        {
            $this->get('session')->getFlashBag()->add('alert', 'Si vous créez une faction vous quitterez votre faction actuelle.');
        }

        $prixFaction = $this->container->getParameter('prix_faction');

        //form
        $form = $this->createFormBuilder($faction)
            ->add('name', 'text')
            ->add('tag', 'text')
            ->add('icon', 'text', array('required' => false))
            ->add('Valider et payer !', new SubmitType())
            ->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);


            $moneyvalid = true;


            if($this->getUser()->getBalance() <  $prixFaction)
            {
                $this->get('session')->getFlashBag()->add('alert', 'Vous devez disposer d\'au moins '.$prixFaction.' POs pour créer une faction !');
                $moneyvalid = false;
            }

            if($form->isValid() AND $moneyvalid)
            {
                $user = $this->getUser();
                $faction->setOwner($user);
                $user->setFactionRole(10);
                $user->setFaction($faction);
                $em->persist($user);
                $em->persist($faction);
                $em->flush();

                //maxcraft
                new LoadFactionRequest($faction);

                return $this->redirect($this->generateUrl('maxcraft_faction', array('factionTag' => $faction->getTag())));
            }

            $validator = $this->get('validator');
            $errorList = $validator->validate($faction);

            foreach($errorList as $error)
            {
                $this->get('session')->getFlashBag()->add('alert', $error->getMessage());
            }

        }



        return $this->render('MaxcraftDefaultBundle:Faction:newfaction.html.twig', array(
            'form' => $form->createView(),
            'prixfaction' => $prixFaction
        ));
    }

    public function factionsListAction(){
        $factions = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Faction')->findBy(
            array(),
            array('tag' => 'desc')
        );

        $prixFaction = $this->container->getParameter('prix_faction');

        return $this->render('MaxcraftDefaultBundle:Faction:factionlist.html.twig', array(

            'factions' => $factions,
            'prix' => $prixFaction,
        ));
    }

    /**
     * @param $mpId
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function acceptrequestAction($mpId){

        $user = $this->getUser();

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:MP');
        $mp= $rep->findOneById($mpId);

        if($mp == null)
        {
            throw $this->createNotFoundException('Cette requete n\'existe pas !');
            /*return $this->render('MaxcraftDefaultBundle:error.html.twig', array(
                'content' => 'Cette requete n\'existe pas !'
            ));*/
        }
        if($mp->getTarget() != $this->getUser())
        {
            throw $this->createNotFoundException('Ce MP ne vous est pas adressé !');
            /*return $this->render('MaxcraftDefaultBundle:error.html.twig', array(
                'content' => 'Ce MP ne vous est pas adressé !'
            ));*/
        }

        if($user->getFaction() == null OR $user->getFaction()->getOwner() != $this->getUser())
        {

            throw $this->createNotFoundException('Vous n\'êtes pas fondateur de faction !');
        }

        if($user->getFaction() == $mp->getSender()->getFaction())
        {

            throw $this->createNotFoundException('Ce joueur est déjà dans votre faction !');
        }

        if($mp->getSender()->isFactionOwner())
        {
            throw $this->createNotFoundException('Vous ne pouvez pas recruter un chef de faction !');
        }

        $em = $this->getDoctrine()->getManager();

        $notif = new Notification($this);
        $notif->setContent('Vous avez été accepté dans la faction <strong>'.$user->getFaction()->getName().'</strong>. Félicitations ! Si vous êtiez dans une autre faction avant vous l\'avez quittée.');
        $notif->setType('FACTION');
        $notif->setUser($mp->getSender());
        $notif->setView(false);
        $em->persist($notif);

        $this->notifFaction($user->getFaction(), '<strong>'.$mp->getSender()->getUsername().'</strong> est entré dans votre faction ! Souhaitez lui la bienvenue.');

        $mp->getSender()->setFaction($user->getFaction());
        $mp->getSender()->setFactionRole(1);

        $em->remove($mp);

        $em->persist($mp->getSender());
        $em->flush();

        $this->get('session')->getFlashBag()->add('info', '<strong>'.$mp->getSender()->getUsername().'</strong> à été accepté dans votre faction !');
        return $this->redirect($this->generateUrl('maxcraft_messages'));
    }

    public function notifFaction($faction, $message, $type = 'FACTION', $view = false)
    {
        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User');
        $members= $rep->findByFaction($faction);

        $em = $this->getDoctrine()->getManager();
        foreach($members as $member)
        {
            $notif = new Notification($this);
            $notif->setContent($message);
            $notif->setType($type);
            $notif->setUser($member);
            $notif->setView($view);
            $em->persist($notif);
        }

        $em->flush();
    }

    public function testAddAction($pseudo, $tag){
        $user = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User')->findOneByUsername($pseudo);
        $faction = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Faction')->findOneByTag(strtoupper($tag));

        if ($user == null || $faction == null){
            throw $this->createNotFoundException('user ou fac null');
        }

        $user->setFaction($faction);
        $this->getDoctrine()->getManager()->persist($user, $faction);
        $this->getDoctrine()->getManager()->flush();

        return $user->getUsername().' ajouté à '.$faction->getName();
    }

    /**
     * @param $mpId
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function acceptallianceAction($mpId){

        $user = $this->getUser();


        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:MP');
        $mp= $rep->findOneById($mpId);

        if($mp == null)
        {
            throw $this->createNotFoundException('Cette requete n\'existe pas !');
        }
        if($mp->getTarget() != $this->getUser())
        {
            throw $this->createNotFoundException('Ce MP ne vous est pas adressé !');
        }

        if($user->getFaction() == null OR $user->getFaction()->getOwner() != $this->getUser())
        {

            throw $this->createNotFoundException('Vous n\'êtes pas fondateur de faction !');
        }

        if($mp->getSender()->getFaction() == null OR !$mp->getSender()->isFactionOwner())
        {

            throw $this->createNotFoundException('Le joueur n\'est plus chef de faction');
        }


        $faction1 = $user->getFaction();
        $faction2 = $mp->getSender()->getFaction();


        $state = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:FactionRole')->findState($faction1, $faction2);

        if($state != 'NEUTRE')
        {
            throw $this->createNotFoundException('Vous devez être neutre avec la faction pour accepter l\'alliance.');
        }

        $em = $this->getDoctrine()->getManager();

        //nouveau state

        $fr = new FactionRole();
        $fr->setSince(new \DateTime());
        $fr->setFaction1($faction2);
        $fr->setFaction2($faction1);
        $fr->setHasRole('FRIEND');
        $em->persist($fr);

        $em->remove($mp);
        $em->flush();

        //NOTIFS
        $this->notifFaction($faction1, 'Votre faction s\'est alliée avec la faction <strong>'.$faction2->getTag().'</strong> ('.$faction2->getName().').');
        $this->notifFaction($faction2, 'Votre faction s\'est alliée avec la faction <strong>'.$faction1->getTag().'</strong> ('.$faction1->getName().').');

        $this->get('session')->getFlashBag()->add('info', 'Vous avez ajouté la faction '.$faction2->getTag().' aux factions alliées !');

        return $this->redirect($this->generateUrl('maxcraft_faction', array('factionTag' => $faction1->getTag())));

    }

    /**
     * @param $validate
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function quitfactionAction($validate = false){

        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        if($user->getFaction() != NULL)
        {

            if($validate == false)
            {
                //verfification
                $yes = $this->generateUrl('quitfaction', array('validate' => true));
                $no = $this->generateUrl('maxcraft_faction', array('factionTag' => $user->getFaction()->getTag()));
                $this->get('session')->getFlashBag()->add('info', 'Voulez vous vraiment quitter votre faction ? <a href="'.$yes.'">Oui</a> <a href="'.$no.'">Non</a>');

                if($user->getFaction()->getOwner() == $user)
                {
                    $this->get('session')->getFlashBag()->add('info', 'Votre départ de la faction entraînera sa suppression définitive.');
                }

            }
            else
            {

                if($user->getFaction()->getOwner() == $user)
                {
                    $this->removeFaction($user->getFaction());
                }

                //depart du membre
                else
                {

                    $faction = $user->getFaction();
                    $user->setFaction(NULL);
                    $em->persist($user);


                    $this->get('session')->getFlashBag()->add('info', 'Vous avez quitté votre faction !');

                    $notif = new Notification($this);
                    $notif
                        ->setContent('Vous avez quitté la faction "'.$faction->getName().'".')
                        ->setView(false)
                        ->setUser($user)
                        ->setType('FACTION');
                    $this->notifFaction($faction, $user->getUsername().' a quitté la faction ');
                    $em->persist($notif);
                    $em->flush();
                }

            }


        }
        return $this->redirect($this->generateUrl('profil', array('pseudo' => $this->getUser()->getUsername())));
    }

    public function removeFaction(Faction $faction)
    {
        $this->notifFaction($faction, 'Votre faction à été dissoute, vous n\'avez plus de faction.');

        $em = $this->getDoctrine()->getManager();
        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User');
        $members = $rep->findByFaction($faction);
        foreach($members as $member)
        {

            $member->setFaction(NULL);
            $member->setFactionRole(1);
            $em->persist($member);

        }

        $states = $em->getRepository('MaxcraftDefaultBundle:FactionRole')->findAllStates($faction);
        foreach($states as $state)
        {

            $em->remove($state);

        }

        //TODO partage de l'argent de la faction
        //$economy->pay($faction->getTag(),$faction->getUser()->getUsername(), $economy->getMoney($faction->getTag()));

        $em->remove($faction);
        $em->flush();

    }

    /**
     * @param $factionTag
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */
    public function factionrequestAction($factionTag, Request $request){

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Faction');
        $faction= $rep->findOneByTag($factionTag);

        if($faction == NULL)
        {
            throw $this->createNotFoundException('La faction "'.$factionTag.'" n\'existe pas !');
        }

        if($faction == $this->getUser()->getFaction())
        {
            throw $this->createNotFoundException('Vous ne pouvez pas demander à entrer dans votre faction !');
        }

        $mp = new MP($this);
        $mp->setTopic('Demande d\'entrée dans votre faction');

        //form
        $form = $this->createFormBuilder($mp)
            ->add('content', 'froala', array('required' => true))
            ->add('Envoyer', new SubmitType())
            ->getForm();


        //recuperation form

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if($form->isValid())
            {
                $mp->setSender($this->getUser());

                $mp->setTarget($faction->getOwner());
                $mp->setType('FACTION_REQUEST');

                $em = $this->getDoctrine()->getManager();
                $em->persist($mp);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', "Votre demande à été envoyée au fondateur de la faction !");

                return $this->redirect($this->generateUrl('maxcraft_messages'));
            }

            $validator = $this->get('validator');
            $errorList = $validator->validate($mp);

            foreach($errorList as $error)
            {
                $this->get('session')->getFlashBag()->add('alert', $error->getMessage());
            }

        }

        return $this->render('MaxcraftDefaultBundle:Faction:factionrequest.html.twig', array(
            'form' => $form->createView(),
            'faction' => $faction,
        ));
    }

    /**
     * @param $userId
     * @param $factionTag
     * @param Request $request
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editmemberAction($userId, $factionTag, Request $request){

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User');
        $user= $rep->findOneById($userId);


        if($user == NULL)
        {
            throw $this->createNotFoundException('Le joueur n\'est pas inscrit !');
        }

        if(!$this->getUser()->isFactionOwner())
        {
            throw $this->createNotFoundException('Vous n\'êtes pas chef de faction !');
        }


        if($this->getUser() == $user)
        {
            throw $this->createNotFoundException('Vous ne pouvez pas vous éditer vous même !');
        }

        if($user->getFaction() != $this->getUser()->getFaction())
        {
            throw $this->createNotFoundException('Ce joueur n\'est pas dans votre faction !');
        }

        $faction = $user->getFaction();

        $grades = array(
            '1' => 'Recrue',
            '2' => 'Membre',
            '9' => 'Chef',
            '10' => 'Fondateur (Attention ! Vous perdrez votre grade !)',
        );

        //form
        $form = $this->createFormBuilder($user)
            ->add('factionrole', 'choice', array('choices' => $grades))
            ->add('Sauvegarder', new SubmitType())
            ->getForm();

        //recuperation form

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);



            if($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();

                if($user->getFactionRole() == 10)
                {
                    //Changement de chef de faction
                    $oldowner = $user->getFaction()->getOwner();
                    $oldowner->setFactionRole(9);
                    $em->persist($oldowner);
                    $faction = $user->getFaction();
                    $faction->setOwner($user);
                    $em->persist($faction);

                    $this->notifFaction($faction, '<strong>'.$user->getUsername().'</strong> est devenu responsable de votre faction.');
                }


                $em->persist($user);

                $notif = new Notification();
                $notif->setContent('Le role <strong>'.$user->getFactionTitle().'</strong> vous à été attribué dans votre faction.');
                $notif->setType('FACTION');
                $notif->setUser($user);
                $em->persist($notif);

                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'Le membre <strong>'.$user->getUsername().'</strong> à bien été édité.');


                return $this->redirect($this->generateUrl('maxcraft_faction', array('factionTag' => $faction->getTag())));
            }

            $validator = $this->get('validator');
            $errorList = $validator->validate($user);

            foreach($errorList as $error)
            {
                $this->get('session')->getFlashBag()->add('alert', $error->getMessage());
            }

        }


        return $this->render('MaxcraftDefaultBundle:Faction:editmember.html.twig', array(
            'user' => $user,
            'faction' => $faction,
            'form' => $form->createView(),
        ));

    }

    /**
     * @param $userId
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function kickmemberAction($userId){

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User');
        $user= $rep->findOneById($userId);


        if($user == NULL)
        {
            throw $this->createNotFoundException('Le joueur n\'est pas inscrit !');
        }

        if(!$this->getUser()->isFactionOwner())
        {
            throw $this->createNotFoundException('Vous n\'êtes pas fondateur de faction !');
        }


        if($this->getUser() == $user)
        {
            throw $this->createNotFoundException('Vous ne pouvez pas vous éditer vous même !');
        }

        if($user->getFaction() != $this->getUser()->getFaction())
        {
            throw $this->createNotFoundException('Ce joueur n\'est pas dans votre faction !');
        }

        $em = $this->getDoctrine()->getManager();

        $user->setFaction(NULL);
        $user->setFactionRole(0);
        $em->persist($user);
        $notif = new Notification();
        $notif->setType('FACTION');
        $notif->setUser($user);
        $notif->setContent('Vous avez été exclu de votre faction !');
        $notif->setView(false);
        $em->persist($notif);
        $em->flush();


        $this->get('session')->getFlashBag()->add('info', 'Vous avez exclu '.$user->getUsername().' de votre faction !');

        return $this->redirect($this->generateUrl('maxcraft_faction', array('factionTag' => $this->getUser()->getFaction()->getTag())));

    }

    /**
     * @param $factionTag
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */
    public function editfactionAction($factionTag, Request $request){

        if(!$this->getUser()->isFactionOwner())
        {
            throw $this->createNotFoundException('Vous n\'êtes pas fondateur de faction !');
        }

        $faction = $this->getUser()->getFaction();

        $albumslist = $this->getDoctrine()->getManager()
            ->createQuery('SELECT a FROM MaxcraftDefaultBundle:Album a')
            ->getResult();

        $albums = array();

        foreach($albumslist as $album)
        {
            $albums[$album->getId()] = $album->getName().' ('.$album->getUser()->getUsername().')';
        }

        //form

        define("ID", $this->getUser()->getId());

        $form = $this->createFormBuilder($faction)
            ->add('name', 'text')
            ->add('description', 'froala', array('required' => false))
            ->add('joininfo', 'froala', array('required' => false))
            ->add('icon', 'text', array('required' => false))
            ->add('album', 'entity', array(
                'class' => 'MaxcraftDefaultBundle:Album',
                'query_builder' => function(AlbumRepository $er, $id = ID) {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.id', 'ASC')
                        ->where('a.user = '.$id);
                },
                'choice_label' => 'name',
                'group_by' => 'album.user.username',
                'placeholder' => 'Aucun',
                'empty_data'  => null,
                'required' => false,
            ))
            ->getForm();

        //traitement
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);



            if($form->isValid())
            {


                $em = $this->getDoctrine()->getManager();
                $em->persist($faction);
                $em->flush();

                //maxcraft
                new LoadFactionRequest($faction);

                $this->get('session')->getFlashBag()->add('info', 'Les paramètres de la faction on étés modifiés.');

                return $this->redirect($this->generateUrl('maxcraft_faction', array('factionTag' => $faction->getTag())));
            }

            $validator = $this->get('validator');
            $errorList = $validator->validate($faction);

            foreach($errorList as $error)
            {
                $this->get('session')->getFlashBag()->add('alert', $error->getMessage());
            }

        }


        return $this->render('MaxcraftDefaultBundle:Faction:editfaction.html.twig', array(
            'faction' => $faction,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param $factionTag
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addFactionEnemyAction($factionTag){

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Faction');
        $faction= $rep->findOneByTag($factionTag);

        if($faction == NULL)
        {
            throw $this->createNotFoundException('La faction "'.strtoupper($factionTag).'" n\'existe pas !');
        }

        $user = $this->getUser();
        if(!$user->isFactionOwner())
        {
            throw $this->createNotFoundException('Vous n\'êtes pas chef de faction');
        }

        $em = $this->getDoctrine()->getManager();

        //supression du state existant
        $state = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:FactionRole')->findStateObject($faction, $user->getFaction());


        if($state AND ($state->getHasRole() == 'ENEMY' OR $state->getState() == 'FRIEND'))
        {
            throw $this->createNotFoundException('Cette faction n\'est pas neutre !');
        }

        if($state)
        {
            $em->remove($state);
        }

        //nouveau state

        $fs = new FactionRole();
        $fs->setFaction1($user->getFaction());
        $fs->setFaction2($faction);
        $fs->setHasRole('ENEMY');
        $em->persist($fs);
        $em->flush();

        //maxcraft
        new LoadFactionRequest($user->getFaction());
        new LoadFactionRequest($faction);


        $this->get('session')->getFlashBag()->add('info', 'Vous avez ajouté la faction '.strtoupper($factionTag).' aux factions enemies !');

        return $this->redirect($this->generateUrl('maxcraft_faction', array('factionTag' => strtoupper($factionTag))));
    }

    /**
     * @param $factionTag
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeFactionStateAction($factionTag){

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Faction');
        $faction= $rep->findOneByTag($factionTag);

        if($faction == NULL)
        {
            throw $this->createNotFoundException('La faction "'.strtoupper($factionTag).'" n\'existe pas !');
        }

        $user = $this->getUser();
        if(!$user->isFactionOwner())
        {
            throw $this->createNotFoundException('Vous n\'êtes pas chef de faction');
        }

        $em = $this->getDoctrine()->getManager();

        //supression du state existant
        $state = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:FactionRole')->findStateObject($faction, $user->getFaction());

        if($state->getState() == 'ENEMY' AND $state->getFaction2() == $user->getFaction())
        {
            throw $this->createNotFoundException('Vous ne pouvez pas retirer cette faction de vos enemies !');
        }

        if($state)
        {
            $em->remove($state);
        }

        $em->flush();



        $this->get('session')->getFlashBag()->add('info', 'La faction '.strtoupper($factionTag).' est désormais neutre !');

        return $this->redirect($this->generateUrl('maxcraft_faction', array('tag' => strtoupper($factionTag))));
    }
}