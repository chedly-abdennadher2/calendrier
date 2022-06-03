<?php

namespace App\Entity;

use App\Repository\AdministrateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdministrateurRepository::class)]
class Administrateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 30)]
    private $nom;

    #[ORM\Column(type: 'string', length: 30)]
    private $prenom;

    #[ORM\Column(type: 'float')]
    private $salaire;

    #[ORM\Column(type: 'json')]
    private $role = [];

    #[ORM\OneToMany(mappedBy: 'administrateur', targetEntity: conge::class)]
    private $listeconge;

    public function __construct()
    {
        $this->listeconge = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getSalaire(): ?float
    {
        return $this->salaire;
    }

    public function setSalaire(float $salaire): self
    {
        $this->salaire = $salaire;

        return $this;
    }

    public function getRole(): ?array
    {
        return $this->role;
    }

    public function setRole(array $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, conge>
     */
    public function getListeconge(): Collection
    {
        return $this->listeconge;
    }

    public function addListeconge(conge $listeconge): self
    {
        if (!$this->listeconge->contains($listeconge)) {
            $this->listeconge[] = $listeconge;
            $listeconge->setAdministrateur($this);
        }

        return $this;
    }

    public function removeListeconge(conge $listeconge): self
    {
        if ($this->listeconge->removeElement($listeconge)) {
            // set the owning side to null (unless already changed)
            if ($listeconge->getAdministrateur() === $this) {
                $listeconge->setAdministrateur(null);
            }
        }

        return $this;
    }
}
