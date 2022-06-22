<?php

namespace App\Repository;

use App\Entity\Contrat;
use App\Entity\Employe;
use App\Entity\SuiviConge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @extends ServiceEntityRepository<SuiviConge>
 *
 * @method SuiviConge|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuiviConge|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuiviConge[]    findAll()
 * @method SuiviConge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuiviCongeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuiviConge::class);
    }

    public function add(SuiviConge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SuiviConge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SuiviConge[] Returns an array of SuiviConge objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

 /*   public function calculersommenbjourrestant (Employe $emp, int $mois, int $annee):array
    {
        $entitymanager=$this->getEntityManager();
     if ($mois>1) {
         $query = $entitymanager->createQuery('select SUM(suivi.nbjourrestant)  from App\Entity\SuiviConge suivi where (suivi.employe=:employe and suivi.annee=:annee and suivi.mois<:mois)')->setParameter('employe', $emp)->setParameter('annee', $annee)->setParameter('mois',$mois);
     }
     else
     {
         $query = $entitymanager->createQuery('select SUM(suivi.nbjourrestant)  from App\Entity\SuiviConge suivi where (suivi.employe=:employe and suivi.annee=:annee)')->setParameter('employe', $emp)->setParameter('annee', $annee-1);

     }
        return $query->getResult();

    }
*/
    public function FindByMoisAnneerecent(Employe $emp,Contrat $contrat)
    {
        $entitymanager=$this->getEntityManager();
        $query=$entitymanager->createQuery('select s from App\Entity\SuiviConge s 
        where s.employe=:employe and s.contrat =:contrat and s.annee =(select max(s2.annee) from App\Entity\SuiviConge s2 
        where s2.employe=:employe and s2.contrat =:contrat ) and s.mois= (select max(s3.mois) from App\Entity\SuiviConge s3 
        where s3.employe=:employe and s3.contrat =:contrat  ) ')
            ->setParameter('employe',$emp)->setParameter('contrat',$contrat);
        return $query->getResult();

    }

}
