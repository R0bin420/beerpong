<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameUserRepository")
 */
class GameUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam(): ?int
    {
        return $this->team;
    }

    public function setTeam(int $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getUser()->getUsername() . " (" . $this->getGame()->getStartDate()->format('d.m.Y') . ")";
    }
}
