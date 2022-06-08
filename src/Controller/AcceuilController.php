<?php

namespace App\Controller;

use App\Entity\Administrateur;
use App\Repository\AdministrateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AcceuilController extends AbstractController
{
    #[Route('/', name: '/')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'AcceuilController',
        ]);
    }

    #[Route('/entree', name: '/entree')]

    public function entreer_partie_admin()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('baseadmin.html.twig');
    }
}
