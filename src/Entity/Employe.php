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

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist', 'remove'])]
    private $login;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbjourpris;

    #[ORM\OneToMany(mappedBy: 'employe', targetEntity: Contrat::class, orphanRemoval: true)]
    private $contrat;



    public function __construct()
    {
        $this->conge = new ArrayCollection();
        $this->contrat = new ArrayCollection();
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

    public function getLogin(): ?User
    {
        return $this->login;
    }

    public function setLogin(?User $login): self
    {
        $this->login = $login;

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

    /**
     * @return Collection<int, Contrat>
     */
    public function getContrat(): Collection
    {
        return $this->contrat;
    }

    public function addContrat(Contrat $contrat): self
    {
        if (!$this->contrat->contains($contrat)) {
            $this->contrat[] = $contrat;
            $contrat->setEmploye($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): self
    {
        if ($this->contrat->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getEmploye() === $this) {
                $contrat->setEmploye(null);
            }
        }

        return $this;
    }
public function calculerquota ()
{
    $nbjour2 = $this->getContrat()->get(0)->getDatefin()->diff($this->getContrat()->get(0)->getDatedebut());
    $diff['jour']= $nbjour2->d;
    $diff['mois']= $nbjour2->m;
    $diff['annee']= $nbjour2->y;
    $this->quota=$this->getContrat()->get(0)->calculquotaparmoisaccorde();
    $this->getContrat()->get(0)->setQuotarestant($this->quota-$this->nbjourpris);

}
}
