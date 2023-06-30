<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RokbaController extends AbstractController
{
    /**
     * @Route("/rokba", name="app_rokba")
     */
    public function index(): Response
    {
        return $this->render('rokba/index.html.twig', [
            'controller_name' => 'RokbaController',
        ]);
    }
}
