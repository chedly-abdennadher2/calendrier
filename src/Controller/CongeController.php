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
    #[Route('/ajouterconge', name: 'app_conge')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $conge = new Conge();
        $form = $this->createForm(congeformulaireType::class, $conge);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $conge = $form->getData();
            $id = $form->get('id')->getData();
            $conge->setState('valide');
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

    #[Route('/supprimerconge', name: 'supprimerconge')]
    public function supprimer(Request $request, ManagerRegistry $doctrine, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(SuppressionType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $id = $form->get('id')->getData();

            $rep = $doctrine->getRepository(Conge::class);
            $conge = $rep->find($id);

            $entityManager->remove($conge);
            $entityManager->flush();
        }

        return $this->renderForm('conge/supprimerconge.html.twig', [
            'form' => $form,
        ]);

    }

    #[Route('/nbjourconge/{id}', name: 'nbjourconge')]
    public function nbjour(string $id, ManagerRegistry $doctrine)
    {
        $rep = $doctrine->getRepository(Conge::class);
        $conge = $rep->find($id);
        $nbjour = $conge->getDateFin()->diff($conge->getDateDebut());

     $diff['jour']= $nbjour->d;
     $diff['mois']= $nbjour->m;
        $diff['annee']= $nbjour->y;
$nbjour= $diff['jour']+$diff['mois'] *30 +$diff['annee']*365;
        return $this->render('conge/voir.html.twig', [
        ]);

    }
}