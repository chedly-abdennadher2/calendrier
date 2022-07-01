<?php

namespace App\Controller;

use App\DataTables\CongeAdminDataTable;
use App\DataTables\CongeDataTable;
use App\Entity\Conge;
use App\Entity\Employe;
use App\Entity\SuiviConge;
use App\Entity\User;
use App\Form\CongeformulaireType;
use App\Form\CongeValiderType;
use App\Form\EmployeformType;
use App\Form\SuppressionType;
use App\Repository\CongeRepository;
use App\Repository\SuiviCongeRepository;
use App\Repository\UserRepository;
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

        $form = $this->createForm(congeformulaireType::class, $conge);
        $rep = $doctrine->getRepository(User::class);
        $form->get('id_user')->setData($user->getId());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $conge = $form->getData();
            if ($conge->getDatedebut()>$conge->getDatefin())
            {$error="vous avez tapé une date début superieur à la date de fin";
                return $this->renderForm('conge/ajouterconge.html.twig', [
                    'form' => $form,
                    'error'=>$error,
                    'admin'=>$admin
                ]);

            }

            $id = $form->get('id_user')->getData();
            $conge->setState('no check');
            $rep = $doctrine->getRepository(User::class);
            $conge->setUser($rep->find($id));
            $nbjour= $conge->calculernbjour();
            $user->setNbjourpris($nbjour);
            $entityManager->persist($conge);
            $entityManager->flush();
            if ($admin=='true') {
                return $this->redirectToRoute('consultercongedatatable');

            }

            else
            {

                return $this->redirectToRoute('consultercongeempdatatable');

            }

        }
        else
            {
                $conge->setUser($user);
            $form->get('id_user')->setData($conge->getUser()->getId());
            $form->get('nom')->setData($conge->getUser()->getNom());
            $form->get('prenom')->setData($conge->getUser()->getPrenom());


        }
        return $this->renderForm('conge/ajouterconge.html.twig', [
            'form' => $form,
            'admin'=>$admin
        ]);


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
        $form = $this->createForm(CongeformulaireType::class, $conge);
        $form->get('id_user')->setData($conge->getUser()->getId());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $conge = $form->getData();
            if ($conge->getDatedebut()>$conge->getDatefin())
            {$error="vous avez tapé une date début superieur à la date de fin";
                return $this->renderForm('conge/modifierconge.html.twig', [
                    'form' => $form,
                    'error'=>$error,
                    'admin'=>$admin
                ]);

            }
            $conge->setState('no check');
            $nbjour= $conge->calculernbjour();
            $user->setNbjourpris($nbjour);

            $entityManager->persist($conge);
            $entityManager->flush();
            if ($admin=='true') {
                return $this->redirectToRoute('consultercongedatatable');

            }

            else
            {

                return $this->redirectToRoute('consultercongeempdatatable');

            }

    }
        else
        {
            $conge->setUser($user);
            $form->get('id_user')->setData($conge->getUser()->getId());
            $form->get('nom')->setData($conge->getUser()->getNom());
            $form->get('prenom')->setData($conge->getUser()->getPrenom());


        }
        return $this->renderForm('conge/modifierconge.html.twig', [
            'form' => $form,
            'admin'=>$admin
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


            if ($form->isSubmitted() && $form->isValid()) {

                $rep = $doctrine->getRepository(Conge::class);
                $conge = $rep->find($id);
                $nbjour= $conge->calculernbjour();
                $user = $conge->getUser();
                $user->nbjourprisreset();
            if ($conge->getState() == 'valide') {
                $user->setNbjourpris($user->getNbjourpris() - $nbjour);
                if ($user->getcontratplusrecent() != null) {
                    $user->getcontratplusrecent()->setQuotarestant($user->getQuota() - $nbjour);
                }
            }
            $entityManager->remove($conge);
            $entityManager->flush();
            $entityManager->persist($user);
            $entityManager->flush();

                if ($admin=='false') {
                    return $this->redirectToRoute('consultercongeempdatatable');
                }
                else {
                return $this->redirectToRoute('consultercongedatatable');

            }


        }

            else {
                $id = $form->get('id')->setData($id);
                return $this->renderForm('conge/supprimerconge.html.twig', [
                    'form' => $form,
                    'admin'=>$admin
                ]);
            }





    }

    public function validerconge(string $choix, ManagerRegistry $doctrine, EntityManagerInterface $entityManager,User $user, Conge $conge)
    {
        $conge->calculernbjour();
        $nbjour = $conge->getNbjour();
        $contrat = $user->getcontratplusrecent();
        $moisdeconge = substr($conge->getDatedebut()->format('d/m/Y'), 3, 2);
        $anneeconge = substr($conge->getDatedebut()->format('d/m/Y'), 6, 4);

        $dispo= $this->accepterdemandedeconge($user, $moisdeconge,$anneeconge, $doctrine);
        if (($dispo==true) and ($choix=='oui')) {
            $rep = $doctrine->getRepository(SuiviConge::class);
            if ($moisdeconge - 1 <= 0) {
                $suivicongetrouvemoisprecedent = $rep->findOneBy(['annee' => $anneeconge - 1, 'mois' => 12, 'user' => $user, 'contrat' => $contrat]);
            } else {
                $suivicongetrouvemoisprecedent = $rep->findOneBy(['annee' => $anneeconge, 'mois' => $moisdeconge - 1, 'user' => $user, 'contrat' => $contrat]);
            }

            $nbjourrestant = 0;
            if ($suivicongetrouvemoisprecedent != null) {

                $nbjourrestant = $suivicongetrouvemoisprecedent->getNbjourRestant();
            }
            if ($nbjourrestant != 0) {
                if (($nbjour <= $nbjourrestant + $suivicongetrouvemoisprecedent->getQuota()) and ($conge->getState() == 'no check')) {
                    $user->setNbjourpris($user->getNbjourpris() + $nbjour);
                    if ($contrat != null) {
                        if ($user->getNbjourpris() == 0) {
                            $contrat->setQuotarestant($user->getQuota() - $nbjour);
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
                    $user->setNbjourpris($user->getNbjourpris() + $nbjour);
                    if ($contrat != null) {
                        if ($user->getNbjourpris() == 0) {
                            $contrat->setQuotarestant($user->getQuota() - $nbjour);
                        } else {
                            $contrat->setQuotarestant($contrat->getQuotarestant() - $nbjour);
                        }
                        $conge->setState('valide');

                    }

                } else {
                    $conge->setState('invalide');


                }

            }


            $conge->setUser($user);
            $entityManager->persist($conge);
            $entityManager->persist($contrat);
            $entityManager->persist($user);
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

    public function validercongerform(string $id, Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $rep = $doctrine->getRepository(Conge::class);
        $conge = $rep->find($id);
        $userauthentifie = $this->getUser();
        $rep = $doctrine->getRepository(User::class);
        $administrateur = $conge->getUser()->getAdministrateur();

        $form = $this->createForm(CongeValiderType::class, $conge);
        $form->get('id')->setData($id);
        $form->get('id_user')->setData($conge->getUser()->getId());
        $form->get('nom')->setData($conge->getUser()->getNom());
        $form->get('prenom')->setData($conge->getUser()->getPrenom());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $choix=$form->get('choix')->getData();
            $this->validerconge($choix,$doctrine, $entityManager,$conge->getUser(),$conge);
            return $this->redirectToRoute('consultercongedatatable');

        }

        return $this->renderForm('conge/validerconge.html.twig', [
            'form' => $form,
            'conge' => $conge,
            'administrateur' =>  $userauthentifie
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
        $rep = $doctrine->getRepository(User::class);
        $employes = $rep->findBy(['administrateur' => $user]);

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
        $user = $this->getUser();

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
            $id = $user->getId();
            $qb->andWhere('user.id=:user_id');
            $qb->setParameter('user_id', $id);
            return $datatableResponse->getResponse();
        }

        return $this->render('conge/consultercongeempdatatable.html.twig', array(
            'datatable' => $datatable,
        ));
    }


    public function accepterdemandedeconge(User $emp, string $mois, string $annee, ManagerRegistry $doctrine)
    {
        $repositoryconge=$doctrine->getRepository(Conge::class);
        $nbconge = $repositoryconge->compterCongeByMoisAnnee(intval($mois), intval($annee));
        $nbcongeresult=$nbconge[0][1];
        $repositoryemploye=$doctrine->getRepository(User::class);
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

    #[Route('/calcul/{idadmin}/{mois}/{annee}', name: 'calcul')]

    public function accepterdemandedecongeadmin(string $idadmin, string $mois, string $annee, ManagerRegistry $doctrine)
    {
        $repositoryadmin = $doctrine->getRepository(User::class);
        $emp = $repositoryadmin->find($idadmin);
        $roles = $user->getRoles();
        $admin = 'false';
        foreach ($roles as $clef => $value) {
            if ($value == 'ROLE_ADMIN') {
                $admin = 'true';
            }
        }
        if ($admin==true)
        {$repositoryconge=$doctrine->getRepository(Conge::class);
        $nbconge = $repositoryconge->compterCongeByMoisAnnee(intval($mois), intval($annee));
        $nbcongeresult=$nbconge[0][1];
        if ($nbcongeresult==0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
}