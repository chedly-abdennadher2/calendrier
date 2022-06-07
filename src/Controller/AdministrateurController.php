<?php

namespace App\Controller;

use App\Entity\Administrateur;
use App\Entity\User;
use App\Form\AdministrateurType;
use App\Repository\AdministrateurRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/administrateur')]
class AdministrateurController extends AbstractController
{
    #[Route('/', name: 'app_administrateur_index', methods: ['GET'])]
    public function index(AdministrateurRepository $administrateurRepository): Response
    {
        return $this->render('administrateur/index.html.twig', [
            'administrateurs' => $administrateurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_administrateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AdministrateurRepository $administrateurRepository,ManagerRegistry $doctrine): Response
    {
        $administrateur = new Administrateur();
        $form = $this->createForm(AdministrateurType::class, $administrateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->get('role')->getData();
            $administrateur->setRole([$role]);
            $nomlogin = $form->get('nom')->getData();
            $rep=$doctrine->getRepository(User::class);
            $user=$rep->findOneBy(["nomutilisateur"=>$nomlogin]);
            $administrateur->setLogin($user);
            $administrateurRepository->add($administrateur, true);

            return $this->redirectToRoute('app_administrateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('administrateur/new.html.twig', [
            'administrateur' => $administrateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_administrateur_show', methods: ['GET'])]
    public function show(Administrateur $administrateur): Response
    {
        return $this->render('administrateur/show.html.twig', [
            'administrateur' => $administrateur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_administrateur_edit', methods: ['GET', 'POST'])]
    public function edit( string $id,Request $request, Administrateur $administrateur, AdministrateurRepository $administrateurRepository,ManagerRegistry $doctrine): Response
    {
        $rep=$doctrine->getRepository(Administrateur::class);
        $administrateur=$rep->find($id);

        $form = $this->createForm(AdministrateurType::class, $administrateur);
        $form->get('role')->setData(implode($administrateur->getRole()));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->get('role')->getData();
            $administrateur->setRole([$role]);
            $nomlogin = $form->get('nom')->getData();
            $rep=$doctrine->getRepository(User::class);
            $user=$rep->findOneBy(["nomutilisateur"=>$nomlogin]);
            $administrateur->setLogin($user);

            $administrateurRepository->add($administrateur, true);

            return $this->redirectToRoute('app_administrateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('administrateur/edit.html.twig', [
            'administrateur' => $administrateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_administrateur_delete', methods: ['POST'])]
    public function delete(Request $request, Administrateur $administrateur, AdministrateurRepository $administrateurRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$administrateur->getId(), $request->request->get('_token'))) {
            $administrateurRepository->remove($administrateur, true);
        }

        return $this->redirectToRoute('app_administrateur_index', [], Response::HTTP_SEE_OTHER);
    }
}
