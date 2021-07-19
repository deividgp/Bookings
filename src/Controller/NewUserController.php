<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserTypeUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class NewUserController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/newuser', name: 'new_user', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        if(!$user){
            $user = new User();
            $form = $this->createForm(UserTypeUser::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setRoles(["ROLE_USER"]);
                //$user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
                $image = $form->get('image')->getData();
                $user->setImage(stream_get_contents(fopen($image->getRealPath(),'rb')));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('app_login');
            }

            return $this->render('new_user/index.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
            ]);
        }else{
            return $this->redirectToRoute('home');
        }
    }
}
