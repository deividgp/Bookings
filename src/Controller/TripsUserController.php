<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Trip;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/trips')]
class TripsUserController extends AbstractController
{
    #[Route('/', name: 'trips_user', methods: ['GET'])]
    public function index(TripRepository $tripRepository): Response
    {
        return $this->render('trips_user/index.html.twig', [
            'trips' => $tripRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'trips_book', methods: ['GET', 'POST'])]
    public function book(Request $request, Trip $trip, TripRepository $tripRepository): Response
    {
        $places=$request->get('places');
        if($trip->getAvailablePlaces()>=$places){
            $booking = new Booking();
            $booking->setTrip($trip);
            $booking->setUser($this->getUser());

            $bookingRepository = $this->getDoctrine()->getRepository(Booking::class);
            $bookings = $bookingRepository->findBy(array('user' => $this->getUser(), 'trip' => $trip));

            if (!$bookings){
                $booking->setPlaces($places);
                $entityManager = $this->getDoctrine()->getManager();
                $trip->setAvailablePlaces($trip->getAvailablePlaces()-$places);
                $entityManager->persist($booking);
                $entityManager->flush();
                return $this->redirectToRoute('trips_user');
            }

            return $this->render('trips_user/index.html.twig', [
                'error' => "You already booked this trip",
                'trips' => $tripRepository->findAll(),
            ]);

        }else{
            return $this->render('trips_user/index.html.twig', [
                'error' => "Not enough available places",
                'trips' => $tripRepository->findAll(),
            ]);
        }
    }
}
