<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Trip;
use App\Repository\BookingRepository;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bookings')]
class BookingsUserController extends AbstractController
{
    #[Route('/', name: 'bookings_user', methods: ['GET'])]
    public function index(BookingRepository $bookingRepository): Response
    {
        return $this->render('bookings_user/index.html.twig', [
            'bookings' => $bookingRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    #[Route('/{id}', name: 'bookings_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Booking $booking, BookingRepository $bookingRepository): Response
    {
        $places = $request->get('places');
        $entityManager = $this->getDoctrine()->getManager();

        if ($booking->getPlaces() != $places) {
            $changePlaces = $places - $booking->getPlaces();
            $trip = $booking->getTrip();

            if ($changePlaces > $trip->getAvailablePlaces()) {
                return $this->render('bookings_user/index.html.twig', [
                    'bookings' => $bookingRepository->findBy(['user' => $this->getUser()]),
                    'error' => 'Not enough available places'
                ]);
            }
            $booking->setPlaces($places);
            $trip->setAvailablePlaces($trip->getAvailablePlaces() - $changePlaces);
        }

        $entityManager->flush();

        return $this->redirectToRoute('bookings_user');
    }

    #[Route('/{id}/delete', name: 'bookings_delete', methods: ['POST'])]
    public function delete(Request $request, Booking $booking): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $trip = $entityManager->getRepository(Trip::class)->find($booking->getTrip());
        $trip->setAvailablePlaces($trip->getAvailablePlaces()+$booking->getPlaces());
        $entityManager->remove($booking);
        $entityManager->flush();

        return $this->redirectToRoute('bookings_user');
    }
}
