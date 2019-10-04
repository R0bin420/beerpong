<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\GameUser;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
    )
    {
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
                            ->setPassword($encoder->encodePassword($newUser, $password))
                            ->setUsername($username);

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
     * @Route("/1versus1", name="1versus1")
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function oneversus(
        EntityManagerInterface $manager,
        Request $request
    )

    {
        /** @var User[] $users */
        $users = $manager->getRepository(User::class)->findBy([], ['username' => 'ASC']);

        // Schritt 1. Variablen deklarieren

        // Schritt 2. Aus Anfrage Daten holen
        $team1_user1 = $manager->getRepository(User::class)->find((int)$request->get("team1_player1"));
        $team1_user2 = $manager->getRepository(User::class)->find((int)$request->get("team1_player2"));
        $team2_user1 = $manager->getRepository(User::class)->find((int)$request->get("team2_player1"));
        $team2_user2 = $manager->getRepository(User::class)->find((int)$request->get("team2_player2"));


        if ($request->getMethod() == 'POST') {
            if ($team1_user1 instanceof User && $team2_user1 instanceof User) {

                if ($team1_user1 != $team2_user1) {
                    $game = new Game();
                    $game->setStartDate(new DateTime());
                    $manager->persist($game);

                    $gameUser1 = new GameUser();
                    $gameUser1->setUser($team1_user1);
                    $gameUser1->setGame($game);
                    $gameUser1->setTeam(1);

                    $manager->persist($gameUser1);

                    $gameUser2 = new GameUser();
                    $gameUser2->setUser($team2_user1);
                    $gameUser2->setGame($game);
                    $gameUser2->setTeam(2);


                    $manager->persist($gameUser2);


                    if ($team1_user2 instanceof User) {
                        $gameUser3 = new GameUser();
                        $gameUser3->setUser($team1_user2);
                        $gameUser3->setGame($game);
                        $gameUser3->setTeam(1);

                        $manager->persist($gameUser3);
                    }

                    if ($team2_user2 instanceof User) {
                        $gameUser4 = new GameUser();
                        $gameUser4->setUser($team2_user2);
                        $gameUser4->setGame($game);
                        $gameUser4->setTeam(2);

                        $manager->persist($gameUser4);
                    }

                    $manager->flush();

                    return $this->redirectToRoute("finish",['id' => $game->getId()]);
                } else {
                    echo "<div class='alert alert-danger' role='alert'>Man kann nicht gegen sich selbst spielen</div>";
                }


            } else {
                echo "<div class='alert alert-danger' role='alert'>Person nicht gefunden</div>";
            }
        }
        return $this->render('home/1versus1.html.twig', [
            "users" => $users
        ]);
    }


    /**
     * @Route("/finish", name="finish")
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    public function finish(
        EntityManagerInterface $manager,
        Request $request
){
        // 1. Prüfen ob Game mit Game ID exisitert, wenn nicht, wieder umleiten.
        $gameId = $request->get('id');
        $game = $manager->find(Game::class,$gameId);
        if (is_null($game)) {
            return $this->redirectToRoute('1versus1');
        }

        // 2. Forms machen zum Senden des Ergebnisses; OK
        // 3. Request auf Post prüfen und Daten speichern (Gewinner usw.)
        if ($request->getMethod()== 'POST'){
            if ($request->get('team1_win') == '1') {
                $game->setWinnerTeam(1);
                $game->setWinType(Game::WINTYPE_DEFAULT);
            } elseif ($request->get('team1_shave') == '1') {
                $game->setWinnerTeam(1);
                $game->setWinType(GAME::WINTYPE_SHAVED);
            } elseif ($request->get('team1_blitzko')){
                $game->setWinnerTeam(1);
                $game->setWinType(GAME::WINTYPE_BLITZKO);
            }elseif ($request->get('team2_win')){
                $game->setWinnerTeam(2);
                $game->setWinType(GAME::WINTYPE_DEFAULT);
            } elseif ($request->get('team2_shave')){
                $game->setWinnerTeam(2);
                $game->setWinType(GAME::WINTYPE_SHAVED);
            } elseif ($request->get('team2_blitzko')){
                $game->setWinnerTeam(2);
                $game->setWinType(GAME::WINTYPE_BLITZKO);
            }


            $game->setEndDate(new DateTime());

            $manager->persist($game);
            $manager->flush();

            // 4. Danach umleiten auf Home?
            return $this->redirectToRoute("home");
        }

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
     * @Route("/profil", name="profil")
     * @return Response
     */
    public function profil() {
        return $this->render('home/profil.html.twig');
    }


}
