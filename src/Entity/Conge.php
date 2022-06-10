<?php

namespace App\Entity;

use App\Repository\CongeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\ManagerRegistry;

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




    #[ORM\ManyToOne(targetEntity: Employe::class, inversedBy: 'conge')]
    #[ORM\JoinColumn(nullable: false)]
    private $employe;

    private $nbjour;

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
        $this->employe = $employe;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNbjour()
    {
        return $this->nbjour;
    }

    /**
     * @param mixed $nbjour
     */
    public function setNbjour($nbjour)
    {
        $this->nbjour = $nbjour;
    }

    public function calculernbjour(string $id, ManagerRegistry $doctrine)
    {
        $rep = $doctrine->getRepository(Conge::class);
        $conge = $rep->find($id);
        $nbjour = $conge->getDatefin()->diff($conge->getDatedebut());

        $diff['jour']= $nbjour->d;
        $diff['mois']= $nbjour->m;
        $diff['annee']= $nbjour->y;
        $nbjour= $diff['jour']+$diff['mois'] *30 +$diff['annee']*365;
$this->setNbjour($nbjour);
return $nbjour;
    }}
