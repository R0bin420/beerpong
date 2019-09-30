<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\GameUser;
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

        // Schritt 1. Variablen deklarieren
        $error = "";

        // Schritt2. Aus Anfrage Daten holen
        $username = $request->get('username');
        $password = $request->get('password');
        $passwordRepeat = $request->get('password_repeat');

        // Schritt 3. Prüfen der gegebenen Variablen
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

                        // Schritt 4. Eintragen in die Datenbank
                        $newUser = new User();
                        $newUser
                            ->setPassword($encoder->encodePassword($newUser,$password))
                            ->setUsername($username)
                        ;

                        $manager->persist($newUser);
                        $manager->flush();

                        // Scritt 5. Weiterleiten auf richtige Seite
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
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    public function twoversus(
        EntityManagerInterface $manager,
        Request $request
    ) {
        $users = $manager->getRepository(User::class)->findBy([], ['username' => 'ASC']);

        var_dump($request->get('team1'));
        //die();
        return $this->render('home/2versus2.html.twig', [
            "users" => $users
        ]);
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
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function oneversus(
        EntityManagerInterface $manager,
        Request $request
    )
    {
        /** @var User[] $users */
        $users = $manager->getRepository(User::class)->findBy([], ['username' => 'ASC']);

        // Schritt 1. Variablen deklarieren
        $error = "";
        // Schritt 2. Aus Anfrage Daten holen
        $team1_user = $manager->getRepository(User::class)->find((int)$request->get("team1_player1"));
        $team2_user = $manager->getRepository(User::class)->find((int)$request->get("team2_player1"));

        if ($request->getMethod() == 'POST') {
            if ($team1_user instanceof User && $team2_user instanceof User)  {

                if($team1_user != $team2_user)  {

                    $game = new Game();
                    $game->setStartDate(new \DateTime());
                    $game->setWinnerTeam(1);

                    $manager->persist($game);

                    $gameUser1 = new GameUser();
                    $gameUser1->setUser($team1_user);
                    $gameUser1->setGame($game);
                    $gameUser1->setTeam(1);

                    $manager->persist($gameUser1);


                    $gameUser2 = new GameUser();
                    $gameUser2->setUser($team2_user);
                    $gameUser2->setGame($game);
                    $gameUser2->setTeam(2);

                    $manager->persist($gameUser2);

                    $manager->flush();

                }
                else {
                    echo "Man kann nicht gegensich selbst spielen";
                }

            }
            else {
                echo "Personen wurden nicht gefunden";
            }
        }
        

        return $this->render('home/1versus1.html.twig', [
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
