<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\AddToCartType;
use App\Entity\Product;

class ProductsController extends AbstractController
{
    /**
     * @Route("/products", name="products")
     */
    public function index(): Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        return $this->render('shop/index.html.twig', [
            'controller_name' => 'ProductsController',
            'products' => $products
        ]);
    }
}
