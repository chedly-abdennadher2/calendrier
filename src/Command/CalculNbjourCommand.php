<?php

namespace App\Command;

use App\Entity\User;
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


    public function calculernbjour( SuiviConge $suiviconge,EntityManagerInterface $entityManager)

    {

        $tabconge=$suiviconge->getUser()->getConge();

        if ($suiviconge->getNbjourpris()==0)

        {foreach ($tabconge as $clef2=>$value2)

        {

            $dateconge=$value2->getDateDebut();



            if (($dateconge->format('m')==$suiviconge->getMois()) and($dateconge->format('Y')==$suiviconge->getAnnee())) {

                $nbjourprisparconge = $value2->calculerNbjourpourcommande();

                $suiviconge->setNbjourpris($suiviconge->getNbjourpris()+$nbjourprisparconge);

            }

        }

           $suiviconge->setNbjourRestant($suiviconge->getQuota() - $suiviconge->getNbjourpris());



        }



    }



    protected function execute(InputInterface $input, OutputInterface $output)

    {
        $dateactuel = date('d-m-Y');
        $moisactuel=substr ($dateactuel,3,2);
        $anneeactuel=substr ($dateactuel,6,4);

        $io = new SymfonyStyle($input, $output);

        $io->success('calcul réalisé avec success voir base donnée.');
        $idemploye=$input->getArgument('idemploye');
        if ($idemploye){
            $rep = $this->entityManager->getRepository(User::class);

            $emp = $rep->find($idemploye);


                $tabcontrat = $emp->getContrat();


                foreach ($tabcontrat as $clef => $value) {

                    $yeardebut = $value->getDatedebut()->format('Y');
                    $moisdebut = $value->getDatedebut()->format('m');

                    $yearfin = $value->getDatefin()->format('Y');
                    $moisfin = $value->getDatefin()->format('m');
                    $rep = $this->entityManager->getRepository(SuiviConge::class);
                    $suivicongetrouveavant=$rep->FindByMoisAnneerecent($emp,$value);
                    if ($suivicongetrouveavant!=null)
                    {
                        $yeardebut=$suivicongetrouveavant[0]->getAnnee();
                        $moisdebut=$suivicongetrouveavant[0]->getMois();
                    }
                    $nbjourrestantaddition=0;
                    $nbjourprissoustraction=0;
                    $nbinstance=0;
                    for ($i = $yeardebut; (($i <= $anneeactuel) and ($i<=$yearfin)); $i++) {

                        for ($moisiteration = $moisdebut; $moisiteration < 13; $moisiteration++) {

                            if (($i==$anneeactuel) and ($moisiteration==$moisactuel))
                            {
                                break;

                            }
                            if (($i==$yearfin) and ($moisiteration==$moisfin))
                            {
                                break;
                            }

                            $rep = $this->entityManager->getRepository(SuiviConge::class);

                            $suivicongetrouve = $rep->findOneBy(['annee' => $i, 'mois' => $moisiteration, 'user' => $emp, 'contrat' => $value]);

                            if ($suivicongetrouve == null) {
                                $suivicongetrouve = new SuiviConge ();

                                $suivicongetrouve->setUser($emp);

                                $suivicongetrouve->setContrat($value);

                                $suivicongetrouve->setQuota($value->getQuotaparmoisaccorde());

                                $suivicongetrouve->setNbjourpris(0);

                                $suivicongetrouve->setMois($moisiteration);

                                $suivicongetrouve->setAnnee($i);
                                $suivicongetrouve->setNbjourrestant($suivicongetrouve->getQuota());
                                $this->calculernbjour($suivicongetrouve,$this->entityManager);
                                $this->entityManager->persist($suivicongetrouve);
                                $this->entityManager->flush();

                            }

                                if ($moisiteration-1<=0)
                                {
                                    $suivicongetrouvemoisprecedent = $rep->findOneBy(['annee' => $i-1, 'mois' => 12, 'user' => $emp, 'contrat' => $value]);
                                }
                                else
                                    {$suivicongetrouvemoisprecedent = $rep->findOneBy(['annee' => $i, 'mois' => $moisiteration-1, 'user' => $emp, 'contrat' => $value]);}

                                if ($suivicongetrouvemoisprecedent!=null){
                                $nbjourrestantaddition=$suivicongetrouvemoisprecedent->getNbjourrestant();
                                }

                        if ($nbinstance!=0)
                        {
                            $suivicongetrouve->setNbjourrestant($suivicongetrouve->getQuota()+$nbjourrestantaddition);
                            $suivicongetrouve->setNbjourrestant($suivicongetrouve->getNbjourrestant()-$suivicongetrouve->getNbjourpris());

                            $this->entityManager->persist($suivicongetrouve);
                            $this->entityManager->flush();

                        }
                            $nbinstance++;




                        }


                    }

                }
            }






        else {
            $rep = $this->entityManager->getRepository(User::class);

            $emplist = $rep->findAll();

            foreach ($emplist as $cleemp => $valueemp) {

                $tabcontrat = $valueemp->getContrat();


                foreach ($tabcontrat as $clef => $value) {

                    $yeardebut = $value->getDatedebut()->format('Y');

                    $moisdebut = $value->getDatedebut()->format('m');

                    $yearfin = $value->getDatefin()->format('Y');

                    $moisfin = $value->getDatefin()->format('m');
                    $rep = $this->entityManager->getRepository(SuiviConge::class);
                    $suivicongetrouveavant=$rep->FindByMoisAnneerecent($valueemp,$value);
                    if ($suivicongetrouveavant!=null)
                    {
                        $yeardebut=$suivicongetrouveavant[0]->getAnnee();
                        $moisdebut=$suivicongetrouveavant[0]->getMois();
                    }
                    $nbjourrestantaddition=0;
                    $nbjourprissoustraction=0;
                    $nbinstance=0;


                    for ($i = $yeardebut; (($i <= $anneeactuel) and ($i<=$yearfin)); $i++) {


                        for ($moisiteration = $moisdebut; $moisiteration < 13; $moisiteration++) {

                            if (($i==$anneeactuel) and ($moisiteration==$moisactuel))
                            {
                                break;
                            }
                            if (($i==$yearfin) and ($moisiteration==$moisfin))
                            {
                               break;
                            }
                            $rep = $this->entityManager->getRepository(SuiviConge::class);

                            $suivicongetrouve = $rep->findOneBy(['annee' => $i, 'mois' => $moisiteration, 'user' => $valueemp, 'contrat' => $value]);



                            if ($suivicongetrouve == null) {
                                $suivicongetrouve = new SuiviConge ();

                                $suivicongetrouve->setUser($valueemp);

                                $suivicongetrouve->setContrat($value);

                                $suivicongetrouve->setQuota($value->getQuotaparmoisaccorde());

                                $suivicongetrouve->setNbjourpris(0);

                                $suivicongetrouve->setMois($moisiteration);

                                $suivicongetrouve->setAnnee($i);

                                $suivicongetrouve->setNbjourrestant($suivicongetrouve->getQuota());



                                $this->calculernbjour($suivicongetrouve,$this->entityManager);

                                $this->entityManager->persist($suivicongetrouve);
                                $this->entityManager->flush();

                            }

                            if ($moisiteration-1<=0)
                            {
                                $suivicongetrouvemoisprecedent = $rep->findOneBy(['annee' => $i-1, 'mois' => 12, 'user' => $valueemp, 'contrat' => $value]);
                            }
                            else
                            {$suivicongetrouvemoisprecedent = $rep->findOneBy(['annee' => $i, 'mois' => $moisiteration-1, 'user' => $valueemp, 'contrat' => $value]);}

                            if ($suivicongetrouvemoisprecedent!=null){
                                $nbjourrestantaddition=$suivicongetrouvemoisprecedent->getNbjourrestant();
                            }

                            if ($nbinstance!=0)
                            {
                                $suivicongetrouve->setNbjourrestant($suivicongetrouve->getQuota()+$nbjourrestantaddition);
                                $suivicongetrouve->setNbjourrestant($suivicongetrouve->getNbjourrestant()-$suivicongetrouve->getNbjourpris());

                                $this->entityManager->persist($suivicongetrouve);
                                $this->entityManager->flush();

                            }
                            $nbinstance++;

                        }

                    }

                }
            }

        }


        return Command::SUCCESS;

    }



}
