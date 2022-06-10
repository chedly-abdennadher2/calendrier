<?php

namespace App\Controller;

use App\Entity\Conge;
use App\Entity\Employe;
use App\Form\CongeformulaireType;
use App\Form\CongeValiderType;
use App\Form\EmployeformType;
use App\Form\SuppressionType;
use App\Repository\EmployeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Validator\Constraints\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Console\Helper\render;

class CongeController extends AbstractController
{
    #[Route('/ajouterconge', name: 'ajouterconge')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $conge = new Conge();
        $form = $this->createForm(congeformulaireType::class, $conge);
        $employecontroller=new EmployeController();
        $id = $employecontroller->rechercheridparlogin($doctrine, $authenticationUtils);
        $rep = $doctrine->getRepository(Employe::class);
        $employe = $rep->find($id);
        $form->get('id')->setData($employe->getId());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $conge = $form->getData();
            $id = $form->get('id')->getData();
            $conge->setState('no check');
            $rep = $doctrine->getRepository(Employe::class);
            $conge->setEmploye($rep->find($id));
            $entityManager->persist($conge);
            $entityManager->flush();
            return $this->redirectToRoute('consultercongeemp', ['id'=>$id], Response::HTTP_SEE_OTHER);


        }
        return $this->renderForm('conge/ajouterconge.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/consulterconge', name: 'consulterconge')]

    public function consulter(ManagerRegistry $doctrine)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $rep = $doctrine->getRepository(Conge::class);
        $conges = $rep->findAll();
        foreach ($conges as $key => $value) {
            $value->calculernbjour($value->getId(), $doctrine);
        }
        return $this->render('conge/consulterconge.html.twig', [
            'conges' => $conges,
        ]);
    }

    #[Route('/mettreajourconge/{id}', name: 'mettreajourconge')]

    public function mettreajour(string $id, Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $rep = $doctrine->getRepository(Conge::class);
        $conge = $rep->find($id);
        $form = $this->createForm(CongeformulaireType::class, $conge);
        $form->get('id')->setData($conge->getEmploye()->getId());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $conge = $form->getData();
            $entityManager->persist($conge);
            $entityManager->flush();
            return $this->redirectToRoute('consultercongeemp', ['id'=>$id], Response::HTTP_SEE_OTHER);

        }
        return $this->renderForm('conge/modifierconge.html.twig', [
            'form' => $form,
        ]);

    }

    #[Route('/supprimerconge/{id}', name: 'supprimerconge')]
    public function supprimer(String $id, Request $request, ManagerRegistry $doctrine, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(SuppressionType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $rep = $doctrine->getRepository(Conge::class);
            $conge = $rep->find($id);
            $conge->calculernbjour($conge->getId(), $doctrine);
            $nbjour = $conge->getNbjour();
            $emp = $conge->getEmploye();
            $emp->nbjourprisreset();
            if ($conge->getState ()=='valide')
            {
                $emp->setNbjourpris($emp->getNbjourpris() - $nbjour);
                if ($emp->getcontratplusrecent() != null) {
                    $emp->getcontratplusrecent()->setQuotarestant($emp->getQuota() - $nbjour);
                }
            }
            $entityManager->remove($conge);
            $entityManager->flush();
            $entityManager->persist($emp);
            $entityManager->flush();

            return $this->redirectToRoute('consultercongeemp', ['id'=>$id], Response::HTTP_SEE_OTHER);

        } else {
            $id = $form->get('id')->setData($id);

        }
        return $this->renderForm('conge/supprimerconge.html.twig', [
            'form' => $form,
        ]);

    }

    #[Route('/validerconge/{id}', name: 'validerconge')]

    public function validerconge(string $id, ManagerRegistry $doctrine, EntityManagerInterface $entityManager, EmployeRepository $repository)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $rep = $doctrine->getRepository(Conge::class);
        $conge = $rep->find($id);
        $emp = $conge->getEmploye();
        $conge->calculernbjour($conge->getId(), $doctrine);
        $nbjour = $conge->getNbjour();
        if (($nbjour <= $emp->getQuota()) and ($conge->getState() == 'no check')) {
            $emp->nbjourprisreset();
            $emp->setNbjourpris($emp->getNbjourpris() + $nbjour);
            if ($emp->getcontratplusrecent()!=null)
            {$emp->getcontratplusrecent()->setQuotarestant($emp->getQuota() - $nbjour);}
            $conge->setState('valide');

        } else if ($conge->getState() == 'no check') {
            $conge->setState('invalide');


        }
        $conge->setEmploye($emp);
        $entityManager->persist($conge);
        $entityManager->flush();
        $repository->add($emp);
    }

    #[Route('/consultercongeemp', name: 'consultercongeemp')]

    public function consultercongerdeemployer(ManagerRegistry $doctrine
    )
    {
        $this->denyAccessUnlessGranted('ROLE_USER');


        $rep = $doctrine->getRepository(Employe::class);
        $user=$this->getUser();
        $employe=$rep->findOneBy(['login'=>$user]);

        $rep = $doctrine->getRepository(Conge::class);
        $conges = $rep->findBy(['employe' => $employe]);
        foreach ($conges as $key => $value) {
            $value->calculernbjour($value->getId(), $doctrine);
        }
        return $this->render('conge/consultercongeemp.html.twig', [
            'conges' => $conges,
        ]);

    }

    #[Route('/validercongeform/{id}', name: 'validercongeform')]

    public function validercongerform(string $id, Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine,EmployeRepository $repository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $rep = $doctrine->getRepository(Conge::class);
        $conge = $rep->find($id);
        $form = $this->createForm(CongeValiderType::class, $conge);
       $form->get('id')->setData($id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->validerconge($id,$doctrine,$entityManager,$repository);
            return $this->redirectToRoute('consulterconge', [], Response::HTTP_SEE_OTHER);

        }
        return $this->renderForm('conge/validerconge.html.twig', [
            'form' => $form,
        ]);

    }

}
