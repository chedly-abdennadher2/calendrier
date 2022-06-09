<?php

namespace App\Entity;

use App\Repository\ContratRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContratRepository::class)]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date')]
    private $datedebut;

    #[ORM\Column(type: 'date')]
    private $datefin;

    #[ORM\Column(type: 'date', nullable: true)]
    private $datearret;

    #[ORM\Column(type: 'string', length: 10)]
    private $typedecontrat;

    #[ORM\Column(type: 'float')]
    private $quotaparmoisaccorde;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $quotarestant;

    #[ORM\ManyToOne(targetEntity: Employe::class, inversedBy: 'contrat')]
    #[ORM\JoinColumn(nullable: false)]
    private $employe;


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

    public function getDatearret(): ?\DateTimeInterface
    {
        return $this->datearret;
    }

    public function setDatearret(?\DateTimeInterface $datearret): self
    {
        $this->datearret = $datearret;

        return $this;
    }

    public function getTypedecontrat(): ?string
    {
        return $this->typedecontrat;
    }

    public function setTypedecontrat(string $typedecontrat): self
    {
        $this->typedecontrat = $typedecontrat;

        return $this;
    }

    public function getQuotaparmoisaccorde(): ?float
    {
        return $this->quotaparmoisaccorde;
    }

    public function setQuotaparmoisaccorde(float $quotaparmoisaccorde): self
    {
        $this->quotaparmoisaccorde = $quotaparmoisaccorde;

        return $this;
    }

    public function getQuotarestant(): ?int
    {
        return $this->quotarestant;
    }

    public function setQuotarestant(?int $quotarestant): self
    {
        $this->quotarestant = $quotarestant;

        return $this;
    }

    public function getEmploye(): ?Employe
    {
        return $this->employe;
    }

    public function setEmploye(?Employe $employe): self
    {
        $this->employe = $employe;

        return $this;
    }
public function calculquotaparmoisaccorde ()
{
    if ($this->typedecontrat=='CDI')
    {
        $this->quotaparmoisaccorde=2.5;
    }
    if ($this->typedecontrat=='CDD')
    {   $this->quotaparmoisaccorde=1.5;
    }
    echo ($this->quotaparmoisaccorde);

}

}
