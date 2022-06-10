<?php

namespace App\Controller;

use App\Entity\Contrat;
use App\Entity\Employe;
use App\Entity\SuiviConge;
use App\Form\SuiviCongeType;
use App\Repository\SuiviCongeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/suivi/conge')]
class SuiviCongeController extends AbstractController
{
    #[Route('/', name: 'app_suivi_conge_index', methods: ['GET'])]
    public function index(SuiviCongeRepository $suiviCongeRepository): Response
    {
        return $this->render('suivi_conge/index.html.twig', [
            'suivi_conges' => $suiviCongeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_suivi_conge_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SuiviCongeRepository $suiviCongeRepository,ManagerRegistry $doctrine): Response
    {
        $suiviConge = new SuiviConge();

        $form = $this->createForm(SuiviCongeType::class, $suiviConge);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $idemp=$form->get('employeid')->getData();
          $idcontrat= $form->get('contratid')->getData();
          $rep=$doctrine->getRepository(Employe::class);
          $emp=$rep->find($idemp);
          $suiviConge->setEmploye($emp);
          $rep=$doctrine->getRepository(Contrat::class);
          $contrat=$rep->find($idcontrat);
          $suiviConge->setContrat($contrat);
            $yeardebut= $contrat->getDatedebut()->format('Y');
            $moisdebut= $contrat->getDatedebut()->format('m');
            $suiviConge->setAnnee($yeardebut);
            $suiviConge->setMois($moisdebut);
            $suiviConge->setQuota($contrat->getQuotaparmoisaccorde());
            $suiviConge->setNbjourpris(0);
            $suiviConge->setNbjourrestant($suiviConge->getQuota());

            $suiviCongeRepository->add($suiviConge, true);

            return $this->redirectToRoute('app_suivi_conge_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('suivi_conge/new.html.twig', [
            'suivi_conge' => $suiviConge,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_suivi_conge_show', methods: ['GET'])]
    public function show(SuiviConge $suiviConge): Response
    {
        return $this->render('suivi_conge/show.html.twig', [
            'suivi_conge' => $suiviConge,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_suivi_conge_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SuiviConge $suiviConge, SuiviCongeRepository $suiviCongeRepository, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(SuiviCongeType::class, $suiviConge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idemp=$form->get('employeid')->getData();
            $idcontrat= $form->get('contratid')->getData();
            $rep=$doctrine->getRepository(Employe::class);
            $emp=$rep->find($idemp);
            $suiviConge->setEmploye($emp);
            $rep=$doctrine->getRepository(Contrat::class);
            $contrat=$rep->find($idcontrat);
            $suiviConge->setContrat($contrat);
            $yeardebut= $contrat->getDatedebut()->format('Y');
            $moisdebut= $contrat->getDatedebut()->format('m');
            $suiviConge->setAnnee($yeardebut);
            $suiviConge->setMois($moisdebut);
            $suiviConge->setQuota($contrat->getQuotaparmoisaccorde());
            $suiviConge->setNbjourpris(0);
            $suiviConge->setNbjourrestant($suiviConge->getQuota());
            $suiviCongeRepository->add($suiviConge, true);

            return $this->redirectToRoute('app_suivi_conge_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('suivi_conge/edit.html.twig', [
            'suivi_conge' => $suiviConge,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_suivi_conge_delete', methods: ['POST'])]
    public function delete(Request $request, SuiviConge $suiviConge, SuiviCongeRepository $suiviCongeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$suiviConge->getId(), $request->request->get('_token'))) {
            $suiviCongeRepository->remove($suiviConge, true);
        }

        return $this->redirectToRoute('app_suivi_conge_index', [], Response::HTTP_SEE_OTHER);
    }
}
