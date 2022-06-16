<?php

namespace App\Controller;

use App\DataTables\EmployeDataTable;
use App\Entity\Administrateur;
use App\Entity\Conge;
use App\Entity\User;
use App\Form\EmployeAdminformType;
use App\Form\EmployeformType;
use App\Form\EmployeSearchFormType;
use App\Form\SuppressionType;
use App\Repository\EmployeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sg\DatatablesBundle\Datatable\DatatableInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function Symfony\Bundle\FrameworkBundle\Controller\redirectToRoute;
use function Symfony\Component\Finder\contains;
use App\Entity\Employe;

use Knp\Component\Pager\PaginatorInterface;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
class EmployeController extends AbstractController
{

    #[Route('/ajouteremploye', name: 'ajouteremploye')]
    public function ajouter(Request $request,EntityManagerInterface $entityManager,ManagerRegistry $doctrine) :Response
    {

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
            return $this->redirectToRoute('/');
        }
    return $this->renderForm('employe/ajouteremploye.html.twig', [
     'form' => $form,
 ]);

    }
    #[Route('/affecteradmin', name: 'affecteradmin')]
    public function ajouterempadmin(Request $request,EntityManagerInterface $entityManager,ManagerRegistry $doctrine) :Response
    {

        // Call whatever methods you've added to your User class
        // For example, if you added a getFirstName() method, you can use that.
        $emp=new Employe();
        $form = $this->createForm(EmployeAdminformType::class,$emp);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $loginemp=$form->get('loginemploye')->getData();
            $loginadmin=$form->get('loginadmin')->getData();
            $rep=$entityManager->getRepository(User::class);
            $useremp=$rep->findBy(['nomutilisateur'=>$loginemp]);
            $useradmin=$rep->findBy(['nomutilisateur'=>$loginadmin]);

            $rep=$entityManager->getRepository(Employe::class);
            $emp=$rep->findOneBy(['login'=>$useremp]);
            $rep=$entityManager->getRepository(Administrateur::class);
            $admin=$rep->findOneBy(['login'=>$useradmin]);
            $emp->setAdmin($admin);
            $admin->addEmploye($emp);
            $entityManager->persist($emp);
            $entityManager->persist($admin);

            $entityManager->flush();
            return $this->redirectToRoute('/');
        }
        return $this->renderForm('employe/ajouteremploye.html.twig', [
            'form' => $form,
        ]);

    }
    #[Route('/modifieraffectationadmin/{loginemploye}/{loginadmin}', name: 'modifieraffectationadmin')]
    public function modifierempadmin(string $loginemploye,string $loginadmin, Request $request,EntityManagerInterface $entityManager,ManagerRegistry $doctrine) :Response
    {

        // Call whatever methods you've added to your User class
        // For example, if you added a getFirstName() method, you can use that.
        $emp=new Employe();
        $form = $this->createForm(EmployeAdminformType::class,$emp);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $loginemp=$form->get('loginemploye')->getData();
            $loginadmin=$form->get('loginadmin')->getData();
            $rep=$entityManager->getRepository(User::class);
            $useremp=$rep->findBy(['nomutilisateur'=>$loginemp]);
            $useradmin=$rep->findBy(['nomutilisateur'=>$loginadmin]);

            $rep=$entityManager->getRepository(Employe::class);
            $emp=$rep->findOneBy(['login'=>$useremp]);
            $rep=$entityManager->getRepository(Administrateur::class);
            $admin=$rep->findOneBy(['login'=>$useradmin]);
            $emp->setAdmin($admin);
            $admin->addEmploye($emp);

            $entityManager->persist($emp);
            $entityManager->persist($admin);

            $entityManager->flush();
            return $this->redirectToRoute('/');
        }
        else
        {
            $form->get('loginemploye')->setData($loginemploye);
            $form->get('loginadmin')->setData($loginadmin);

        }
        return $this->renderForm('employe/ajouteremploye.html.twig', [
            'form' => $form,
        ]);

    }
    
    
    #[Route('/consulteremploye', name: 'consulteremploye')]

    public function consulter(Request $request, ManagerRegistry $doctrine,EmployeRepository $repository, PaginatorInterface $paginator)
    {
        $employessearchpages=null;
        $user=$this->getUser();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $rep=$doctrine->getRepository(Administrateur::class);
        $administrateur=$rep->findOneBy(['login'=>$user]);
        $rep=$doctrine->getRepository(Employe::class);
        $employes= $rep->findAll();
        $employe=new Employe();
        $form=$this->createForm(EmployeSearchFormType::class,$employe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $nom=$form->get('nom')->getData();
            $prenom=$form->get('prenom')->getData();

            $employessearch= $this->rechercherparnomprenom($nom,$prenom,$repository);
            $employessearchpages = $paginator->paginate(
                $employessearch, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                6 // Nombre de résultats par page
            );

        }
        $employespages = $paginator->paginate(
            $employes, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );

            return $this->render('employe/consulteremploye.html.twig', [
            'employes' => $employespages,
            'form'=>$form->createView(),
             'employeparnometprenom'  => $employessearchpages

        ]);
    }
    #[Route('/mettreajouremploye/{id}', name: 'mettreajouremploye')]

public function mettreajour(string $id, Request $request,EntityManagerInterface $entityManager,ManagerRegistry $doctrine) :Response
{
    $this->denyAccessUnlessGranted('ROLE_USER');

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
       return $this->redirectToRoute('consulteremp');
    }
    return $this->renderForm('employe/modifieremploye.html.twig', [
        'form' => $form,
    ]);
}
    #[Route('/supprimeremploye/{id}', name: 'supprimeremploye')]
public function supprimer (string $id, Request $request,ManagerRegistry $doctrine,EntityManagerInterface $entityManager)
{
    $this->denyAccessUnlessGranted('ROLE_USER');

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



    #[Route('/consulteremp', name: 'consulteremp')]

    public function consulteremployer(ManagerRegistry $doctrine
    )
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $rep = $doctrine->getRepository(Employe::class);
        $user=$this->getUser();
        $employe=$rep->findOneBy(['login'=>$user]);

        return $this->render('employe/consulteremployespecifiique.html.twig', [
            'employe' => $employe,
        ]);
    }

    #[Route('/recherchersalairesup/{salaire}', name: 'recherchersalairesup')]
public function recherchersalairesup (string $salaire,EmployeRepository $repository)
{
    $employes=$repository->findAllGreaterThanSalaire($salaire);
    return $employes;
}
    #[Route('/rechercherparnomprenom/{nom}/{prenom}', name: 'rechercherparnomprenom')]
    public function rechercherparnomprenom (string $nom,string $prenom ,EmployeRepository $repository)
    {
    $employes=$repository->findBy(['nom'=>$nom,'prenom'=>$prenom]);
    return $employes;
    }
    #[Route('/trieremploye/{critere}/{sens}', name: 'trieremploye')]
    public function trier(Request $request, ManagerRegistry $doctrine,EmployeRepository $repository, PaginatorInterface $paginator,string $critere,string $sens)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $rep=$doctrine->getRepository(Employe::class);

              $employes= $rep->findBy(array(),array($critere=>$sens));


        $employespages = $paginator->paginate(
            $employes, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );

        return $this->render('employe/consulteremploye.html.twig', [
            'employes' => $employespages,
        ]);
    }


    /**
     * Lists all Post entities.
     *
     * @param Request $request
     *
     * @Route("/consulteremployedatatable", name="consulteremployedatatable")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction(Request $request, DatatableFactory $datatableFactory, DatatableResponse $datatableResponse)
    {
        $isAjax = $request->isXmlHttpRequest();

        // Get your Datatable ...
        //$datatable = $this->get('app.datatable.post');
        //$datatable->buildDatatable();

        // or use the DatatableFactory
        /**
         * @var DatatableInterface $datatable
         */
        $datatable = $datatableFactory->create(EmployeDataTable::class);
        $datatable->buildDatatable();
        dump($isAjax);
        if ($isAjax) {
            $responseService = $datatableResponse;
            $responseService->setDatatable($datatable);
            $responseService->getDatatableQueryBuilder();

            return $responseService->getResponse();
        }

        return $this->render('Employe/consulteremployedatatable.html.twig', array(
            'datatable' => $datatable,
        ));
    }

}

