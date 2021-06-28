<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $user = $this->getUser();

        if($user){
            $this->get('session')->set('admin', false);
            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }
}