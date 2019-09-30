<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @Route("/register", name="register")
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function register(
        EntityManagerInterface $manager,
        Request $request,
        UserPasswordEncoderInterface $encoder
    ) {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $error = "";
        $username = $request->get('username');
        $password = $request->get('password');
        $passwordRepeat = $request->get('password_repeat');

        if ($request->getMethod() == 'POST') {
            if (empty($username) || empty($password)) {
                $error = "Nutzername oder Passwort ist nicht gefüllt.";
            } else {
                $usernameCheck = $manager->getRepository(User::class)->findBy(['username' => $username]);
                if (count($usernameCheck) > 0) {
                    $error = "Nutzername schon vergeben.";
                } else {
                    if ($password != $passwordRepeat) {
                        $error = "Passwörter stimmen nicht überein";
                    } else {
                        $newUser = new User();
                        $newUser
                            ->setPassword($encoder->encodePassword($newUser,$password))
                            ->setUsername($username)
                        ;

                        $manager->persist($newUser);
                        $manager->flush();

                        return $this->redirectToRoute('app_login', ['successRegister' => true]);
                    }
                }
            }
        }

        return $this->render('home/register.html.twig', [
            "error" => $error
        ]);
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
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function oneversus(
        EntityManagerInterface $manager
    ) {
        $users = $manager->getRepository(User::class)->findBy([],['username' => 'ASC']);
        if ($request->getMethod() == 'POST') {

        }

        return $this->render('home/1versus1.html.twig',[
            "users" => $users
        ]);
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
