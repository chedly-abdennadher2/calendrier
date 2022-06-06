<?php

namespace App\Controller;

use App\Entity\Conge;
use App\Entity\Employe;
use App\Form\CongeformulaireType;
use App\Form\EmployeformType;
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
    public function ajouter(Request $request,EntityManagerInterface $entityManager,ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $conge =new Conge();
        $form = $this->createForm(congeformulaireType::class,$conge);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $conge= $form->getData();
            $id=$form->get('id')->getData();
            $conge->setState('no check');
             $rep=$doctrine->getRepository(Employe::class);
             $conge->setEmploye($rep->find($id));
            $entityManager->persist($conge);
            $entityManager->flush();



        }
        return $this->renderForm('conge/index.html.twig', [
            'form' => $form,
]);
    }
    #[Route('/consulterconge', name: 'consulterconge')]

    public function consulter(ManagerRegistry $doctrine)
    {
        $rep=$doctrine->getRepository(Conge::class);
        $conges= $rep->findAll();

        return $this->render('conge/consulterconge.html.twig', [
            'conges' => $conges,
        ]);
    }
}
