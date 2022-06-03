<?php

namespace App\Entity;

use App\Repository\CongeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CongeRepository::class)]
class Conge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date')]
    private $datedebut;

    #[ORM\Column(type: 'date')]
    private $datefin;

    #[ORM\Column(type: 'string', length: 10)]
    private $state;

    #[ORM\OneToOne(mappedBy: 'conge', targetEntity: Employe::class, cascade: ['persist', 'remove'])]
    private $employe;

    #[ORM\ManyToOne(targetEntity: Administrateur::class, inversedBy: 'listeconge')]
    private $administrateur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatedebut(): ?\DateTimeInterface
    {
        return $this->datedebut;
    }

    public function setDatedebut(\DateTimeInterface $datedebut): self
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(\DateTimeInterface $datefin): self
    {
        $this->datefin = $datefin;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getEmploye(): ?Employe
    {
        return $this->employe;
    }

    public function setEmploye(?Employe $employe): self
    {
        // unset the owning side of the relation if necessary
        if ($employe === null && $this->employe !== null) {
            $this->employe->setConge(null);
        }

        // set the owning side of the relation if necessary
        if ($employe !== null && $employe->getConge() !== $this) {
            $employe->setConge($this);
        }

        $this->employe = $employe;

        return $this;
    }

    public function getAdministrateur(): ?Administrateur
    {
        return $this->administrateur;
    }

    public function setAdministrateur(?Administrateur $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }
}
