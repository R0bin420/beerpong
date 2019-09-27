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
    public function twoversus() {
        return $this->render('home/2versus2.html.twig');
    }

    /**
     * @Route("/1versus2", name="1versus2")
     * @return Response
     */
    public function onwoversus() {
        return $this->render('home/1versus2.html.twig');
    }

    /**
     * @Route("/1versus1", name="1versus1")
     * @return Response
     */
    public function oneversus() {
        return $this->render('home/1versus1.html.twig');
    }

    /**
     * @Route("/finish", name="finish")
     * @return Response
     */
    public function finish() {
        return $this->render('home/finish.html.twig');
    }
    /**
     * @Route("/blog", name="blog")
     * @return Response
     */
    public function blog() {
        return $this->render('home/blog.html.twig');
    }
    /**
     * @Route("/admin", name="admin")
     * @return Response
     */
    public function admin() {
        return $this->render('home/admin.html.twig');
    }
    /**
     * @Route("/profil", name="profil")
     * @return Response
     */
    public function profil() {
        return $this->render('home/profil.html.twig');
    }


}
