<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
{
    CONST WINTYPE_DEFAULT = 1;
    CONST WINTYPE_SHAVED = 2;
    CONST WINTYPE_BLITZKO = 3;


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $winnerTeam;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GameUser", mappedBy="game", orphanRemoval=true)
     */
    private $users;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $winType;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getWinnerTeam(): ?int
    {
        return $this->winnerTeam;
    }

    public function setWinnerTeam(?int $winnerTeam): self
    {
        $this->winnerTeam = $winnerTeam;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return Collection|GameUser[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(GameUser $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setGame($this);
        }

        return $this;
    }

    public function removeUser(GameUser $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getGame() === $this) {
                $user->setGame(null);
            }
        }

        return $this;
    }

    public function getWinType(): ?int
    {
        return $this->winType;
    }

    public function setWinType(?int $winType): self
    {
        $this->winType = $winType;

        return $this;
    }

    public function __toString()
    {
        $usernames = [];
        foreach($this->getUsers() as $user)
            $usernames[] = $user->getUser()->getUsername();

        return $this->getStartDate()->format('d.m.Y') . " (" . implode(", ", $usernames) . ")";
    }
}
