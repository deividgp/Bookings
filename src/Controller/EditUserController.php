<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserTypeUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditUserController extends AbstractController
{
    #[Route('/{id}/edituser', name: 'edit_user', methods: ['GET', 'POST'])]
    public function index(Request $request, User $user): Response
    {
        $form = $this->createForm(UserTypeUser::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $user->setImage(stream_get_contents(fopen($image->getRealPath(),'rb')));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('edit_user/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
