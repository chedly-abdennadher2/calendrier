<?php

namespace App\Repository;

use App\Entity\Conge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conge>
 *
 * @method Conge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conge[]    findAll()
 * @method Conge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CongeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conge::class);
    }

    public function add(Conge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Conge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
public function FindAllByMoisAnnee(int $mois,int $annee)
{
    $entitymanager=$this->getEntityManager();
    $query=$entitymanager->createQuery('select c from App\Entity\Conge c where MONTH(c.datedebut)=:mois and YEAR(c.datedebut) = :annee')
        ->setParameter('mois',$mois)->setParameter('annee',$annee);
    return $query->getResult();

}

    public function compterCongeByMoisAnnee(int $mois,int $annee)
    {
        $entitymanager=$this->getEntityManager();
        $query=$entitymanager->createQuery('select Count(c) from App\Entity\Conge c where MONTH(c.datedebut)=:mois and YEAR(c.datedebut) = :annee')
            ->setParameter('mois',$mois)->setParameter('annee',$annee);
        return $query->getResult();

    }

}
