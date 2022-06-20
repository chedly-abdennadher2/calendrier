<?php

namespace App\Controller;

use App\DataTables\CongeAdminDataTable;
use App\DataTables\CongeDataTable;
use App\Entity\Administrateur;
use App\Entity\Conge;
use App\Entity\Employe;
use App\Form\CongeformulaireType;
use App\Form\CongeSearchFormulaireType;
use App\Form\CongeValiderType;
use App\Form\EmployeformType;
use App\Form\SuppressionType;
use App\Repository\CongeRepository;
use App\Repository\EmployeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Datatable\DatatableInterface;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Validator\Constraints\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use function Symfony\Component\Console\Helper\render;
use Knp\Component\Pager\PaginatorInterface;
class CongeController extends AbstractController
{
    #[Route('/ajouterconge', name: 'ajouterconge')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $conge = new Conge();
        $form = $this->createForm(congeformulaireType::class, $conge);
        $user=$this->getUser();
        $rep = $doctrine->getRepository(Employe::class);
        $employe = $rep->findOneBy(['login'=>$user]);
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

    public function consulter(Request $request,ManagerRegistry $doctrine,CongeRepository $repository,PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user=$this->getUser();
        $rep=$doctrine->getRepository(Administrateur::class);
        $administrateur=$rep->findOneBy(['login'=>$user]);

        $rep = $doctrine->getRepository(Conge::class);

        $conges = $rep->findAll();
        foreach ($conges as $key => $value) {
            $value->calculernbjour($value->getId(), $doctrine);
        }
        $conge =new Conge();
        $form=$this->createForm(CongeSearchFormulaireType::class,$conge);
        $form->handleRequest($request);
        $congesearchpages=null;
        if ($form->isSubmitted() && $form->isValid())
        {
            $mois=$form->get('mois')->getData();
            $annee=$form->get('annee')->getData();
        $congesearch=    $this->recherchercongeparmoisetannee($mois,$annee,$repository);
            $congesearchpages = $paginator->paginate(
                $congesearch, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                6 // Nombre de résultats par page
            );

        }
        $congespages = $paginator->paginate(
            $conges, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );

        return $this->render('conge/consulterconge.html.twig', [
            'conges' => $congespages,
            'admin'=>$administrateur,
            'form'=>$form->createView(),
            'congesearch'=>$congesearchpages
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
        $contrat=$emp->getcontratplusrecent();

        if (($nbjour <= $contrat->getQuotaparmoisaccorde()) and ($contrat->getQuotarestant()>0 ) and ($conge->getState() == 'no check')) {
            $emp->setNbjourpris($emp->getNbjourpris() + $nbjour);
            if ($contrat!=null)
            {  if ($emp->getNbjourpris()==0)
            { $contrat->setQuotarestant($emp->getQuota() - $nbjour);}
            else
            {
                $contrat->setQuotarestant($contrat->getQuotarestant()- $nbjour);
            }


            }
            $conge->setState('valide');

        } else if ($conge->getState() == 'no check') {
            $conge->setState('invalide');


        }
        $conge->setEmploye($emp);
        dump($contrat->getQuotaRestant());
        $entityManager->persist($conge);
        $entityManager->persist($contrat);
        $entityManager->persist($emp);
        $entityManager->flush();

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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
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
    public function recherchercongeparmoisetannee (string $mois,string $annee,CongeRepository $repository)
    {$conges= $repository->FindAllByMoisAnnee($mois,$annee);
return $conges;
    }
    #[Route('/trierconge/{critere}', name: 'trierconge')]
    public function trier(Request $request, ManagerRegistry $doctrine,EmployeRepository $repository, PaginatorInterface $paginator,string $critere)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user =$this->getUser();
        $rep=$doctrine->getRepository(Administrateur::class);
        $administrateur=$rep->findOneBy(['login'=>$user]);
        $rep=$doctrine->getRepository(Conge::class);
        $conges= $rep->findBy(array(),array($critere=>'ASC'));
        foreach ($conges as $key => $value) {
            $value->calculernbjour($value->getId(), $doctrine);
        }

        $congespages = $paginator->paginate(
            $conges, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );

        return $this->render('conge/consulterconge.html.twig', [
            'conges' => $congespages,
            'admin'=>$administrateur,

        ]);
    }
    /**
     * Lists all Post entities.
     *
     * @param Request $request
     *
     * @Route("/consultercongedatatable", name="consultercongedatatable")
     * @Method("GET")
     *
     * @return Response
     */
    public function consultercongedatatable(Request $request, DatatableFactory $datatableFactory, DatatableResponse $datatableResponse)
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
        $datatable = $datatableFactory->create(CongeAdminDataTable::class);
        $datatable->buildDatatable();
        if ($isAjax) {
            $responseService = $datatableResponse;
            $responseService->setDatatable($datatable);
            $responseService->getDatatableQueryBuilder();

            return $responseService->getResponse();
        }

        return $this->render('conge/consultercongedatatable.html.twig', array(
            'datatable' => $datatable,
        ));
    }

    /**
     * Lists all Post entities.
     *
     * @param Request $request
     *
     * @Route("/consultercongeempdatatable", name="consultercongeempdatatable")
     * @Method("GET")
     *
     * @return Response
     */
    public function consultercongeempdatatable(Request $request, DatatableFactory $datatableFactory, DatatableResponse $datatableResponse,EntityManagerInterface $doctrine)
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
        $datatable = $datatableFactory->create(CongeDataTable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $datatableResponse->setDatatable($datatable);
            $datatableQueryBuilder = $datatableResponse->getDatatableQueryBuilder();
            $qb = $datatableQueryBuilder->getQb();
            $id = $emp->getId();
            $qb->leftJoin("conge.employe","employe");
            $qb->andWhere('employe.id=:employe');
            $qb->setParameter('employe', $id);
            return $datatableResponse->getResponse();
        }

        return $this->render('conge/consultercongeempdatatable.html.twig', array(
            'datatable' => $datatable,
        ));
    }



}
