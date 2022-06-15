<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $rep=$entityManager->getRepository(Employe::class);
            $employe=$rep->FindOneBy(['nom'=>$user->getNomutilisateur()]);
           if ($employe!=null){
            $employe->setLogin($user);
            $entityManager->persist($employe);}
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('login2',['nomuser'=>$user->getNomutilisateur(),'password'=>$form->get('plainPassword')->getData()]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
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
