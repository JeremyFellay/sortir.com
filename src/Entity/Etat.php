<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatRepository::class)]
class Etat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(targetEntity: Sorties::class, mappedBy: 'etat')]
    private Collection $sortie;

    public function __construct()
    {
        // Initialisation de la collection de sorties
        $this->sortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Sorties>
     */
    public function getSortie(): Collection
    {
        return $this->sortie;
    }

    // Ajouter une sortie à l'état
    public function addSortie(Sorties $sortie): static
    {
        if (!$this->sortie->contains($sortie)) {
            $this->sortie->add($sortie);
            $sortie->setEtat($this);
        }

        return $this;
    }

    // Supprimer une sortie de l'état
    public function removeSortie(Sorties $sortie): static
    {
        if ($this->sortie->removeElement($sortie)) {
            // Définir l'état de la sortie sur null
            if ($sortie->getEtat() === $this) {
                $sortie->setEtat(null);
            }
        }

        return $this;
    }
}