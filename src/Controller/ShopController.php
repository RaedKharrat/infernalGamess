<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShopController extends AbstractController
    /**
     * @Route("/shop")
     */
{
    /**
     * @Route("/", name="shop")
     */
    public function index(): Response
    {
        $products= $this->getDoctrine()->getRepository(Product::class)->findAll();
        //$lastProducts = $this->getDoctrine()->getRepository(Product::class)->findBy(array(), null, 3, 3);
        
        return $this->render('shop/index.html.twig', [
            'controller_name' => 'ShopController',
            'products'=>$products
         //   'lastProducts'=>$lastProducts

        ]);
       
    }

    /**
     * @Route("/cart", name="view_product")
     */
    public function viewProduct(SessionInterface $session , ProductRepository $repo){

        $panier = $session->get('panier',[]);
        $panierWithData = [];
        foreach ($panier as $id => $quantity){
            $panierWithData[] = [
                'product' => $repo->find($id),
                'quantity' => $quantity
            ];

         }
          $total = 0;
          foreach($panierWithData as $item) {
              $totalItem = $item['product']->getPrice() * $item['quantity'];
              $total += $totalItem ;
          }
          $session->set('total',$total);


        return $this->render('shop/cart.html.twig',
            ['items'=>$panierWithData,
            'total' =>$total
        ]);
    }

    /**
     * @Route("/add/{id}", name="shop_add")
     */
    public function add(int $id,SessionInterface $session){

        
         $panier =$session->get('panier',[]);
         if(!empty($panier[$id])){
             $panier[$id]++;
         } else {
            $panier[$id] = 1 ; 
            
         }
        
         $panier =$session->set('panier',$panier);
         return $this->redirectToRoute("view_product");

        
    }
    /**
     * @Route("/panier/remove/{id}",name="shop_remove")
     */
    public function remove($id,SessionInterface $session ){
        $panier = $session->get('panier',[]);
        if(!empty($panier[$id])) {
            unset($panier[$id]);
        } 

    $session->set('panier' , $panier);
    
    return $this->redirectToRoute("view_product");
    }


       /**
     * @Route ("/search" ,name="search")
     */
    function search (ProductRepository $repository, Request $request) {
        $data = $request -> get('search');
        $product = $repository ->findBy( ['name'=> $data]);
        return  $this -> render('shop/index.html.twig' ,[
                'products' => $product
            ]
        );
        
     
}
/**
 * @Route ("/view/{id}" ,name="viewProduct")
 */
function viewOneProduct($id){
   
    $product=$this->getDoctrine()->getRepository(Product::class)->find($id);

    return $this->render('shop/product.html.twig',
        ['product'=>$product]);
}

/**
     * @Route("/searchByCategory", name="searchByCategory")
     */
    public function searchByCategory(Request $request, ProductRepository $rep,  NormalizerInterface $normalizer): Response
    {
        $products= $rep->findByStateCategory( $request->get('id') );
        $jsonData= $normalizer->normalize($products, 'json', ['groups'=>'products:read']);
        return new Response(json_encode($jsonData));
    }


    

 

}
