<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ItemList;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

#Form Components
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ShoppingCartController extends Controller
{
    /**
     * @Route("/", name="cart")
     */
    public function indexAction()
    {
        $data['item_list']=$this->getDoctrine()
            ->getRepository('AppBundle:ItemList')
            ->findAll();

        return $this->render('cart/index.html.twig',$data);
    }


    /**
     * @Route("/cart/create", name="cart_create")
     */
    public function createAction(Request $request)
    {
        $items=new ItemList;

        $form=$this->createFormBuilder($items)
            ->add('name',TextType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:5px')))
            ->add('category',ChoiceType::class,array('choices'=>array('Phone'=>'Phone','Computer'=>'Computer'), 'attr'=>array('class'=>'form-control','style'=>'margin-bottom:5px')))
            ->add('description',TextareaType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:5px')))
            ->add('save',SubmitType::class,array('label'=>'Create','attr'=>array('class'=>'btn btn-success','style'=>'margin-bottom:5px')))
            ->getForm();

        $form->handleRequest($request);

        //Submit Response
        if($form->isSubmitted() && $form->isValid()){
            //Get Data
            $name=$form['name']->getData();
            $category=$form['category']->getData();
            $description=$form['description']->getData();

            $items->setName($name);
            $items->setCategory($category);
            $items->setDescription($description);

            //Save Data
            $em=$this->getDoctrine()->getManager();
            $em->persist($items);
            $em->flush();

            //Message Display
            $this->addFlash(
                'notice',
                array(
                    'message'=>'Item Added',
                    'class'=>'success'
                )
            );

            //Redirect after Success
            #base url
            $base_url = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

            return $this->redirectToRoute('cart'); #use the route name here
        }else {

            return $this->render('cart/create.html.twig',array(
                'form'=>$form->createView()
            ));
        }



    }

    /**
     * @Route("/cart/edit/{id}", name="cart_edit")
     */
    public function editAction($id,Request $request)
    {
        $items=$this->getDoctrine()
            ->getRepository('AppBundle:ItemList')
            ->find($id);

        $items->setName($items->getName());
        $items->setCategory($items->getCategory());
        $items->setDescription($items->getDescription());

        $form=$this->createFormBuilder($items)
            ->add('name',TextType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:5px')))
            ->add('category',ChoiceType::class,array('choices'=>array('Phone'=>'Phone','Computer'=>'Computer'), 'attr'=>array('class'=>'form-control','style'=>'margin-bottom:5px')))
            ->add('description',TextareaType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:5px')))
            ->add('save',SubmitType::class,array('label'=>'Update','attr'=>array('class'=>'btn btn-success','style'=>'margin-bottom:5px')))
            ->getForm();

        $form->handleRequest($request);

        //Submit Response
        if($form->isSubmitted() && $form->isValid()){
            //Get Data
            $name=$form['name']->getData();
            $category=$form['category']->getData();
            $description=$form['description']->getData();

            $em=$this->getDoctrine()->getManager();
            $items=$em->getRepository('AppBundle:ItemList')->find($id);

            $items->setName($name);
            $items->setCategory($category);
            $items->setDescription($description);

            //Save Data
            $em->flush();

            //Message Display
            $this->addFlash(
                'notice',
                array(
                    'message'=>'Item Updated',
                    'class'=>'warning'
                )
            );

            //Redirect after Success
            #base url
            $base_url = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

            return $this->redirectToRoute('cart'); #use the route name here
        }else {

            return $this->render('cart/edit.html.twig',array(
                'form'=>$form->createView()
            ));
        }
    }

    /**
     * @Route("/cart/details/{id}", name="cart_details")
     */
    public function detailsAction($id)
    {
        $items=$this->getDoctrine()
            ->getRepository('AppBundle:ItemList')
            ->find($id);

        $data['item_details']=$items;
        return $this->render('cart/details.html.twig',$data);
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete")
     */
    public function deleteAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $items=$em->getRepository('AppBundle:ItemList')->find($id);

        $em->remove($items);
        $em->flush();

        //Message Display
        $this->addFlash(
            'notice',
            array(
                'message'=>'Item Removed',
                'class'=>'danger'
            )
        );

        return $this->redirectToRoute('cart'); #use the route name here
    }

}
