<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class AdminHomeController extends AbstractController
{
    #[Route('/admin/', name: 'admin_home')]
    public function index(ChartBuilderInterface $chartBuilder, BookingRepository $bookingRepository): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $data = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $labels = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23];

        $qb = $bookingRepository->createQueryBuilder('p')
            ->select('b.dateCreated')
            ->from(Booking::class, 'b');

        $dateTimeQuery = $qb->getQuery()->execute();

        foreach ($dateTimeQuery as $dateTime){
            $date = $dateTime['dateCreated'];

            if($date->format("Y-m-d") == date("Y-m-d")){
                $data[intval($date->format("H"))] = $data[intval($date->format("H"))] + 1;
            }
        }

        $data = array_slice($data, 0, intval(date("H"))+1);
        $labels = array_slice($labels, 0, intval(date("H"))+1);

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Bookings '.date("Y-m-d"),
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $data,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'xAxes' => [
                    'type' => 'time',
                    'time' => [
                        'unit' => 'hour',
                        'tooltipFormat' => 'HH',
                    ],
                ],
                'yAxes' => [
                    ['ticks' => ['min' => 0, 'max' => 10]],
                ],
            ],
        ]);

        $this->get('session')->set('admin', true);

        return $this->render('admin_home/index.html.twig', [
            'chart' => $chart,
        ]);
    }
}
