<?php

namespace App\Controller;

use App\Form\EmployeformType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Finder\contains;
use App\Entity\Employe;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request,EntityManagerInterface $entityManager) :Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();

        // Call whatever methods you've added to your User class
        // For example, if you added a getFirstName() method, you can use that.
    $emp =new Employe();
    $form = $this->createForm(EmployeformType::class,$emp);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        $emp= $form->getData();
        $entityManager->persist($emp);
        $entityManager->flush();

        }
    return $this->renderForm('profile/index.html.twig', [
     'form' => $form,
 ]);

    }

    /**
     * Returns an array of service types required by such instances, optionally keyed by the service names used internally.
     *
     * For mandatory dependencies:
     *
     *  * ['logger' => 'Psr\Log\LoggerInterface'] means the objects use the "logger" name
     *    internally to fetch a service which must implement Psr\Log\LoggerInterface.
     *  * ['loggers' => 'Psr\Log\LoggerInterface[]'] means the objects use the "loggers" name
     *    internally to fetch an iterable of Psr\Log\LoggerInterface instances.
     *  * ['Psr\Log\LoggerInterface'] is a shortcut for
     *  * ['Psr\Log\LoggerInterface' => 'Psr\Log\LoggerInterface']
     *
     * otherwise:
     *
     *  * ['logger' => '?Psr\Log\LoggerInterface'] denotes an optional dependency
     *  * ['loggers' => '?Psr\Log\LoggerInterface[]'] denotes an optional iterable dependency
     *  * ['?Psr\Log\LoggerInterface'] is a shortcut for
     *  * ['Psr\Log\LoggerInterface' => '?Psr\Log\LoggerInterface']
     *
     * @return string[] The required service types, optionally keyed by service names
     */

}
