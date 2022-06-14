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
           ->addArgument('idemploye',InputArgument::OPTIONAL,'id de employe pourlequel on va faire le calcul')
;
        $this->setHelp('passer comme parametre a cette commande annee mois idemploye  cette commande te permet enregistrer dans la base de donnée le nombre de jour de conges pris et restant par un employe donné pendant un mois de annee donnée');

        ;
    }


    public function calculernbjour( SuiviConge $suiviconge , EntityManagerInterface $entityManager)

    {

        $tabconge=$suiviconge->getEmploye()->getConge();

        if ($suiviconge->getNbjourpris()==0)

        {foreach ($tabconge as $clef2=>$value2)

        {

            $dateconge=$value2->getDateDebut();



            if (($dateconge->format('m')==$suiviconge->getMois()) and($dateconge->format('Y')==$suiviconge->getAnnee())) {

                $nbjourprisparconge = $value2->calculerNbjourpourcommande($value2->getId(), $entityManager);

                $suiviconge->setNbjourpris($suiviconge->getNbjourpris()+$nbjourprisparconge);

            }

        }



            $suiviconge->setNbjourRestant($suiviconge->getQuota()-$suiviconge->getNbjourpris());



        }



    }



    protected function execute(InputInterface $input, OutputInterface $output)

    {

        $io = new SymfonyStyle($input, $output);

        $io->success('calcul réalisé avec success voir base donnée.');
        $idemploye=$input->getArgument('idemploye');
        if ($idemploye){
            $rep = $this->entityManager->getRepository(Employe::class);

            $emp = $rep->find($idemploye);


                $tabcontrat = $emp->getContrat();


                foreach ($tabcontrat as $clef => $value) {

                    $yeardebut = $value->getDatedebut()->format('Y');

                    $moisdebut = $value->getDatedebut()->format('m');

                    $yearfin = $value->getDatefin()->format('Y');

                    $moisfin = $value->getDatefin()->format('m');

                    $moisiteration = $moisdebut;


                    for ($i = $yeardebut; $i <= $yearfin; $i++) {


                        for ($moisiteration = $moisdebut; $moisiteration < 13; $moisiteration++) {

                            $rep = $this->entityManager->getRepository(SuiviConge::class);

                            $suivicongetrouve = $rep->findOneBy(['annee' => $i, 'mois' => $moisiteration, 'employe' => $emp, 'contrat' => $value]);


                            if ($suivicongetrouve == null) {
                                $suiviconge = new SuiviConge ();

                                $suiviconge->setEmploye($emp);

                                $suiviconge->setContrat($value);

                                $suiviconge->setQuota($value->getQuotaparmoisaccorde());

                                $suiviconge->setNbjourpris(0);

                                $suiviconge->setMois($moisiteration);

                                $suiviconge->setAnnee($i);

                                $suiviconge->setNbjourrestant($suiviconge->getQuota());

                                $this->calculernbjour($suiviconge, $this->entityManager);
                                $this->entityManager->persist($suiviconge);
                                $this->entityManager->flush();
                            }


                        }


                    }

                }
            }




        else {
            $rep = $this->entityManager->getRepository(Employe::class);

            $emplist = $rep->findAll();

            foreach ($emplist as $cleemp => $valueemp) {

                $tabcontrat = $valueemp->getContrat();


                foreach ($tabcontrat as $clef => $value) {

                    $yeardebut = $value->getDatedebut()->format('Y');

                    $moisdebut = $value->getDatedebut()->format('m');

                    $yearfin = $value->getDatefin()->format('Y');

                    $moisfin = $value->getDatefin()->format('m');

                    $moisiteration = $moisdebut;


                    for ($i = $yeardebut; $i <= $yearfin; $i++) {


                        for ($moisiteration = $moisdebut; $moisiteration < 13; $moisiteration++) {
                            $rep = $this->entityManager->getRepository(SuiviConge::class);

                            $suivicongetrouve = $rep->findOneBy(['annee' => $i, 'mois' => $moisiteration, 'employe' => $valueemp, 'contrat' => $value]);



                            if ($suivicongetrouve == null) {
                                $suiviconge = new SuiviConge ();

                                $suiviconge->setEmploye($valueemp);

                                $suiviconge->setContrat($value);

                                $suiviconge->setQuota($value->getQuotaparmoisaccorde());

                                $suiviconge->setNbjourpris(0);

                                $suiviconge->setMois($moisiteration);

                                $suiviconge->setAnnee($i);

                                $suiviconge->setNbjourrestant($suiviconge->getQuota());



                                $this->calculernbjour($suiviconge, $this->entityManager);

                                $this->entityManager->persist($suiviconge);
                                $this->entityManager->flush();
                            }


                        }


                    }

                }
            }

        }


        return Command::SUCCESS;

    }



}
