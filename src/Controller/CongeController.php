<?php

namespace App\Controller;

use App\DataTables\CongeAdminDataTable;
use App\DataTables\CongeDataTable;
use App\Entity\Administrateur;
use App\Entity\Conge;
use App\Entity\Employe;
use App\Entity\SuiviConge;
use App\Form\CongeadminformulaireType;
use App\Form\CongeformulaireType;
use App\Form\CongeSearchFormulaireType;
use App\Form\CongeValiderType;
use App\Form\EmployeformType;
use App\Form\SuppressionType;
use App\Repository\CongeRepository;
use App\Repository\EmployeRepository;
use App\Repository\SuiviCongeRepository;
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
        $conge = new Conge();
        $user = $this->getUser();
        $roles = $user->getRoles();
        $admin = 'false';
        foreach ($roles as $clef => $value) {
            if ($value == 'ROLE_ADMIN') {
                $admin = 'true';
            }
        }

        if ($admin=='true')
        { $form = $this->createForm(CongeadminformulaireType::class, $conge);
        $rep = $doctrine->getRepository(Administrateur::class);
        $admin = $rep->findOneBy(['login' => $user]);
        $form->get('id_admin')->setData($admin->getId());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $conge = $form->getData();
            $id = $form->get('id_admin')->getData();
            $conge->setState('no check');
            $rep = $doctrine->getRepository(Administrateur::class);
            $conge->setAdministrateur($rep->find($id));
            $entityManager->persist($conge);
            $entityManager->flush();
            return $this->redirectToRoute('consultercongedatatable');


        }
            return $this->renderForm('conge/ajouterconge.html.twig', [
                'form' => $form,
            ]);

        }
        else
        {
            $form = $this->createForm(congeformulaireType::class, $conge);
            $rep = $doctrine->getRepository(Employe::class);
            $employe = $rep->findOneBy(['login' => $user]);
            $form->get('id_employe')->setData($employe->getId());
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $conge = $form->getData();
                $id = $form->get('id_employe')->getData();
                $conge->setState('no check');
                $rep = $doctrine->getRepository(Employe::class);
                $conge->setEmploye($rep->find($id));
                $entityManager->persist($conge);
                $entityManager->flush();
                return $this->redirectToRoute('consultercongeempdatatable');


            }
            return $this->renderForm('conge/ajouterconge.html.twig', [
                'form' => $form,
            ]);

        }




    }


    #[Route('/mettreajourconge/{id}', name: 'mettreajourconge')]

    public function mettreajour(string $id, Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $rep = $doctrine->getRepository(Conge::class);
        $conge = $rep->find($id);
        $user = $this->getUser();
        $roles = $user->getRoles();
        $admin = 'false';
        foreach ($roles as $clef => $value) {
            if ($value == 'ROLE_ADMIN') {
                $admin = 'true';
            }
        }
        if ($admin=='true')
        {
            $form = $this->createForm(CongeAdminformulaireType::class, $conge);
        $form->get('id_admin')->setData($conge->getAdministrateur()->getId());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $conge = $form->getData();
            $entityManager->persist($conge);
            $entityManager->flush();
            return $this->redirectToRoute('consultercongedatatable');

        }}
        else
        {
            $form = $this->createForm(CongeformulaireType::class, $conge);
            $form->get('id_employe')->setData($conge->getEmploye()->getId());

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $conge = $form->getData();
                $entityManager->persist($conge);
                $entityManager->flush();
                return $this->redirectToRoute('consultercongeempdatatable');

            }
        }
        return $this->renderForm('conge/modifierconge.html.twig', [
            'form' => $form,
        ]);

    }

    #[Route('/supprimerconge/{id}', name: 'supprimerconge')]
    public function supprimer(String $id, Request $request, ManagerRegistry $doctrine, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $admin = 'false';
        foreach ($roles as $clef => $value) {
            if ($value == 'ROLE_ADMIN') {
                $admin = 'true';
            }
        }


        $form = $this->createForm(SuppressionType::class);
        $form->handleRequest($request);
        if ($admin=='false') {

            if ($form->isSubmitted() && $form->isValid()) {

                $rep = $doctrine->getRepository(Conge::class);
                $conge = $rep->find($id);
                $conge->calculernbjour();
                $nbjour = $conge->getNbjour();
                $emp = $conge->getEmploye();
                $emp->nbjourprisreset();
            if ($conge->getState() == 'valide') {
                $emp->setNbjourpris($emp->getNbjourpris() - $nbjour);
                if ($emp->getcontratplusrecent() != null) {
                    $emp->getcontratplusrecent()->setQuotarestant($emp->getQuota() - $nbjour);
                }
            }
            $entityManager->remove($conge);
            $entityManager->flush();
            $entityManager->persist($emp);
            $entityManager->flush();


            return $this->redirectToRoute('consultercongeempdatatable');
        }
           else {
            $id = $form->get('id')->setData($id);
               return $this->renderForm('conge/supprimerconge.html.twig', [
                   'form' => $form,
               ]);

            }
        }

       else
            {
                if ($form->isSubmitted() && $form->isValid()) {

                    $rep = $doctrine->getRepository(Conge::class);
                    $conge = $rep->find($id);
                    $entityManager->remove($conge);
                    $entityManager->flush();
                }
                else {
                    $id = $form->get('id')->setData($id);
                    return $this->renderForm('conge/supprimerconge.html.twig', [
                        'form' => $form,
                    ]);

                }
                return $this->redirectToRoute('consultercongedatatable');


            }


    }

    #[Route('/validerconge/{id}', name: 'validerconge')]
    public function validerconge(string $id, ManagerRegistry $doctrine, EntityManagerInterface $entityManager, EmployeRepository $repository)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $rep = $doctrine->getRepository(Conge::class);
        $conge = $rep->find($id);
        $emp = $conge->getEmploye();
        $conge->calculernbjour();
        $nbjour = $conge->getNbjour();
        $contrat = $emp->getcontratplusrecent();
        $moisdeconge = substr($conge->getDatedebut()->format('d/m/Y'), 3, 2);
        $anneeconge = substr($conge->getDatedebut()->format('d/m/Y'), 6, 4);

        $dispo= accepterdemandedeconge($emp->getId(), $moisdeconge,$anneeconge, $doctrine);
        if ($dispo==true) {
            $rep = $doctrine->getRepository(SuiviConge::class);
            if ($moisdeconge - 1 <= 0) {
                $suivicongetrouvemoisprecedent = $rep->findOneBy(['annee' => $anneeconge - 1, 'mois' => 12, 'employe' => $emp, 'contrat' => $contrat]);
            } else {
                $suivicongetrouvemoisprecedent = $rep->findOneBy(['annee' => $anneeconge, 'mois' => $moisdeconge - 1, 'employe' => $emp, 'contrat' => $contrat]);
            }

            $nbjourrestant = 0;
            if ($suivicongetrouvemoisprecedent != null) {

                $nbjourrestant = $suivicongetrouvemoisprecedent->getNbjourRestant();
            }
            if ($nbjourrestant != 0) {
                if (($nbjour <= $nbjourrestant + $suivicongetrouvemoisprecedent->getQuota()) and ($conge->getState() == 'no check')) {
                    $emp->setNbjourpris($emp->getNbjourpris() + $nbjour);
                    if ($contrat != null) {
                        if ($emp->getNbjourpris() == 0) {
                            $contrat->setQuotarestant($emp->getQuota() - $nbjour);
                        } else {
                            $contrat->setQuotarestant($contrat->getQuotarestant() - $nbjour);
                        }

                    }
                    $conge->setState('valide');

                } else {
                    $conge->setState('invalide');

                }
            } else {
                if (($nbjour < $contrat->getQuotaparmoisaccorde()) and ($conge->getState() == 'no check')) {
                    $emp->setNbjourpris($emp->getNbjourpris() + $nbjour);
                    if ($contrat != null) {
                        if ($emp->getNbjourpris() == 0) {
                            $contrat->setQuotarestant($emp->getQuota() - $nbjour);
                        } else {
                            $contrat->setQuotarestant($contrat->getQuotarestant() - $nbjour);
                        }
                        $conge->setState('valide');

                    }

                } else {
                    $conge->setState('invalide');


                }

            }


            $conge->setEmploye($emp);
            $entityManager->persist($conge);
            $entityManager->persist($contrat);
            $entityManager->persist($emp);
            $entityManager->flush();
        }
        else
        {
            $conge->setState('invalide');
            $entityManager->persist($conge);
            $entityManager->flush();
        }
    }


    #[Route('/validercongeform/{id}', name: 'validercongeform')]

    public function validercongerform(string $id, Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine, EmployeRepository $repository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $rep = $doctrine->getRepository(Conge::class);
        $conge = $rep->find($id);
        $user = $this->getUser();
        $rep = $doctrine->getRepository(Administrateur::class);
        $administrateur = $rep->findOneBy(['login' => $user]);

        $form = $this->createForm(CongeValiderType::class, $conge);
        $form->get('id')->setData($id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->validerconge($id, $doctrine, $entityManager, $repository);
            return $this->redirectToRoute('consultercongedatatable');

        }
        return $this->renderForm('conge/validerconge.html.twig', [
            'form' => $form,
            'conge' => $conge,
            'administrateur' => $administrateur
        ]);

    }

    public function recherchercongeparmoisetannee(string $mois, string $annee, CongeRepository $repository)
    {
        $conges = $repository->FindAllByMoisAnnee($mois, $annee);
        return $conges;
    }

    #[Route('/trierconge/{critere}', name: 'trierconge')]
    public function trier(Request $request, ManagerRegistry $doctrine, EmployeRepository $repository, PaginatorInterface $paginator, string $critere)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();
        $rep = $doctrine->getRepository(Administrateur::class);
        $administrateur = $rep->findOneBy(['login' => $user]);
        $rep = $doctrine->getRepository(Conge::class);
        $conges = $rep->findBy(array(), array($critere => 'ASC'));
        foreach ($conges as $key => $value) {
            $value->calculernbjour();
        }

        $congespages = $paginator->paginate(
            $conges, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );

        return $this->render('conge/consulterconge.html.twig', [
            'conges' => $congespages,
            'admin' => $administrateur,

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
    public function consultercongedatatable(Request $request, DatatableFactory $datatableFactory, DatatableResponse $datatableResponse, EntityManagerInterface $doctrine)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();
        $rep = $doctrine->getRepository(Employe::class);
        $employes = $rep->findBy(['admin' => $user]);

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
     *
     * @param Request $request
     *
     * @Route("/consultercongeempdatatable", name="consultercongeempdatatable")
     * @Method("GET")
     *
     * @return Response
     */
    public function consultercongeempdatatable(Request $request, DatatableFactory $datatableFactory, DatatableResponse $datatableResponse, EntityManagerInterface $doctrine)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $rep = $doctrine->getRepository(Employe::class);
        $user = $this->getUser();
        $emp = $rep->findOneBy(['login' => $user]);

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
            $qb->andWhere('employe.id=:employe');
            $qb->setParameter('employe', $id);
            return $datatableResponse->getResponse();
        }

        return $this->render('conge/consultercongeempdatatable.html.twig', array(
            'datatable' => $datatable,
        ));
    }

    #[Route('/calcul/{idemp}/{mois}/{annee}', name: 'calcul')]

    public function accepterdemandedeconge(string $idemp, string $mois, string $annee, ManagerRegistry $doctrine)
    {
        $repositoryemploye = $doctrine->getRepository(Employe::class);
        $emp = $repositoryemploye->find($idemp);
        $repositoryconge=$doctrine->getRepository(Conge::class);
        $nbconge = $repositoryconge->compterCongeByMoisAnnee(intval($mois), intval($annee));
        $nbcongeresult=$nbconge[0][1];
        $nbemploye =$repositoryemploye->countBy();

        if ($nbcongeresult<$nbemploye)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}