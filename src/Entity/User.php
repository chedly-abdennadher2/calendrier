<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['nomutilisateur'], message: 'There is already an account with this nomutilisateur')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $nomutilisateur;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;
    #[ORM\Column(type: 'string', length: 30)]
    private $nom;

    #[ORM\Column(type: 'string', length: 30, nullable: true)]
    private $prenom;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $quota;

    #[ORM\Column(type: 'float', nullable: true)]
    private $salaire;



    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbjourpris;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Conge::class, orphanRemoval: true)]
    private $conge;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Contrat::class, orphanRemoval: true)]
    private $contrat;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomutilisateur(): ?string
    {
        return $this->nomutilisateur;
    }

    public function setNomutilisateur(string $nomutilisateur): self
    {
        $this->nomutilisateur = $nomutilisateur;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->nomutilisateur;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function __construct()
    {
        $this->conge = new ArrayCollection();
        $this->contrat = new ArrayCollection();
        $this->suiviconge = new ArrayCollection();
        $this->nbvisit=0;
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
    public function getcontratplusrecent()
    {
        $tab=$this->getContrat();
        $max=0;
        foreach ($tab as $clef=>$value)
        { if ($value->getDatedebut()>$tab->get($max)->getDatedebut())
        {$max=$clef;}
        }
        return $tab->get($max);
    }
    public function calculerquota ()
    {
        $date =date ('d-m-Y');
        $jouractuel = substr ($date,0,2);
        $moisactuel = substr ($date,3,2);
        $anneeactuel=substr ($date,6,4);
        $datedebutrecent=$this->getcontratplusrecent()->getDatedebut();
        $yeardebut= $datedebutrecent->format('Y');
        $moisdebut= $datedebutrecent->format('m');
        $jourdebut= $datedebutrecent->format('d');
        $nbmois=($anneeactuel-$yeardebut)*12+($moisactuel-$moisdebut);
        $nbmois=  number_format( (float) $nbmois, 2, '.', '');

        $this->quota=$this->getcontratplusrecent()->getQuotaparmoisaccorde()*$nbmois;
        echo $nbmois;

        $this->getcontratplusrecent()->setQuotarestant($this->quota-$this->nbjourpris);
    }
    public function nbjourprisreset()
    {
        $date =date ('d-m-Y');
        $jour = substr ($date,0,2);
        if (intval($jour)==1)
        {$this->nbjourpris=0;
        }

    }
}
