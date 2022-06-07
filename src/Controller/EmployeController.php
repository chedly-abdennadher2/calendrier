<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EmployeformType;
use App\Form\SuppressionType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Finder\contains;
use App\Entity\Employe;

class EmployeController extends AbstractController
{
    #[Route('/ajouteremploye', name: 'ajouteremploye')]
    public function ajouter(Request $request,EntityManagerInterface $entityManager,ManagerRegistry $doctrine) :Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Call whatever methods you've added to your User class
        // For example, if you added a getFirstName() method, you can use that.
    $emp =new Employe();
    $form = $this->createForm(EmployeformType::class,$emp);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $nomlogin = $form->get('nom')->getData();
            $rep=$doctrine->getRepository(User::class);
            $user=$rep->findOneBy(["nomutilisateur"=>$nomlogin]);
            $emp= $form->getData();
            $emp->setLogin($user);

        $entityManager->persist($emp);
        $entityManager->flush();

        }
    return $this->renderForm('employe/ajouteremploye.html.twig', [
     'form' => $form,
 ]);

    }
    #[Route('/consulteremploye', name: 'consulteremploye')]

    public function consulter(ManagerRegistry $doctrine)
    {
 $rep=$doctrine->getRepository(Employe::class);
$employes= $rep->findAll();

        return $this->render('employe/consulteremploye.html.twig', [
            'employes' => $employes,
        ]);
    }
    #[Route('/mettreajouremploye/{id}', name: 'mettreajouremploye')]

public function mettreajour(string $id, Request $request,EntityManagerInterface $entityManager,ManagerRegistry $doctrine) :Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $rep=$doctrine->getRepository(Employe::class);
    $emp=$rep->find($id);

    $form = $this->createForm(EmployeformType::class,$emp);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $emp= $form->getData();
        $nomlogin = $form->get('nom')->getData();
        $rep=$doctrine->getRepository(User::class);
        $user=$rep->findOneBy(["nomutilisateur"=>$nomlogin]);
        $emp->setLogin($user);
        $entityManager->persist($emp);
        $entityManager->flush();

    }
    return $this->renderForm('employe/modifieremploye.html.twig', [
        'form' => $form,
    ]);
}
    #[Route('/supprimeremploye/{id}', name: 'supprimeremploye')]
public function supprimer (string $id, Request $request,ManagerRegistry $doctrine,EntityManagerInterface $entityManager)
{
    $form = $this->createForm(SuppressionType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $rep=$doctrine->getRepository(Employe::class);
        $emp=$rep->find($id);

        $entityManager->remove($emp);
        $entityManager->flush();
    }
else {
    $form->get('id')->setData($id);}

    return $this->renderForm('employe/supprimeremploye.html.twig', [
        'form' => $form,
    ]);

}
}
