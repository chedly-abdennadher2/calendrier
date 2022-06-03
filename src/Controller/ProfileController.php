<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Finder\contains;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();

        // Call whatever methods you've added to your User class
        // For example, if you added a getFirstName() method, you can use that.

       return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
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
