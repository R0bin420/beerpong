<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/login", name="login")
     * @return Response
     */
    public function login()
    {
        return $this->render('home/login.html.twig');
    }

    /**
     * @Route("/register", name="register")
     * @return Response
     */
    public function register() {
        return $this->render('home/register.html.twig');
    }
    /**
     * @Route("/2versus2", name="2versus2")
     * @return Response
     */
    public function versus() {
        return $this->render('home/2versus2.html.twig');
    }
}
