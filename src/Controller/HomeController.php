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
            $allTrips = $tripRepository->findAll();
            $trips = array();

            foreach ($allTrips as $trip){
                if ($trip->getFeatured() == true){
                    array_push($trips, $trip);
                }
            }
            return $this->render('home/index.html.twig', [
                'trips' => $trips,
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }
}
