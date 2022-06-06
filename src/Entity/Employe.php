<?php

namespace App\Entity;

use App\Repository\EmployeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeRepository::class)]
class Employe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 30)]
    private $nom;

    #[ORM\Column(type: 'string', length: 30, nullable: true)]
    private $prenom;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $quota;

    #[ORM\Column(type: 'float', nullable: true)]
    private $salaire;

    #[ORM\OneToMany(mappedBy: 'employe', targetEntity: Conge::class, orphanRemoval: true)]
    private $conge;


    public function __construct()
    {
        $this->conge = new ArrayCollection();
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

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getQuota(): ?int
    {
        return $this->quota;
    }

    public function setQuota(?int $quota): self
    {
        $this->quota = $quota;

        return $this;
    }

    public function getSalaire(): ?float
    {
        return $this->salaire;
    }

    public function setSalaire(?float $salaire): self
    {
        $this->salaire = $salaire;

        return $this;
    }

    /**
     * @return Collection<int, Conge>
     */
    public function getConge(): Collection
    {
        return $this->conge;
    }

    public function addConge(Conge $conge): self
    {
        if (!$this->conge->contains($conge)) {
            $this->conge[] = $conge;
            $conge->setEmploye($this);
        }

        return $this;
    }

    public function removeConge(Conge $conge): self
    {
        if ($this->conge->removeElement($conge)) {
            // set the owning side to null (unless already changed)
            if ($conge->getEmploye() === $this) {
                $conge->setEmploye(null);
            }
        }

        return $this;
    }


}
