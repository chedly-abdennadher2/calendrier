<?php

namespace App\Controller;

use App\DataTables\CongeDataTable;
use App\DataTables\ContratAdminDataTable;
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
        $datatable = $datatableFactory->create(ContratAdminDataTable::class);
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
        $datatable = $datatableFactory->create(ContratAdminDataTable::class);
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
        if ($admin == 'false') {

            if($user!=null)
         {
             $form->get('employe')->setData($user->getId());}}


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $id = $form->get('employe')->getData();
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
    public function edit(Request $request, Contrat $contrat, ContratRepository $contratRepository, ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ContratUpdateType::class, $contrat);
        $form->get('employe')->setData($contrat->getUser()->getId());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $id = $form->get('employe')->getData();
            $rep = $doctrine->getRepository(User::class);
            $emp = $rep->find($id);
            $contrat->setUser($emp);
            $contrat->calculquotaparmoisaccorde();
            $emp->calculerquota();
            $entityManager->persist($emp);
            $entityManager->flush();
            $contratRepository->add($contrat, true);
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

        return $this->renderForm('contrat/edit.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
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

    #[Route('/triercontrat/{critere}', name: 'triercontrat')]
    public function trier(Request $request, ManagerRegistry $doctrine, EmployeRepository $repository, PaginatorInterface $paginator, string $critere)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $rep = $doctrine->getRepository(Contrat::class);
        $contrats = $rep->findBy(array(), array($critere => 'ASC'));

        $contratpages = $paginator->paginate(
            $contrats, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );

        return $this->render('contrat/index.html.twig', [
            'contrats' => $contratpages,

        ]);
    }

}