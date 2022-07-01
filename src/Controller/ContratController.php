<?php

namespace App\Controller;

use App\DataTables\CongeDataTable;
use App\DataTables\ContratDataTable;
use App\Entity\Conge;
use App\Entity\Contrat;
use App\Entity\User;
use App\Form\ContratType;
use App\Form\ContratUpdateType;
use App\Repository\ContratRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Datatable\DatatableInterface;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
#[Route('/contrat')]
class ContratController extends AbstractController
{
    #[Route('/consultercontratdatatable', name: 'consultercontratdatatable')]

    public function consultercontratdatatable(Request $request, DatatableFactory $datatableFactory, DatatableResponse $datatableResponse)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $isAjax = $request->isXmlHttpRequest();

        // Get your Datatable ...
        //$datatable = $this->get('app.datatable.post');
        //$datatable->buildDatatable();

        // or use the DatatableFactory
        /**
         * @var DatatableInterface $datatable
         */
        $datatable = $datatableFactory->create(ContratDataTable::class);
        $datatable->buildDatatable();
        if ($isAjax) {
            $responseService = $datatableResponse;
            $responseService->setDatatable($datatable);
            $responseService->getDatatableQueryBuilder();

            return $responseService->getResponse();
        }

        return $this->render('contrat/consultercontratdatatable.html.twig', array(
            'datatable' => $datatable,
        ));
    }
    #[Route('/consultercontratempdatatable', name: 'consultercontratempdatatable')]

    public function consultercontratempdatatable(Request $request, DatatableFactory $datatableFactory, DatatableResponse $datatableResponse,EntityManagerInterface $doctrine)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user=$this->getUser();

        $isAjax = $request->isXmlHttpRequest();

        // Get your Datatable ...
        //$datatable = $this->get('app.datatable.post');
        //$datatable->buildDatatable();

        // or use the DatatableFactory
        /**
         * @var DatatableInterface $datatable
         */
        $datatable = $datatableFactory->create(ContratDataTable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $datatableResponse->setDatatable($datatable);
            $datatableQueryBuilder = $datatableResponse->getDatatableQueryBuilder();
            $qb = $datatableQueryBuilder->getQb();
            $id = $user->getId();
            $qb->andWhere('user.id=:id');
            $qb->setParameter('id', $id);
            return $datatableResponse->getResponse();
        }

        return $this->render('contrat/consultercontratempdatatable.html.twig', array(
            'datatable' => $datatable,
        ));
    }



    #[Route('/new', name: 'app_contrat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContratRepository $contratRepository, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {

        $user = $this->getUser();
        $contrat = new Contrat();
        $form = $this->createForm(ContratType::class, $contrat);
        $roles=$user->getRoles();
        $admin = 'false';
        foreach ($roles as $clef => $value) {
            if ($value == 'ROLE_ADMIN') {
                $admin = 'true';
            }
        }

            if($user!=null)
            {$form->get('employe_id')->setData($user->getId());
             $form->get('employe_nom')->setData($user->getNom());
             $form->get('employe_prenom')->setData($user->getPrenom());

         }


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contrat= $form->getData();
            if ($contrat->getDatedebut()>$contrat->getDatefin()) {
                $error = "vous avez tapé une date début superieur à la date de fin";
                return $this->renderForm('contrat/new.html.twig', [
                    'contrat' => $contrat,
                    'form' => $form,
                    'error'=>$error,
                    'admin'=>$admin
                ]);

            }

                $id = $form->get('employe_id')->getData();
            $rep = $doctrine->getRepository(User::class);
            $emp = $rep->find($id);
            $contrat->setUser($emp);
            $contrat->calculquotaparmoisaccorde();
            $emp->addContrat($contrat);
            $contratRepository->add($contrat, true);
            $emp->calculerquota();
            $entityManager->persist($emp);
            $entityManager->flush();
            $roles = $user->getRoles();
            $admin = 'false';
            foreach ($roles as $clef => $value) {
                if ($value == 'ROLE_ADMIN') {
                    $admin = 'true';
                }
            }
            if ($admin == 'true') {
                return $this->redirectToRoute('consultercontratdatatable');
            } else {
                return $this->redirectToRoute('consultercontratempdatatable');

            }
        }
        return $this->renderForm('contrat/new.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
            'admin'=>$admin

        ]);
    }



    #[Route('/{id}', name: 'app_contrat_show', methods: ['GET'])]
    public function show(Contrat $contrat): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $admin = 'false';
        foreach ($roles as $clef => $value) {
            if ($value == 'ROLE_ADMIN') {
                $admin = 'true';
            }
        }
        return $this->render('contrat/show.html.twig', [
            'contrat' => $contrat,
            'admin'=>$admin
        ]);
    }

#[Route('/{id}/edit', name: 'app_contrat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contrat $contrat, ContratRepository $contratRepository, ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $admin = 'false';
        foreach ($roles as $clef => $value) {
            if ($value == 'ROLE_ADMIN') {
                $admin = 'true';
            }
        }

        $form = $this->createForm(ContratType::class, $contrat);
        $form->get('employe_id')->setData($contrat->getUser()->getId());
        $form->get('employe_nom')->setData($contrat->getUser()->getNom());
        $form->get('employe_prenom')->setData($contrat->getUser()->getPrenom());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contrat=$form->getData();
            if ($contrat->getDatedebut() >$contrat->getDatefin())
            {
                $error="vous avez tapé une date de debut supérieur à la date de fin";
                return $this->renderForm('contrat/edit.html.twig', [
                    'contrat' => $contrat,
                    'form' => $form,
                    'error'=>$error,
                    'admin'=>$admin
                ]);

            }
            $id = $form->get('employe_id')->getData();
            $rep = $doctrine->getRepository(User::class);
            $emp = $rep->find($id);
            $contrat->setUser($emp);
            $contrat->calculquotaparmoisaccorde();
            $emp->calculerquota();
            $entityManager->persist($emp);
            $entityManager->flush();
            $contratRepository->add($contrat, true);
            if ($admin == 'true') {
                return $this->redirectToRoute('consultercontratdatatable');
            } else {
                return $this->redirectToRoute('consultercontratempdatatable');

            }
        }

        return $this->renderForm('contrat/edit.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
            'admin'=>$admin
        ]);

    }


    #[Route('/{id}', name: 'app_contrat_delete', methods: ['POST'])]
    public function delete(Request $request, Contrat $contrat, ContratRepository $contratRepository): Response
    {
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('delete' . $contrat->getId(), $request->request->get('_token'))) {
            $contratRepository->remove($contrat, true);
        }

        $roles = $user->getRoles();
        $admin = 'false';
        foreach ($roles as $clef => $value) {
            if ($value == 'ROLE_ADMIN') {
                $admin = 'true';
            }
        }
        if ($admin == 'true') {
            return $this->redirectToRoute('consultercontratdatatable');
        } else {
            return $this->redirectToRoute('consultercontratempdatatable');

        }
    }


}