<?php

namespace App\Entity;

use App\Repository\SuiviCongeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuiviCongeRepository::class)]
class SuiviConge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;


    #[ORM\ManyToOne(targetEntity: Contrat::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $contrat;

    #[ORM\Column(type: 'integer')]
    private $annee;

    #[ORM\Column(type: 'integer')]
    private $mois;

    #[ORM\Column(type: 'float')]
    private $quota;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbjourpris;

    #[ORM\Column(type: 'integer',nullable: true)]
    private $nbjourrestant;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getContrat(): ?Contrat
    {
        return $this->contrat;
    }

    public function setContrat(?Contrat $contrat): self
    {
        $this->contrat = $contrat;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getMois(): ?int
    {
        return $this->mois;
    }

    public function setMois(int $mois): self
    {
        $this->mois = $mois;

        return $this;
    }

    public function getQuota(): ?float
    {
        return $this->quota;
    }

    public function setQuota(float $quota): self
    {
        $this->quota = $quota;

        return $this;
    }

    public function getNbjourpris(): ?int
    {
        return $this->nbjourpris;
    }

    public function setNbjourpris(?int $nbjourpris): self
    {
        $this->nbjourpris = $nbjourpris;

        return $this;
    }

    public function getNbjourrestant(): ?int
    {
        return $this->nbjourrestant;
    }

    public function setNbjourrestant(int $nbjourrestant): self
    {
        $this->nbjourrestant = $nbjourrestant;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
