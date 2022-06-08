<?php

namespace App\Controller;

use phpDocumentor\Reflection\Types\Array_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]

    public function index(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'error' => $error,
        ]);

    }
    #[Route('/logout', name: 'logout')]
    public function logout()
    {
        return $this->render('base.html.twig');
    }

public function index2(AuthenticationUtils $authenticationUtils,string $nomuser, string $password)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user

        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $nomuser,
            'password'=>$password,
            'error' => $error,
        ]);
    }
}
