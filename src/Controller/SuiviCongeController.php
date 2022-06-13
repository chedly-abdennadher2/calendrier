<?php

namespace App\Controller;

use App\Entity\Contrat;
use App\Entity\Employe;
use App\Entity\SuiviConge;
use App\Form\SaisirmoisanneeType;
use App\Form\SuiviCongeType;
use App\Repository\SuiviCongeRepository;
use Container0SlFVJx\get_Console_Command_AssetsInstall_LazyService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/suivi/conge')]
class SuiviCongeController extends AbstractController
{

    public function  remplirsuivicongeauto (String $idemp,EntityManagerInterface $entityManager)
    {
        $rep=$entityManager->getRepository(Employe::class);
        $emp=$rep->find ($idemp);
        $tabcontrat =$emp->getContrat();

        foreach ($tabcontrat as $clef=>$value)
        {
            $yeardebut= $value->getDatedebut()->format('Y');
            $moisdebut= $value->getDatedebut()->format('m');
            $yearfin= $value->getDatefin()->format('Y');
            $moisfin= $value->getDatefin()->format('m');
            $moisiteration=$moisdebut;

            for ($i=$yeardebut;$i<=$yearfin;$i++)
            {

              for ( $moisiteration=$moisdebut;$moisiteration<13;$moisiteration++)
              {
                  $suiviconge=new SuiviConge ();
                  $suiviconge->setEmploye($emp);
                  $suiviconge->setContrat($value);
                  $suiviconge->setQuota($value->getQuotaparmoisaccorde());
                  $suiviconge->setNbjourpris(0);
                  $suiviconge->setMois($moisiteration);
                  $suiviconge->setAnnee($i);
                  $suiviconge->setNbjourrestant($suiviconge->getQuota());
                  $entityManager->persist ($suiviconge);
                  $entityManager->flush();

              }

            }
        }


    }

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
    #[Route('/showemp', name: 'app_suivi_conge_showemp', methods: ['GET'])]
    public function afficherparemp(ManagerRegistry $doctrine)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $rep= $doctrine->getRepository(Employe::class);
        $user =$this->getUser();
        $emp=$rep->findOneBy(['login'=>$user]);
        $rep=$doctrine->getRepository(SuiviConge::class);
        $suivi_conges=$rep->findBy(['employe'=>$emp]);
        return $this->render('suivi_conge/showemp.html.twig', [
            'suivi_conges' => $suivi_conges,
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
public function calculernbjour(Integer $mois,Integer $annee, Integer $idemp, EntityManager $entityManager)
{
    $rep=$entityManager->getRepository(Employe::class);
    $emp=$rep->find($idemp);
    if ($emp!=null) {
        $rep = $entityManager->getRepository(SuiviConge::class);
        $suivi_conges = $rep->findBy(['employe' => $emp]);
        if ($suivi_conges!=null)
        {
            foreach ($suivi_conges as $cle=>$value)
            {
                $tabconge=$value->getEmploye()->getConge();
                if ($value->getNbjourpris()==0)
                {foreach ($tabconge as $clef2=>$value2)
                {
                    $dateconge=$value2->getDateDebut();

                    if (($dateconge->format('m')==$mois) and($dateconge->format('Y')==$annee)) {
                        $nbjourprisparconge = $value2->calculerNbjourpourcommande($value2->getId(), $this->entityManager);
                        $value->setNbjourpris($value->getNbjourpris()+$nbjourprisparconge);
                    }
                }
                }
                else
                {


                }
                $value->setNbjourRestant($value->getQuota()-$value->getNbjourpris());

                $this->entityManager->persist($value);
                $this->entityManager->flush();
            }

        }

    }

}

}

