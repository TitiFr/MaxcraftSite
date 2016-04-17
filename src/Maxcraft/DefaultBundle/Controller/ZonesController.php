<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/01/16
 * Time: 21:27
 */

namespace Maxcraft\DefaultBundle\Controller;


use Maxcraft\DefaultBundle\Entity\AlbumRepository;
use Maxcraft\DefaultBundle\Entity\Builder;
use Maxcraft\DefaultBundle\Websocket\Requests\LoadZoneRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;


class ZonesController extends Controller
{
    /**
     * @param $zoneId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @internal param Request $request
     * @Security("has_role('ROLE_USER')")
     */
    public function parcelleAction($zoneId){


        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Zone');
        $zone= $rep->findOneById($zoneId);

        if($zone == NULL)
        {
            throw $this->createNotFoundException('Cette parcelle n\'existe pas ! (id : '.$zoneId.')');
        }

        //$mapUrl = $this->container->getParameter('map_url');
        //$mapUrl .= '?worldname='.$zone->getWorld().'&mapname=flat&zoom=4&x='.$zone->getCenter()['x'].'&y=64&z='.$zone->getCenter()['z'];

        //cuboiders

        $cuboiders = $this->getDoctrine()->getManager()
            ->createQuery('SELECT b FROM MaxcraftDefaultBundle:Builder b WHERE b.role = \'CUBO\' AND b.zone = '.$zone->getId())
            ->getResult();

        $builders = $this->getDoctrine()->getManager()
            ->createQuery('SELECT b FROM MaxcraftDefaultBundle:Builder b WHERE b.role = \'BUILD\' AND b.zone = '.$zone->getId())
            ->getResult();

        //zone filles
        $filles =  $this->getDoctrine()->getManager()
            ->createQuery('SELECT z FROM MaxcraftDefaultBundle:Zone z WHERE z.parent = '.$zone->getId())
            ->getResult();


        //vente
        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:OnSaleZone');
        $vente = $rep->findOneByZone($zone);

        //TODO à faire !!!! (shops)
        /*//shops
        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Shop');
        $shops = $rep->findByZone($zone->getId());*/

        return $this->render('MaxcraftDefaultBundle:Zones:parcelle.html.twig', array(
            'zone' => $zone,
            //'mapurl' => $mapUrl,
            'cuboiders' => $cuboiders,
            'builders' => $builders,
            'filles' => $filles,
            'vente' => $vente,
            //'shops' => $shops,
        ));
    }

    public function zoneAction($zone)
    {

        //En vente ?

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:OnSaleZone');
        $vente = $rep->findOneByZone($zone);


        return $this->render('MaxcraftDefaultBundle:Zones:zone.html.twig', array(
            'parcelle' => $zone,
            'vente' => $vente,
        ));
    }

    /**
     * @param $zoneId
     * @param Request $request
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editParcelleAction($zoneId, Request $request){

        $user = $this->getUser();

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Zone');
        $zone= $rep->findOneById($zoneId);

        if($zone == NULL)
        {
            throw $this->createNotFoundException('Cette parcelle n\'existe pas ! (id : '.$zoneId.')');
        }

        if(!($user->getRole() == 'ROLE_ADMIN' OR $zone->getServZone()->getOwner() == $user))
        {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette parcelle !');
        }

        define("ID", $this->getUser()->getId());

        //FORM
        $form = $this->createFormBuilder($zone)
            ->add('name', 'text')
            ->add('description', 'froala', array('required' => false))
            ->add('album', 'entity', array(
                'class' => 'MaxcraftDefaultBundle:Album',
                'query_builder' => function(AlbumRepository $er, $id = ID) {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.id', 'ASC')
                        ->where('a.user = '.$id);
                },
                'choice_label' => 'name',
                'group_by' => 'album.user',
                'placeholder' => 'Aucun',
                'empty_data'  => null,
                'required' => false,
            ))
            ->getForm();

        //POST FORM

        //recuperation form

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);


            if($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();

                $em->persist($zone);


                $em->flush();

                //maxcraft
                new LoadZoneRequest($zone);


                $this->get('session')->getFlashBag()->add('info', 'Les informations de la parcelle ont été modifiées.');
                return $this->redirect($this->generateUrl('parcelle', array('zoneId' => $zone->getId())));
            }

            $validator = $this->get('validator');
            $errorList = $validator->validate($zone);

            foreach($errorList as $error)
            {
                $this->get('session')->getFlashBag()->add('alert', $error->getMessage());
            }

        }


        return $this->render('MaxcraftDefaultBundle:Zones:editparcelle.html.twig', array(
            'form' => $form->createView(),
            'zone' => $zone,
        ));

    }

    /**
     * @param $zoneId
     * @param Request $request
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newZoneUserAction($zoneId, Request $request){

        $user = $this->getUser();

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Zone');
        $zone= $rep->findOneById($zoneId);

        if($zone == NULL)
        {
            throw $this->createNotFoundException('Cette parcelle n\'existe pas ! (id : '.$zoneId.')');
        }

        if(!($user->getRole() == 'ROLE_ADMIN' OR $zone->getUser() == $user))
        {
            throw $this->createNotFoundException('Vous ne pouvez pas modifier cette parcelle !');
        }

        $zu = new Builder();



        //recuperation form

        if ($request->isMethod('POST')) {

            if($request->get('pseudo') AND $request->get('role'))
            {
                $pseudo = $request->get('pseudo');
                $role = $request->get('role');
                //Formulaire rempli

                $builder = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User')->findOneByUsername($pseudo);

                if($builder == null)
                {
                    throw $this->createNotFoundException('Ce joueur n\'existe pas !');
                }

                $isalreadyuser = $this->getDoctrine()->getManager()->createQuery('SELECT COUNT(b.id) FROM MaxcraftDefaultBundle:Builder b WHERE b.user = '.$builder->getId().' AND b.zone = '.$zone->getId())->getSingleScalarResult();


                if($isalreadyuser OR $zone->getOwner() == $builder)
                {
                    throw $this->createNotFoundException('Ce joueur à déjà des droits sur ce terrain ! Vous devez les retirer avant de pouvoir en ajouter !');
                }

                if($role != 'CUBO' AND $role != 'BUILD')
                {
                    throw $this->createNotFoundException('Ce type n\'est pas valide');

                }

                //CREATION
                $zn = new Builder();
                $zn->setUser($builder);
                $zn->setZone($zone);
                $zn->setRole($role);

                $em = $this->getDoctrine()->getManager();
                $em->persist($zn);
                $em->flush();

                new LoadZoneRequest($zone);

                $this->get('session')->getFlashBag()->add('info', 'Les droits ont été ajoutés à '.$builder->getUsername());
                return $this->redirect($this->generateUrl('parcelle', array('zoneId' => $zone->getId())));


            }
        }


        return $this->render('MaxcraftDefaultBundle:Zones:newzoneuser.html.twig', array(
            'zone' => $zone,
        ));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_USER')")
     */
    public function removeZoneUserAction($id){

        $user = $this->getUser();

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Builder');
        $zoneuser = $rep->findOneById($id);

        if($zoneuser == NULL)
        {
            throw $this->createNotFoundException('Ce droit n\'existe pas !');
        }

        if(!($user->getRole() == 'ROLE_ADMIN' OR $zoneuser->getZone()->getOwner() == $user))
        {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce droit !');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($zoneuser);
        $em->flush();

        new LoadZoneRequest($zoneuser->getZone());

        $this->get('session')->getFlashBag()->add('info', 'Les droits ont étés supprimés !');
        return $this->redirect($this->generateUrl('parcelle', array('zoneId' => $zoneuser->getZone()->getId())));
    }


    public function ventesAction(){

        $ventes = $this->getDoctrine()->getManager()
            ->createQuery('SELECT osz FROM MaxcraftDefaultBundle:OnSaleZone osz ORDER BY osz.price ASC')
            ->getResult();

        return $this->render('MaxcraftDefaultBundle:Zones:ventes.html.twig', array(
            'ventes' => $ventes,
            'nbventes' => count($ventes),
        ));
    }
}