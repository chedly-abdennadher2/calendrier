<?php

namespace App\Command;

use App\Entity\Employe;
use App\Entity\SuiviConge;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'calcul_nbjour',
    description: 'calculer le nombre de jour pris par un employé dans un mois et annee donnée'
)]
class CalculNbjourCommand extends Command
{
    private $entityManager;
    public function __construct(entityManagerInterface $entityManager)
    { $this->entityManager=$entityManager;
        parent::__construct();

    }

    protected function configure()
    {
        $this
            ->addArgument('idemploye',InputArgument::REQUIRED,'id de employe pourlequel on va faire le calcul')
;
        $this->setHelp('passer comme parametre a cette commande annee mois idemploye  cette commande te permet enregistrer dans la base de donnée le nombre de jour de conges pris et restant par un employe donné pendant un mois de annee donnée');

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $idemp=$input->getArgument('idemploye');
        $io->success('calcul réalisé avec success voir base donnée.');
        $rep=$this->entityManager->getRepository(Employe::class);
        $emp=$rep->find ($idemp);
        $tabcontrat =$emp->getContrat();
        foreach ($tabcontrat as $clef=>$value)
        {

        }

        return Command::SUCCESS;
    }

}
