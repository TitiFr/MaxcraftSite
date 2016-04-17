<?php

namespace Maxcraft\DefaultBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;
use Maxcraft\DefaultBundle\Entity\Builder;
use Maxcraft\DefaultBundle\Entity\Comment;
use Maxcraft\DefaultBundle\Entity\Image;
use Maxcraft\DefaultBundle\Entity\Player;
use Maxcraft\DefaultBundle\Entity\User;
use Maxcraft\DefaultBundle\Entity\Vote;
use Maxcraft\DefaultBundle\Entity\WebZone;
use Maxcraft\DefaultBundle\Entity\Zone;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{
    public function indexAction($page, Request $request) //TODO fnr
    {
        //Récupération Album principal
        $albumid = $this->container->getParameter('index_album');
        $album = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Album')->findOneById($albumid);
        if ($album != null) {
            $images = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Album')->findImages($album);
        }
        else {
            $images = null;
        }

        //news par page
        $parpage = $this->container->getParameter('news_par_page');

        //Récupération des news
        $repNews = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:News');
        $news = $repNews->findByPage($page,$parpage);

        $totalNews = $repNews->countDisplay();
        $totalPages = ceil(($totalNews)/($parpage));
        $newslist = array();
        $commentFormList = array();
        foreach ($news as $new){
            $newslist[$new->getId()]['news'] = $new;
            $newslist[$new->getId()]['comments'] = $repNews->getComments($new->getId());
            $newslist[$new->getId()]['nbcomments'] = count($newslist[$new->getId()]['comments']);

            //préparation ajout commentaire
            $comment = new Comment();
            $comment->setNews($new);
            $newslist[$new->getId()]['comment'] = $comment;

            $newslist[$new->getId()]['form'] = $this->createFormBuilder($comment)
                ->add('content', 'froala', array('required' => true))
                ->add('newsid', 'hidden', array(
                    'data' => $new->getId()
                ))
                ->getForm();

            $newslist[$new->getId()]['commentform'] =  $newslist[$new->getId()]['form']
                ->createView();
            $commentFormList[$new->getId()] = $newslist[$new->getId()]['form']
                ->createView();
            $commentFormList[$new->getId()] = $newslist[$new->getId()]['form']->createView();
        }

        if ($request->isMethod('POST')){
            foreach ($newslist as $new)
            {
                $form = $new['form'];
                $form->handleRequest($request);

                if($form->isValid() AND $new['news']->getId() == $new['comment']->getNewsId())
                {

                    $new['comment']->setNews($new['news']);
                    $new['comment']->setUser($this->getUser());

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($new['comment']);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add('info', 'Votre commentaire pour la news << '.$new['news']->getTitle().' >> à été posté.');

                    return $this->redirect($this->generateUrl('maxcraft_default_blog', array(
                        'page' => $page,
                    )));
                }

            }
        }



        return $this->render('MaxcraftDefaultBundle:Default:index.html.twig', array(
            'newslist' => $newslist,
            'totalpages' => $totalPages,
            'page' => $page,
            'album' => $album,
            'images' => $images,
            'commentformList' => $commentFormList

        ));


    }



    public function registerAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        //TODO Aller chercher Règlement

        $page = $this->getDoctrine()->getRepository("MaxcraftDefaultBundle:Page")->findByRoute('reglements');
        $sections = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:PageSection')->findByPage($page);

        $annees = array();

        for($i=2016;$i>=1940;$i--)
        {
            $annees[$i] = $i;

        }

        $user = new User($this, null);
        $userForm = $this->createFormBuilder($user)
            ->add('username', new TextType())
            ->add('email', new TextType())
            ->add('password', new RepeatedType(), array(
                'type' => new PasswordType(),
                'invalid_message' => 'Vous avez mal recopié votre mot de passe !'
            ))
            ->add('naissance', new ChoiceType(), array(
                'choices' => $annees,
                'empty_value' => 'Choisisez',
                'empty_data' => null
            ))
            ->add('activite', new TextType(), array(
                'required' => false
            ))
            ->add('loisirs', new TextType(), array(
                'required' => false
            ))
            ->add('fromwhere', new TextareaType())
            ->add('Valider', new SubmitType())
            ->getForm();

        if ($request->isMethod('POST')){
            $userForm->handleRequest($request);

            //Test si dejà visité
            $havevisited = $em->getRepository('MaxcraftDefaultBundle:Player')->haveVisited($user->getUsername());
            if ($havevisited != true) 	$this->get('session')->getFlashBag()->add('alert', 'Vous devez vous connecter en jeu à maxcraft.fr pour pouvoir vous inscrire !');

            if ($userForm->isValid() && $havevisited == true ){

                $player = $em->getRepository('MaxcraftDefaultBundle:Player')->findOneByPseudo($user->getUsername());
                $user->setPlayer(($player));
                $user->setUuid($player->getUuid());
                $user->setSpleeping(false);

                $user->cryptePassword($user->getPassword()); //cryptage md5
                $user->setIp($this->container->get('request')->getClientIp());

                $user->setRole('ROLE_USER');

                $em->persist($user, $player);
                $em->flush();

                //TODO gérer permissions sur serveur + forum (dire qu'il est inscris)
                //new registeredPlayer($user->getPlayer())

                $this->get('session')->getFlashBag()->add('info', 'Votre inscription est terminée ! Vous pouvez à présent vous connecter.');

                return $this->redirect($this->generateUrl('maxcraft_homepage'));
            }

            $validator = $this->get('validator');
            $errorList = $validator->validate($user);

            foreach($errorList as $error)
            {
                $this->get('session')->getFlashBag()->add('alert', $error->getMessage());

            }
        }

        return $this->render('MaxcraftDefaultBundle:Default:register.html.twig', array(
            'form' => $userForm->createView(),
            'page'=>$page,
            'sections' =>$sections
        ));
    }

    /**
     * @param $albumId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uploadImageAction($albumId, Request $request){

        $file = $request->files->get('file');
        if($file == NULL)
        {
            throw $this->createNotFoundException('Une erreur s\'est produite !');
        }

        $em = $this->getDoctrine()->getManager();
        if ( !($album= $em->getRepository('MaxcraftDefaultBundle:Album')->findOneById($albumId))) throw $this->createNotFoundException('Cet Album n\'existe pas!');

        $image = new Image();

        $image->setFile($file);

        $image->upload();

        if (count($album->getImages()) == 0) $album->setAlbumimage($image);
        $album->addImage($image);
        $image->setAlbum($album);


        $em->persist($image);
        $em->flush();

        $this->get('session')->getFlashBag()->add('info', "Image(s) ajoutée(s) avec succès !");

        return $this->render('MaxcraftDefaultBundle:Default:response.html.twig', array(
            'display' => $file,
        ));

    }

    public function changeAlbumImageAction($albumid, $imageid){
        $this->islogged();

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Album');
        $album = $rep->find($albumid);

        if($album == NULL)
        {
            throw $this->createNotFoundException('L\'album '.$albumid.' n\'existe pas !');
        }

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Image');
        $image = $rep->find($imageid);

        if($image == NULL)
        {
            throw $this->createNotFoundException('Cette image n\'existe pas !');
        }

        $album->setAlbumimage($image);

        $em = $this->getDoctrine()->getManager();
        $em->persist($album);
        $em->flush();

        $this->get('session')->getFlashBag()->add('info', 'L\'image à été changée !');

        return $this->redirect($this->generateUrl('maxcraft_album_edit', array(
            'albumId' => $albumid,
        )));
    }

    public function removeImageAction($imageid){
        $this->islogged();

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Image');
        $image = $rep->find($imageid);

        if($image == NULL)
        {
            throw $this->createNotFoundException('Cette image n\'existe pas !');
        }
        $image->remove();
        $em = $this->getDoctrine()->getManager();
        if($image->getAlbum()->getAlbumimage() == $image)
        {
            $album = $image->getAlbum();
            $album->setAlbumimage(NULL);
            $em->persist($album);
        }

        $em->remove($image);

        $em->flush();

        $this->get('session')->getFlashBag()->add('info', 'L\'image à été supprimée !');

        return $this->redirect($this->generateUrl('maxcraft_album_edit', array(
            'albumId' => $image->getAlbum()->getId()
        )));
    }



    public function islogged()
    {
        if(!($this->get('security.context')->isGranted('ROLE_USER')))
        {
            throw $this->createNotFoundException('Vous devez vous connecter pour acceder à cette page !');
        }
        else
        {
            if($this->getUser()->getBanned())
            {
                throw $this->createNotFoundException('Vous êtes banni de ce site ! Contactez un admin en cas de problème.');
            }
        }
    }

    public function isAdmin()
    {
        if(!($this->get('security.context')->isGranted('ROLE_ADMIN')))
        {
            throw $this->createNotFoundException('Vous devez être admin pour acceder à cette page !');
        }
        else
        {
            if($this->getUser()->getBanned())
            {
                throw $this->createNotFoundException('Vous êtes banni de ce site ! Contactez un admin en cas de problème.');
            }
        }
    }

    public function isModo()
    {
        if(!($this->get('security.context')->isGranted('ROLE_MODO')))
        {
            throw $this->createNotFoundException('Vous devez être modo pour acceder à cette page !');
        }
        else
        {
            if($this->getUser()->getBanned())
            {
                throw $this->createNotFoundException('Vous êtes banni de ce site ! Contactez un admin en cas de problème.');
            }
        }
    }

    public function menuAction(){
        $pages = $this->getDoctrine()->getManager()->createQuery('SELECT p FROM MaxcraftDefaultBundle:Page p WHERE p.display = 1 ORDER BY p.ordervalue ASC')->getResult();

        if(($this->get('security.context')->isGranted('ROLE_USER')))
        {
            $nbnotif =  count($this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Notification')->findBy(
              array('user' => $this->getUser(),
                  'view' => false)
            ));
                /*->createQuery('SELECT count(n.id) FROM MaxcraftDefaultBundle:Notification n WHERE n.user = '.$this->getUser().' AND n.view = 0')
                ->getSingleScalarResult();*/

            $nbmp =  count($this->getDoctrine()->getManager()->getRepository('MaxcraftDefaultBundle:MP')->findBy(
                array('target' => $this->getUser(),
                    'view'=>false)
            ));
                /*->createQuery('SELECT count(m.id) FROM MaxcraftDefaultBundle:MP m WHERE m.target = '.$this->getUser().' AND m.view = 0')
                ->getSingleScalarResult();*/

            $alert = $nbmp + $nbnotif;


            //sites de classement

            $voteforus = $this->container->getParameter('voteforus');


            return $this->render('MaxcraftDefaultBundle:Others:menu.html.twig', array(
                'nbnotif' => $nbnotif,
                'nbmp' => $nbmp,
                'alert' => $alert,
                'pages' => $pages,
                'voteforus' => $voteforus
            ));

        }
        else
        {
            return $this->render('MaxcraftDefaultBundle:Others:menu.html.twig', array(
                'pages' => $pages
            ));
        }
    }

    public function wrapAction(){

        //Derniers inscris
        $lastregisteredusers = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User')->findBy(
          array(),
          array('registerDate' => 'desc'),
          12,
          0
        );

        //enligne
        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Session');
        $onlineUsers = $rep->getOnlineUsers();

        //sites de classement
        $voteforus = $this->container->getParameter('voteforus');


        return $this->render('MaxcraftDefaultBundle:Others:wrap.html.twig', array(
            'lastusers' => $lastregisteredusers,
            'voteforus' => $voteforus,
            'onlineusers' => $onlineUsers,
            'onlinenbr' => count($onlineUsers),
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function headerAction(){
        //list des joueurs cos

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Session');
        $onlineUsers = $rep->getOnlineUsers();
        $bukkitVersion = $this->container->getParameter('bukkit_version');

        $nbusers = count($this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User')->findAll());

        $youtube = $this->container->getParameter('youtube');
        $twitter = $this->container->getParameter('twitter');
        $facebook = $this->container->getParameter('facebook');


        return $this->render('MaxcraftDefaultBundle:Others:header.html.twig', array(
            'connected' => count($onlineUsers),
            'totalplayers' => $nbusers,
            'version' => $bukkitVersion,
            'onlineusers' => $onlineUsers,
            'youtube' => $youtube,
            'twitter' => $twitter,
            'facebook' => $facebook,

        ));
    }


    public function playersAction($parser, $page){

        $parpage = $this->container->getParameter('nb_joueurs_par_page');

        //Permier switch
        if ($parser == 'date'){
            $pagetitle = 'Les joueurs';
            $description = 'Voici la liste des joueurs classés du plus nouveau au plus ancien.';

            $nbtotal = count($this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User')->findAll());
        }
        if ($parser == 'gametime'){
            $pagetitle = 'Les joueurs plus aguerris';
            $description = 'Voici la liste des joueurs classés par temps de jeu.';

            $nbtotal = count($this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User')->findAll());
        }

        //Calcul du nombre de pages
        $nbpage = floor($nbtotal/$parpage)+1;
        $start = $parpage*($page-1);

        if($page > $nbpage)
        {
            return $this->render('MaxcraftDefaultBundle:Others:error.html.twig', array(
                'content' => 'Cette page n\'existe pas !'
            ));
        }

        //deuxieme switch

        if ($parser == 'date'){
            $players = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User')->findBy(
                array(),
                array('id' => 'desc'),
                $parpage,
                $start
            );
        }
        if ($parser == 'gametime'){
            $players = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User')->findBy(
                array(),
                array('gametime' => 'desc'),
                $parpage,
                $start
            );

        }

        return $this->render('MaxcraftDefaultBundle:Default:players.html.twig', array(
            'title' => $pagetitle,
            'description' => $description,
            'players' => $players,
            'nbtotal' => $nbtotal,
            'nbpages' => $nbpage,
            'page' => $page,
            'parser' => $parser,
            //pour aller chercher l'argent d'un joueur : user.balance (sur twig)
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function voteAction(){

        if(!($this->get('security.context')->isGranted('ROLE_USER')))
        {
            return $this->redirect($this->generateUrl('maxcraft_homepage'));
        }

        $voteforus = $this->container->getParameter('voteforus');
        $gains = $this->container->getParameter('vote_gains');


        foreach ($voteforus as $site)
        {
            if($site['dopped'])
            {
                $dopped = $site;
            }
        }

        $lastvote = new ArrayCollection();
        $lv = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Vote')->findBy(
          array('user' => $this->getUser()),
          array('date' => 'desc'),
          1
        );

        foreach($lv as $l){
            $lastvote->add($l);
        }

        $date = new \DateTime();
        if($lastvote == null || $lastvote->isEmpty()) $canvote = true;
        else
        {
            foreach ($lastvote as $llv){
                $ecart = $date->getTimestamp() - $lastvote[0]->getDate()->getTimestamp();
                break;
            }

            if($ecart <= 86400)
            {
                $canvote = false;
            }
            else $canvote = true;
        }

        //Calcul du gain

        // On trouve les votes depuis 7 jours


        $votes = new ArrayCollection();
        $votes->add($this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Vote')->findBy(
          array('user' => $this->getUser()),
          array('date'=>'desc')
        ));
            /*->createQuery('SELECT v FROM MaxcraftDefaultBundle:Vote v WHERE v.user = '.$this->getUser()->getId().' ORDER BY v.date DESC')
            ->getResult();*/


        //Repérage du votestreak
        $datevote = time();
        $streak = 0;
        foreach($votes as $vote)
        {
            foreach ($vote as $v) {
                $date = $v->getDate()->getTimestamp();
                if ($datevote - $date <= 172800) {
                    $streak++;
                    $datevote = $date;
                } else {
                    break;
                }
                break;
            }
        }

        //Modulo 7 pour réinitialisation

        $streak = $streak%7;



        foreach($gains as $gain)
        {
            if($gain['jours'] == $streak+1)
            {
                $mongain = $gain['gain'];
            }
        }



        if($canvote)
        {
            $vote = new Vote();
            $vote->setUser($this->getUser());
            $vote->setGain($mongain);

            $em = $this->getDoctrine()->getManager();
            $em->persist($vote);
            $em->flush();

            $this->getUser()->getPlayer()->setBalance($this->getUser()->getPlayer()->getBalance()+$mongain);


        }


        return $this->render('MaxcraftDefaultBundle:Default:vote.html.twig', array(
            'gains' => $gains,
            'canvote' => $canvote,
            'voteforus' => $voteforus,
            'dopped' => $dopped,
            'mongain' => $mongain,

        ));
    }


    public function playerFinderAction(Request $request){

        $pseudo = $request->query->get('form_pseudo');
        if(!$pseudo)
        {
            $this->get('session')->getFlashBag()->add('alert', 'Une erreur s\'est produite !');
            return $this->redirect($this->generateUrl('maxcraft_homepage'));
        }

        $userexists =  $this->getDoctrine()->getManager()
            ->createQuery('SELECT COUNT(u.id) FROM MaxcraftDefaultBundle:User u WHERE u.username = \''.$pseudo.'\'')
            ->getSingleScalarResult();

        if(!$userexists)
        {
            $this->get('session')->getFlashBag()->add('alert', 'Ce joueur n\'est pas inscrit sur maxcraft.fr !');
            return $this->redirect($this->generateUrl('maxcraft_homepage'));
        }


        return $this->redirect($this->generateUrl('profil', array('pseudo' => $pseudo)));
    }


    public function albumAction($albumId){

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Album');
        $album= $rep->findOneById($albumId);


        if($album == NULL)
        {
            throw $this->createNotFoundException('Cet album n\'est pas accessible !');
        }

        return $this->render('MaxcraftDefaultBundle:Default:album.html.twig', array(
            'album' => $album,

        ));
    }

    public function progressBarAction($long, $value, $total, $h, $color = 'green', $inversed= null){

        $bar = ($value/$total)*($long-2);
        $image = \Imagecreate($long, $h);

        if($color == 'green')
        {
            $black = \imagecolorallocate($image, 150, 150, 150);
            $blanc = \imagecolorallocate($image, 215, 215, 215);
            $vert = \imagecolorallocate($image, 106, 197, 62);
        }
        elseif ($color == 'red')
        {
            $black = \imagecolorallocate($image, 150, 150, 150);
            $blanc = \imagecolorallocate($image, 215, 215, 215);
            $vert = \imagecolorallocate($image, 139, 0, 0);
        }
        elseif($color == 'auto')
        {
            $black = \imagecolorallocate($image, 150, 150, 150);
            $blanc = \imagecolorallocate($image, 215, 215, 215);
            $color = $this->colorDegrade($value/$total, array('r' => 199, 'g' => 0, 'b' => 0), array('r' => 133, 'g' => 235, 'b' => 0));
            $vert = \imagecolorallocate($image, $color['r'], $color['g'], $color['b']);
        }

        \ImageFilledRectangle ($image, 0, 0, $long, 1, $black);
        \ImageFilledRectangle ($image, 1, 1, $long-2, $h-2, $blanc);
        \ImageFilledRectangle ($image, 1, 1, $bar, $h-2, $vert);

        if($inversed)
        {
            $image = imagerotate($image, 180, 0);
        }

        imagepng($image);
        $reponse = new Response();




        $reponse->headers->set('Content-Type', 'image/png');
        return $reponse;
    }

    public function colorDegrade($value, $startColor, $endColor)
    {
        $color = array();
        //RED


        $color['r'] = round($startColor['r'] + ($endColor['r']-$startColor['r'])*$value);

        //GREEN


        $color['g'] = round($startColor['g'] + ($endColor['g']-$startColor['g'])*$value);

        //BLUE


        $color['b'] = round($startColor['b'] + ($endColor['b']-$startColor['b'])*$value);

        return $color;
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param $name
     * @param $points
     * @param $world
     * @return string
     */
    public function testAddZoneAction($name, $points, $world){

        $zone = new Zone();

        $wzone = new WebZone($zone);
        $zone->setWebZone($wzone);



        $wzone->setShopDemand(false);
        $wzone->setDescription('Zone générée pour des tests');
        $zone->setName($name);
        $wzone->setName($zone->getName());
        $zone->setPoints($points);
        $zone->setWorld($world);

        dump($zone, $wzone);

        $em = $this->getDoctrine()->getManager();
        $em->persist($wzone, $zone);
        $em->flush();

        return 'OK';
    }
}


