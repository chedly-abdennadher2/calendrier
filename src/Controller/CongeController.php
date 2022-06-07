<?php

namespace App\Controller;

use App\Entity\Conge;
use App\Entity\Employe;
use App\Form\CongeformulaireType;
use App\Form\CongeformulaireUpdateType;
use App\Form\EmployeformType;
use App\Form\SuppressionType;
use App\Repository\EmployeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CongeController extends AbstractController
{
    #[Route('/ajouterconge', name: 'ajouterconge')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $conge = new Conge();
        $form = $this->createForm(congeformulaireType::class, $conge);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $conge = $form->getData();
            $id = $form->get('id')->getData();
            $conge->setState('no check');
            $rep = $doctrine->getRepository(Employe::class);
            $conge->setEmploye($rep->find($id));
            $entityManager->persist($conge);
            $entityManager->flush();


        }
        return $this->renderForm('conge/ajouterconge.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/consulterconge', name: 'consulterconge')]

    public function consulter(ManagerRegistry $doctrine)
    {
        $rep = $doctrine->getRepository(Conge::class);
        $conges = $rep->findAll();
    foreach ($conges as $key=>$value)
    {
        $value->calculernbjour($value->getId(), $doctrine);
    }
        return $this->render('conge/consulterconge.html.twig', [
            'conges' => $conges,
        ]);
    }

    #[Route('/mettreajourconge/{id}', name: 'mettreajourconge')]

    public function mettreajour(string $id, Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $rep = $doctrine->getRepository(Conge::class);
        $conge = $rep->find($id);
        $form = $this->createForm(CongeformulaireUpdateType::class, $conge);
        $form->get('id')->setData($conge->getEmploye()->getId());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $conge = $form->getData();
            $entityManager->persist($conge);
            $entityManager->flush();

        }
        return $this->renderForm('conge/modifierconge.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/supprimerconge/{id}', name: 'supprimerconge')]
    public function supprimer(String $id,Request $request, ManagerRegistry $doctrine, EntityManagerInterface $entityManager )
    {
        $form = $this->createForm(SuppressionType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $rep = $doctrine->getRepository(Conge::class);
            $conge = $rep->find($id);

            $entityManager->remove($conge);
            $entityManager->flush();
        }
else {
    $id = $form->get('id')->setData($id);

}
        return $this->renderForm('conge/supprimerconge.html.twig', [
            'form' => $form,
        ]);

    }

public function validerconge (string $id,ManagerRegistry $doctrine, EntityManagerInterface $entityManager,EmployeRepository $repository)
{
    $rep = $doctrine->getRepository(Conge::class);
    $conge = $rep->find($id);
    $emp=$conge->getEmploye();
    $conge->calculernbjour($conge->getId(),$doctrine);
    $nbjour=$conge->getNbjour();
    if (($nbjour <= $emp->getQuota()) and ($conge->getState()=='no check')) {

        $emp->setQuota($emp->getQuota()-$nbjour);
        var_dump("hello valide");
        $conge->setState('valide');

    }
else if ($conge->getState()=='no check') {
    $conge->setState('invalide');
    var_dump("hello invalide");


}
    $conge->setEmploye($emp);
    $entityManager->persist($conge);
    $entityManager->flush();
    $repository->add($emp);
}


}