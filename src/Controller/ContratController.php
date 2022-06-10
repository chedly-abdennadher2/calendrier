<?php

namespace App\Controller;

use App\Entity\Conge;
use App\Entity\Contrat;
use App\Entity\Employe;
use App\Form\CongeformulaireUpdateType;
use App\Form\ContratType;
use App\Repository\ContratRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contrat')]
class ContratController extends AbstractController
{
    #[Route('/', name: 'app_contrat_index', methods: ['GET'])]
    public function index(ContratRepository $contratRepository): Response
    {
        return $this->render('contrat/index.html.twig', [
            'contrats' => $contratRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_contrat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContratRepository $contratRepository,EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $contrat = new Contrat();
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $id=$form->get('employe')->getData();
            $rep = $doctrine->getRepository(Employe::class);
            $emp=$rep->find($id);
            $contrat->setEmploye($emp);
            $contrat->calculquotaparmoisaccorde();
            $emp->addContrat($contrat);
            $contratRepository->add($contrat, true);
            $emp->calculerquota();
            $entityManager->persist($emp);
            $entityManager->flush();

            return $this->redirectToRoute('app_contrat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contrat/new.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
        ]);
    }
    #[Route('/afficher', name:'app_contrat_afficher', methods: ['GET'])]
    public function afficher(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user =$this->getUser();

         $rep = $doctrine->getRepository(Employe::class);
        $emp=$rep->findOneBy(['login'=>$user]);
         $rep = $doctrine->getRepository(Contrat::class);
       $contrats = $rep->findBy(['employe' => $emp]);

         return $this->render('contrat/consultercontratemp.html.twig', [
             'contrats' => $contrats,
         ]);

        }

    #[Route('/{id}', name: 'app_contrat_show', methods: ['GET'])]
    public function show(Contrat $contrat): Response
    {
        return $this->render('contrat/show.html.twig', [
            'contrat' => $contrat,
        ]);
    }

#[Route('/{id}/edit', name: 'app_contrat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contrat $contrat, ContratRepository $contratRepository,ManagerRegistry $doctrine,EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContratType::class, $contrat);
        $form->get('employe')->setData($contrat->getEmploye()->getId());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $id=$form->get('employe')->getData();
            $rep = $doctrine->getRepository(Employe::class);
            $emp=$rep->find($id);
            $contrat->setEmploye($emp);
            $contrat->calculquotaparmoisaccorde();
        $emp->calculerquota();
        $entityManager->persist($emp);
        $entityManager->flush();
        $contratRepository->add($contrat, true);

        return $this->redirectToRoute('app_contrat_index', [], Response::HTTP_SEE_OTHER);
    }

        return $this->renderForm('contrat/edit.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_contrat_delete', methods: ['POST'])]
    public function delete(Request $request, Contrat $contrat, ContratRepository $contratRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contrat->getId(), $request->request->get('_token'))) {
            $contratRepository->remove($contrat, true);
        }

        return $this->redirectToRoute('app_contrat_index', [], Response::HTTP_SEE_OTHER);
    }

}
