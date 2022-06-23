<?php

namespace App\Entity;

use App\Repository\CongeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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

    #[ORM\Column(type: 'string', length: 20)]
    private $typeconge;

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
// etant donnee que la méthode pour la commande nbjour necissite un entity manager
// ailleurs j'ai cette méthode appele avec managerregistry j'ai du créer une autre fonction
// avec même code mais dn paramètre d'entreé différent
    public function calculernbjourpourcommande()
    {
        if ($this->state == 'valide')
        {            $nbjour = $this->getDatefin()->diff($this->getDatedebut());

        $diff['jour'] = $nbjour->d;
        $diff['mois'] = $nbjour->m;
        $diff['annee'] = $nbjour->y;
        $nbjour = $diff['jour'] + $diff['mois'] * 30 + $diff['annee'] * 365;
        $this->setNbjour($nbjour);
        return $nbjour;
    }
    else
{
$this->setNbjour(0);
return 0;

}
    }
    public function calculernbjour()
    {
        $nbjour = $this->getDatefin()->diff($this->getDatedebut());

        $diff['jour']= $nbjour->d;
        $diff['mois']= $nbjour->m;
        $diff['annee']= $nbjour->y;
        $nbjour= $diff['jour']+$diff['mois'] *30 +$diff['annee']*365;
        $this->setNbjour($nbjour);
        return $nbjour;
    }

    public function getTypeconge(): ?string
    {
        return $this->typeconge;
    }

    public function setTypeconge(string $typeconge): self
    {
        $this->typeconge = $typeconge;

        return $this;
    }
}
