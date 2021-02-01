<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/my-profile", name="profile")
     */
    public function index(UserRepository $UserRepository): Response
    {
        return $this->render('profile/index.html.twig', [
            'user' => $UserRepository->findAll(),
        ]); 
    }

}
