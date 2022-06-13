<?php

namespace App\Command;

use App\Entity\Employe;
use App\Entity\SuiviConge;
use App\Repository\SuiviCongeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'calculnbjour',
    description: 'calculer le nombre de jour pris par un employé dans un mois et annee donnée',
)]
class CalculNbjourCommand extends Command
{
    private $entityManager;
    public function __construct(entityManagerInterface $entityManager)
    { $this->entityManager=$entityManager;
        parent::__construct();
        $this->setHelp('passer comme parametre a cette commande annee mois idemploye  cette commande te permet enregistrer dans la base de donnée le nombre de jour de conges pris et restant par un employe donné pendant un mois de annee donnée');

    }

    protected function configure(): void
    {
        $this
            ->addArgument('mois', InputArgument::REQUIRED, 'mois pendant lequel on va calculer le nombre de jour pris')
            ->addArgument ('annee',InputArgument::REQUIRED,'annee pendant laquelle on va calculer le nombre de jour pris')
            ->addArgument('idemploye',InputArgument::REQUIRED,'id de employe pourlequel on va faire le calcul')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $mois = $input->getArgument('mois');
        $annee=$input->getArgument('annee');
        $idemploye=$input->getArgument('idemploye');
        $rep=$this->entityManager->getRepository(Employe::class);
        $emp=$rep->find($idemploye);
        if ($emp!=null) {
            $rep = $this->entityManager->getRepository(SuiviConge::class);
            $suivi_conges = $rep->findBy(['employe' => $emp]);
            if ($suivi_conges!=null)
            {
                foreach ($suivi_conges as $cle=>$value)
                {
                    $tabconge=$value->getEmploye()->getConge();
                    if ($value->getNbjourpris()==0)
                    {foreach ($tabconge as $clef2=>$value2)
                    {
                        $dateconge=$value2->getDateDebut();

                        if (($dateconge->format('m')==$mois) and($dateconge->format('Y')==$annee)) {
                            $nbjourprisparconge = $value2->calculerNbjour($value2->getId(), $this->entityManager);
                            $value->setNbjourpris($value->getNbjourpris()+$nbjourprisparconge);
                        }
                    }
                    }
                    else
                    {
                        $io->success('calcul déja effectué auparavant.');

                        return Command::INVALID;

                    }
                    $value->setNbjourRestant($value->getQuota()-$value->getNbjourpris());

                    $this->entityManager->persist($value);
                    $this->entityManager->flush();
                }

            }

        }

$io->success('calcul réalisé avec success voir base donnée.');

        return Command::SUCCESS;
    }

}
