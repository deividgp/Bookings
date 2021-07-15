<?php

namespace App\Controller;

use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(TripRepository $tripRepository): Response
    {
        $user = $this->getUser();

        if($user){
            $this->get('session')->set('admin', false);
            $trips = $tripRepository->findAll();
            $random = rand(0, sizeof($trips)-1);

            return $this->render('home/index.html.twig', [
                'trip' => $trips[$random],
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }
}
