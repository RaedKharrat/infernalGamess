<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class WishListController extends AbstractController
    /**
    * @Route("/wishlist")
    */
{
    /**
     * @Route("/", name="wish_list")
     */
    public function index(): Response
    {
        return $this->render('shop/wishList.html.twig', [
            'controller_name' => 'WishListController',
        ]);
    }

    /**
     * @Route("/add/{id}", name="wishlist_add")
     */
    public function add(int $id,SessionInterface $session){

        
        $list =$session->get('list',[]);
        if(!empty($list[$id])){
            $list[$id]++;
        } else {
           $list[$id] = 1 ; 
           
        }
       
        $list =$session->set('list',$list);
        return $this->redirectToRoute("shop");

       
   }

       /**
     * @Route("/viewlist", name="view_list")
     */
    public function viewProduct(SessionInterface $session , ProductRepository $repo){

        $list = $session->get('list',[]);
        $listWithData = [];
        foreach ($list as $id => $quantity){
            $listWithData[] = [
                'product' => $repo->find($id),
                    
            ];

         }
         
         
         $total = 0;
         foreach($listWithData as $item) {
             $item = $item['product']->getPrice() ;
             
         }
        



         return $this->render('shop/wishList.html.twig',
         ['items'=>$listWithData
        
     ]);
    }

      /**
     * @Route("/remove/{id}",name="list_remove")
     */
    public function remove($id,SessionInterface $session ){
        $list = $session->get('list',[]);
        if(!empty($list[$id])) {
            unset($list[$id]);
        } 

    $session->set('panier' , $panier);
    
    return $this->redirectToRoute("view_product");
    }


     
}
