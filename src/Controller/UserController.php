<?php

namespace App\Controller;

use App\DataTables\EmployeAdminDataTable;
use App\DataTables\EmployeDataTable;
use App\Entity\Conge;
use App\Entity\User;
use App\Form\EmployeAdminformType;
use App\Form\EmployeformType;
use App\Form\EmployeupdateformType;
use App\Form\SuppressionType;
use App\Repository\EmployeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sg\DatatablesBundle\Datatable\DatatableInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

use function Symfony\Bundle\FrameworkBundle\Controller\redirectToRoute;
use function Symfony\Component\Finder\contains;

use Knp\Component\Pager\PaginatorInterface;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
class UserController extends AbstractController
{

    #[Route('/ajouteremploye', name: 'ajouteremploye')]
    public function ajouter(Request $request,EntityManagerInterface $entityManager,ManagerRegistry $doctrine,UserPasswordHasherInterface $userPasswordHasher ) :Response
    {

        // Call whatever methods you've added to your User class
        // For example, if you added a getFirstName() method, you can use that.
        $emp =new User();
        $form = $this->createForm(EmployeformType::class,$emp);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $emp= $form->getData();
            $password=($form->get('password')->getData());
            $emp->setPassword(
                $userPasswordHasher->hashPassword(
                    $emp,
                    $password
                )
            );
            $emp->setRoles(['ROLE_USER']);

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
        $emp=new User();
        $form = $this->createForm(EmployeAdminformType::class,$emp);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $loginemp=$form->get('loginemploye')->getData();
            $loginadmin=$form->get('loginadmin')->getData();
            $rep=$entityManager->getRepository(User::class);
            $useremp=$rep->findOneBy(['nomutilisateur'=>$loginemp]);
            $useradmin=$rep->findOneBy(['nomutilisateur'=>$loginadmin]);
            $useremp->setAdministrateur($useradmin);
            $useradmin->addUser($useremp);
            $entityManager->persist($useremp);
            $entityManager->persist($useradmin);

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
        $emp=new User();
        $form = $this->createForm(EmployeAdminformType::class,$emp);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $loginemp=$form->get('loginemploye')->getData();
            $loginadmin=$form->get('loginadmin')->getData();
            $rep=$entityManager->getRepository(User::class);
            $useremp=$rep->findOneBy(['nomutilisateur'=>$loginemp]);
            $useradmin=$rep->findOneBy(['nomutilisateur'=>$loginadmin]);
            $useremp->setAdministrateur($useradmin);
            $useradmin->addUser($useremp);

            $entityManager->persist($useremp);
            $entityManager->persist($useradmin);

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


    #[Route('/mettreajouremploye/{id}', name: 'mettreajouremploye')]

    public function mettreajour(string $id, Request $request,EntityManagerInterface $entityManager,ManagerRegistry $doctrine) :Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $rep=$doctrine->getRepository(User::class);
        $emp=$rep->find($id);

        $form = $this->createForm(EmployeupdateformType::class,$emp);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $emp= $form->getData();
            $entityManager->persist($emp);
            $entityManager->flush();
            return $this->redirectToRoute('consulterempdatatable');
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

        if (($form->isSubmitted() and $form->isValid())) {

            $rep=$doctrine->getRepository(User::class);
            $emp=$rep->find($id);
            $entityManager->remove($emp);
            $entityManager->flush();
            return $this->redirectToRoute('/');

        }
        else {
            $form->get('id')->setData($id);}

        return $this->renderForm('employe/supprimeremploye.html.twig', [
            'form' => $form,
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

    public function consulteremployedatatable(Request $request, DatatableFactory $datatableFactory, DatatableResponse $datatableResponse)
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
        $datatable = $datatableFactory->create(EmployeAdminDataTable::class);
        $datatable->buildDatatable();
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


    #[Route('/consulterempdatatable', name: 'consulterempdatatable')]

    public function consulteremployerspecifiquedatatable(Request $request, ManagerRegistry $doctrine, DatatableFactory $datatableFactory, DatatableResponse $datatableResponse
    )
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
        $datatable = $datatableFactory->create(EmployeDataTable::class);

        $datatable->buildDatatable(['user'=>$user]);
        if ($isAjax) {

            $datatableResponse->setDatatable($datatable);
            $datatableQueryBuilder = $datatableResponse->getDatatableQueryBuilder();
            $qb = $datatableQueryBuilder->getQb();
            $id = $user->getId();
            $qb->andWhere('user.id=:id');
            $qb->setParameter('id', $id);

            return $datatableResponse->getResponse();
        }

        return $this->render('Employe/consulteremployespecifiquedatatable.html.twig', array(
            'datatable' => $datatable,
        ));
    }

    public function deveniradmin (string $id,ManagerRegistry $doctrine,UserRepository $repository)
    {
        $rep=$doctrine->getRepository(User::class);
        $user=$rep->find($id);
        $rep=$doctrine->getRepository(User::class);
        $user=$rep->findOneBy(["nomutilisateur"=>$user->getNomutilisateur()]);
        if ($user !=null)
        {    $user->setRoles(['ROLE_ADMIN']);
            $repository->add($user,true);
        }

    }
}
?>