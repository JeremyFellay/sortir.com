<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampusRepository::class)]
class Campus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'campus')]
    private Collection $users;

    #[ORM\OneToMany(targetEntity: Sorties::class, mappedBy: 'campus')]
    private Collection $sortie;

    public function __construct()
    {
        // Initialisation des collections d'utilisateurs et de sorties
        $this->users = new ArrayCollection();
        $this->sortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    // Ajouter un utilisateur au campus
    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setCampus($this);
        }

        return $this;
    }

    // Supprimer un utilisateur du campus
    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // Définir le campus de l'utilisateur sur null
            if ($user->getCampus() === $this) {
                $user->setCampus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sorties>
     */
    public function getSortie(): Collection
    {
        return $this->sortie;
    }

    // Ajouter une sortie au campus
    public function addSortie(Sorties $sortie): static
    {
        if (!$this->sortie->contains($sortie)) {
            $this->sortie->add($sortie);
            $sortie->setCampus($this);
        }

        return $this;
    }

    // Supprimer une sortie du campus
    public function removeSortie(Sorties $sortie): static
    {
        if ($this->sortie->removeElement($sortie)) {
            // Définir le campus de la sortie sur null
            if ($sortie->getCampus() === $this) {
                $sortie->setCampus(null);
            }
        }

        return $this;
    }
}
