<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/01/16
 * Time: 21:25
 */

namespace Maxcraft\DefaultBundle\Controller;


use Maxcraft\DefaultBundle\Entity\Page;
use Maxcraft\DefaultBundle\Entity\PageSection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class GuideController extends Controller
{

    public function guideAction($page){

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Page');
        $page = $rep->findOneByRoute($page);


        if($page == NULL)
        {
            throw $this->createNotFoundException('Cette page du guide n\'existe pas');
        }

        $sections = $this->getDoctrine()->getManager()->createQuery('SELECT s FROM MaxcraftDefaultBundle:PageSection s WHERE s.page = '.$page->getId().' AND s.display = 1 ORDER BY s.ordervalue ASC')->getResult();

        return $this->render('MaxcraftDefaultBundle:Guide:guide.html.twig', array(
            'page' => $page,
            'sections' => $sections,
        ));
    }


    /**
     * @param null $pageId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editpageAction($pageId = null, Request $request){

        $em = $this->getDoctrine()->getManager();

        if($pageId == null)
        {
            $page = new Page();
        }
        else
        {
            $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Page');
            $page= $rep->findOneById($pageId);
        }

        if($page == null)
        {
            throw $this->createNotFoundException('Cette page n\'existe pas !');
        }

        //form
        $form = $this->createFormBuilder($page)
            ->add('title', 'text')
            ->add('route', 'text')
            ->add('ordervalue', 'integer')
            ->add('display', 'choice', array('choices' => array('1' => 'Oui', '0' => 'Non')))
            ->getForm();

        //traitement
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if($form->isValid())
            {



                $em->persist($page);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'La page a été editée !');

                return $this->redirect($this->generateUrl('admin_guide'));
            }

            $validator = $this->get('validator');
            $errorList = $validator->validate($page);

            foreach($errorList as $error)
            {
                $this->get('session')->getFlashBag()->add('alert', $error->getMessage());
            }

        }



        return $this->render('MaxcraftDefaultBundle:Guide:editpage.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function sectionsAction($page){

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Page');
        $page = $rep->findOneByRoute($page);



        if($page == NULL)
        {
            throw $this->createNotFoundException('Cette page du guide n\'existe pas');
        }


        $sections = $this->getDoctrine()->getManager()->createQuery('SELECT s FROM MaxcraftDefaultBundle:PageSection s WHERE s.page = '.$page->getId().' ORDER BY s.ordervalue ASC')->getResult();

        return $this->render('MaxcraftDefaultBundle:Guide:sections.html.twig', array(
            'page' => $page,
            'sections' => $sections,
        ));
    }

    /**
     * @param null $sectionId
     * @param Request $request
     * @Security("has_role('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editsectionAction($sectionId = null, Request $request){

        $em = $this->getDoctrine()->getManager();

        if($sectionId == null)
        {
            $section = new PageSection();
        }
        else
        {
            $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:PageSection');
            $section= $rep->findOneById($sectionId);
        }

        if($section == null)
        {
            throw $this->createNotFoundException('Cette section n\'existe pas !');
        }

        //form
        $form = $this->createFormBuilder($section)
            ->add('title', 'text')
            ->add('content', 'froala')
            ->add('ordervalue', 'integer')
            ->add('page', 'entity', array(
                'class' => 'MaxcraftDefaultBundle:Page',
                'property' => 'title'
            ))
            ->add('display', 'choice', array('choices' => array('1' => 'Oui', '0' => 'Non')))
            ->getForm();

        //traitement

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);



            if($form->isValid())
            {



                $em->persist($section);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'La section a été editée !');

                return $this->redirect($this->generateUrl('admin_editsection', array('sectionId' =>  $section->getId())));
            }

            $validator = $this->get('validator');
            $errorList = $validator->validate($section);

            foreach($errorList as $error)
            {
                $this->get('session')->getFlashBag()->add('alert', $error->getMessage());
            }

        }



        return $this->render('MaxcraftDefaultBundle:Guide:editsection.html.twig', array(
            'form' => $form->createView(),
            'section' => $section,
        ));
    }

    /**
     * @param $sectionId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function removeSectionAction($sectionId){

        $em = $this->getDoctrine()->getManager();

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:PageSection');
        $section= $rep->findOneById($sectionId);

        if($section == null)
        {
            throw $this->createNotFoundException('Cette section n\'existe pas !');
        }
        $page = $section->getPage();
        $em->remove($section);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_sections', array(
            'page' => $page->getRoute()
        )));
    }

    /**
     * @param $pageId
     * @Security("has_role('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removePageAction($pageId){

        $em = $this->getDoctrine()->getManager();

        $rep = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Page');
        $page= $rep->findOneById($pageId);

        if($page == null)
        {
            throw $this->createNotFoundException('Cette page n\'existe pas !');
        }

        $sections = $this->getDoctrine()->getManager()->createQuery('SELECT s FROM MaxcraftDefaultBundle:PageSection s WHERE s.page = '.$page->getId().' ')->getResult();

        foreach($sections as $section)
        {
            $em->remove($section);
        }
        $em->flush();
        $em->remove($page);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_guide'));
    }

    public function guideMenuAction($pageId)
    {

        $pages = $this->getDoctrine()->getManager()->createQuery('SELECT p FROM MaxcraftDefaultBundle:Page p WHERE p.display = 1 ORDER BY p.ordervalue ASC')->getResult();

        return $this->render('MaxcraftDefaultBundle:Guide:guidemenu.html.twig', array(
            'pages' => $pages,
            'id' => $pageId,
        ));
    }
}