<?php

namespace App\Controller;

use App\DataTables\SuiviCongeAdminDataTable;
use App\Entity\Contrat;
use App\Entity\SuiviConge;
use App\Form\SaisirmoisanneeType;
use App\Form\SuiviCongeType;
use App\Repository\SuiviCongeRepository;
use Container0SlFVJx\get_Console_Command_AssetsInstall_LazyService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Datatable\DatatableInterface;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/suivi/conge')]
class SuiviCongeController extends AbstractController
{
    #[Route('/consultersuivicongedatatable', name: 'consultersuivicongedatatable')]
    public function consultersuivicongedatatable(Request $request, DatatableFactory $datatableFactory, DatatableResponse $datatableResponse)
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
        $datatable = $datatableFactory->create(SuiviCongeAdminDataTable::class);
        $datatable->buildDatatable();
        if ($isAjax) {
            $responseService = $datatableResponse;
            $responseService->setDatatable($datatable);
            $responseService->getDatatableQueryBuilder();

            return $responseService->getResponse();
        }

        return $this->render('suivi_conge/consultersuivicongeadmindatatable.html.twig', array(
            'datatable' => $datatable,
        ));
    }
    #[Route('/consultersuivicongeempdatatable', name: 'consultersuivicongeempdatatable')]

    public function consultersuivicongeempdatatable(Request $request, DatatableFactory $datatableFactory, DatatableResponse $datatableResponse,EntityManagerInterface $doctrine)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $rep = $doctrine->getRepository(Employe::class);
        $user=$this->getUser();
        $emp = $rep->findOneBy(['login'=>$user]);

        $isAjax = $request->isXmlHttpRequest();

        // Get your Datatable ...
        //$datatable = $this->get('app.datatable.post');
        //$datatable->buildDatatable();

        // or use the DatatableFactory
        /**
         * @var DatatableInterface $datatable
         */
        $datatable = $datatableFactory->create(SuiviCongeAdminDataTable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $datatableResponse->setDatatable($datatable);
            $datatableQueryBuilder = $datatableResponse->getDatatableQueryBuilder();
            $qb = $datatableQueryBuilder->getQb();
            $id = $emp->getId();
            $qb->andWhere('employe.id=:employe');
            $qb->setParameter('employe', $id);
            return $datatableResponse->getResponse();
        }

        return $this->render('suivi_conge/consultersuivicongedatatable.html.twig', array(
            'datatable' => $datatable,
        ));
    }


    #[Route('/new', name: 'app_suivi_conge_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SuiviCongeRepository $suiviCongeRepository,ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
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
          if ($contrat->getEmploye()->  getId()==$emp->getId()) {
              $suiviConge->setContrat($contrat);
              $yeardebut = $contrat->getDatedebut()->format('Y');
              $moisdebut = $contrat->getDatedebut()->format('m');
              $suiviConge->setAnnee($yeardebut);
              $suiviConge->setMois($moisdebut);
              $suiviConge->setQuota($contrat->getQuotaparmoisaccorde());
              $suiviConge->setNbjourpris(0);
              $suiviConge->setNbjourrestant($suiviConge->getQuota());

              $suiviCongeRepository->add($suiviConge, true);
          }
            return $this->redirectToRoute('consultersuivicongedatatable');
        }

        return $this->renderForm('suivi_conge/new.html.twig', [
            'suivi_conge' => $suiviConge,
            'form' => $form,
        ]);
    }

    #[Route('/afficher', name: 'app_suivi_conge_afficher_par_mois_annee', methods: ['GET','POST'])]
    public function affichernbjourpris (Request $request,ManagerRegistry $doctrine,SuiviCongeRepository $suiviCongeRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $suiviConge = new SuiviConge();
        $form = $this->createForm(SaisirmoisanneeType::class, $suiviConge);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mois=$form->get('mois')->getData();
            $annee=$form->get('annee')->getData();
            $idcontrat=$form->get('idcontrat')->getData();
            $rep=$doctrine->getRepository(Contrat::class);
            $contrat=$rep->find($idcontrat);
            $rep = $doctrine->getRepository(Employe::class);
            $user = $this->getUser();
            $emp = $rep->findOneBy(['login' => $user]);


            if ($emp != null) {
                $rep = $doctrine->getRepository(SuiviConge::class);
                $suivi_conges = $rep->findBy(['employe' => $emp,'mois'=>$mois,'annee'=>$annee,'contrat'=>$contrat]);

                if ($mois=='tout'){
                    $suivi_conges = $rep->findBy(['employe' => $emp,'annee'=>$annee,'contrat'=>$contrat]);

                }
                if  ($annee=='tout'){
                    $suivi_conges = $rep->findBy(['employe' => $emp,'mois'=>$mois,'contrat'=>$contrat]);

                }
                if (($mois=='tout') and ($annee=='tout')){
                    $suivi_conges = $rep->findBy(['employe' => $emp,'contrat'=>$contrat]);
                }
                return $this->render('suivi_conge/showmoisannee.html.twig', [
                    'suivi_conges' => $suivi_conges,
                ]);


            }
            return $this->render('suivi_conge/showmoisannee.html.twig',
            );

        }
        return $this->renderForm('suivi_conge/saisirmoisannee.html.twig', [
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

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

            if ($contrat->getEmploye()->  getId()==$emp->getId()) {

                $suiviConge->setContrat($contrat);
                $yeardebut = $contrat->getDatedebut()->format('Y');
                $moisdebut = $contrat->getDatedebut()->format('m');
                $suiviConge->setAnnee($yeardebut);
                $suiviConge->setMois($moisdebut);
                $suiviConge->setQuota($contrat->getQuotaparmoisaccorde());
                $suiviConge->setNbjourpris(0);
                $suiviConge->setNbjourrestant($suiviConge->getQuota());
                $suiviCongeRepository->add($suiviConge, true);
            }
            return $this->redirectToRoute('consultersuivicongedatatable');
        }
        else {
            $form->get('employeid')->setData($suiviConge->getEmploye()->getId());
            $form->get('contratid')->setData($suiviConge->getContrat()->getId());


        }

        return $this->renderForm('suivi_conge/edit.html.twig', [
            'suivi_conge' => $suiviConge,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_suivi_conge_delete', methods: ['POST'])]
    public function delete(Request $request, SuiviConge $suiviConge, SuiviCongeRepository $suiviCongeRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$suiviConge->getId(), $request->request->get('_token'))) {
            $suiviCongeRepository->remove($suiviConge, true);
        }

        return $this->redirectToRoute('consultersuivicongedatatable');
    }




}


