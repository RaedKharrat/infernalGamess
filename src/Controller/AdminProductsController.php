<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Date;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Form\ProductFormType;

class AdminProductsController extends AbstractController
    /**
     * @Route("/admin/products")
     */
{
    /**
     * @Route("/", name="admin_products")
     */
    public function index(ProductRepository $repo): Response
    {
        $products = $repo->findAll();

        return $this->render('admin_products/index.html.twig',[
            "products" =>$products,
        ]);
    }

    /**
     * @Route("/new", name="admin_products_new", methods={"GET", "POST"})
     */

   
    public function addProduct(Request $request ): Response{
        $product= new Product();
           $form = $this->createForm(ProductFormType::class,$product);
           $form->handleRequest($request);

           if($form->isSubmitted() && $form->isValid())
           {

            $picture = $form['picture']->getData();
            $newPictureName = $product->getName().'.'.$picture->getExtension();
            $picture->move(
                $this->getParameter('PicturesProducts'),
                $newPictureName
            );
            $product->setPicture($newPictureName);
               $entityManager = $this->getDoctrine()->getManager();
               $entityManager->persist($product);
               $entityManager->flush();
               return $this->redirectToRoute("admin_products");

           }

           return $this->render("admin_products/new.html.twig",[
               'product' =>$product,
               'form' => $form->createView()
           ]);
    }

    /**
     * @Route("/edit/{id}", name="admin_products_edit", methods={"GET", "POST"})
     */
    public function edit(ProductRepository $repo,Request $request, EntityManagerInterface $entityManager,int $id): Response
    {
        $product = $repo->find($id);
        $form = $this->createForm(ProductFormType::class,$product);
        $form->handleRequest($request);
       
    
        if($form->isSubmitted() && $form->isValid())
            {
                $picture = $form['picture']->getData();
                $newPictureName = $product->getName().'.'.$picture->getExtension();
                $picture->move(
                    $this->getParameter('PicturesProducts'),
                    $newPictureName
                );
                $product->setPicture($newPictureName);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
                return $this->redirectToRoute("admin_products");
        
            }
        
        return $this->render("admin_products/new.html.twig", [
            'product' =>$product,
               'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_products_delete")
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, $id , ProductRepository $repo): Response
    {

        $product = $repo->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($product);
        $entityManager->flush();
     
        return $this->redirectToRoute("admin_products");
    }

    /**
     * @Route ("/search" ,name="search")
     */
    function search (ProductRepository $repository, Request $request) {
        $data = $request -> get('search');
        $product = $repository ->findBy( ['name'=> $data]);
        return $this -> render('admin_products/index.html.twig' ,[
                'products' => $product
            ]
        );
     
}
}