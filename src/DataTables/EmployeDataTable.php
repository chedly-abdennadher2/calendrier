<?php


namespace App\DataTables;

use App\Entity\Conge;
use App\Entity\Contrat;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\BooleanColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Column\MultiselectColumn;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;
use Sg\DatatablesBundle\Datatable\Editable\SelectEditable;
use Sg\DatatablesBundle\Datatable\Editable\TextEditable;
use Sg\DatatablesBundle\Datatable\Filter\DateRangeFilter;
use Sg\DatatablesBundle\Datatable\Filter\NumberFilter;
use Sg\DatatablesBundle\Datatable\Filter\SelectFilter;
use Sg\DatatablesBundle\Datatable\Filter\TextFilter;
use Sg\DatatablesBundle\Datatable\Style;

class EmployeDataTable extends AbstractDatatable
{
/*    public function getLineFormatter()
    {
        $formatter = function($row) {
            $row['datedebut']=null;
            $row['datefin']=null;
            $row['datearret']=null;

            $row['id']=null;
            $row['typedecontrat']=null;
            $row['quotaparmoisaccorde']=null;
            $row['quotarestant']=null;

            $doctrine=$this->getEntityManager();
            $repository=$doctrine->getRepository(Employe::class);
            $employes=$repository->findAll();

            foreach ($this->contrats as $clef2=>$value2)
            {
                $row['id']=$row['id'].$value2->getId();
                $row['id']=$row['id']."<br>";

                $row['datedebut']=$row['datedebut']. date_format($value2->getDatedebut(),'d/m/Y');
                $row['datedebut']=$row['datedebut']."<br>";
                $row['datefin']=$row['datefin']. date_format($value2->getDatefin(),'d/m/Y');
                $row['datefin']=$row['datefin']."<br>";
                $row['typedecontrat']=$row['typedecontrat'].$value2->getTypedecontrat();
                $row['typedecontrat']=$row['typedecontrat']."<br>";
                $row['quotaparmoisaccorde']=$row['quotaparmoisaccorde'].$value2->getQuotaparmoisaccorde();
                $row['quotaparmoisaccorde']=$row['quotaparmoisaccorde']."<br>";
                $row['quotarestant']=$row['quotarestant'].$value2->getQuotarestant();
                $row['quotarestant']=$row['quotarestant']."<br>";

            }



            return $row;
        };

        return $formatter;
    }

*/
    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {

        $doctrine=$this->getEntityManager();
        $repository=$doctrine->getRepository(Contrat::class);


        $this->ajax->set(array(
            // send some extra example data
            'data' => array('data1' => 1, 'data2' => 2),
            // cache for 10 pages
            'pipeline' => 10
        ));

        $this->options->set(array(
            'classes' => Style::BOOTSTRAP_3_STYLE,
            'stripe_classes' => [ 'strip1', 'strip2', 'strip3' ],
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'order' => array(array(0, 'asc')),
            'order_cells_top' => true,
            //'global_search_type' => 'gt',
            'search_in_non_visible_columns' => true,
        ));

        $this->columnBuilder
            ->add('id', Column::class, array(
                'title' => 'Id',
                'searchable' => true,
                'orderable' => true,
                'filter' => array(NumberFilter::class, array(
                    'classes' => 'test1 test2',
                    'search_type' => 'eq',
                    'cancel_button' => true,
                    'type' => 'number',
                    'show_label' => true,
                    'datalist' => array('3', '50', '75')
                )),
            ))

            ->add('nom', Column::class, array(
                'title' => 'nom',
                'searchable' => true,
                'orderable' => true,
                'filter' => array(NumberFilter::class, array(
                    'classes' => 'test1 test2',
                    'search_type' => 'eq',
                    'cancel_button' => true,
                    'type' => 'number',
                    'show_label' => true,
                    'datalist' => array('3', '50', '75')
                )),
            ))

            ->add('prenom', Column::class, array(
                'title' => 'prenom',
                'searchable' => true,
                'orderable' => true,
                'filter' => array(NumberFilter::class, array(
                    'classes' => 'test1 test2',
                    'search_type' => 'eq',
                    'cancel_button' => true,
                    'type' => 'number',
                    'show_label' => true,
                    'datalist' => array('3', '50', '75')
                )),
            ))

            ->add('salaire', Column::class, array(
                'title' => 'salaire',
                'searchable' => true,
                'orderable' => true,
                'filter' => array(NumberFilter::class, array(
                    'classes' => 'test1 test2',
                    'search_type' => 'eq',
                    'cancel_button' => true,
                    'type' => 'number',
                    'show_label' => true,
                    'datalist' => array('3', '50', '75')
                )),
            ))
            ->add('quota', Column::class, array(
                'title' => 'quota',
                'searchable' => true,
                'orderable' => true,
                'filter' => array(NumberFilter::class, array(
                    'classes' => 'test1 test2',
                    'search_type' => 'eq',
                    'cancel_button' => true,
                    'type' => 'number',
                    'show_label' => true,
                    'datalist' => array('3', '50', '75')
                )),
            ))
            ->add('nomutilisateur', Column::class, array(
                'title' => 'nomutilisateur',
                'searchable' => true,
                'orderable' => true,
                'filter' => array(NumberFilter::class, array(
                    'classes' => 'test1 test2',
                    'search_type' => 'eq',
                    'cancel_button' => true,
                    'type' => 'number',
                    'show_label' => true,
                    'datalist' => array('3', '50', '75')
                )),
            ))
            ->add('isLeaving', Column::class, array(
                'title' => 'isLeaving',
                'searchable' => true,
                'orderable' => false,
                'filter' => array(NumberFilter::class, array(
                    'classes' => 'test1 test2',
                    'search_type' => 'eq',
                    'cancel_button' => true,
                    'type' => 'number',
                    'show_label' => true,
                    'datalist' => array('3', '50', '75')
                )),
            ))
            ->add(null, ActionColumn::class, array(
                'title' => 'Actions',
                'start_html' => '<div class="start_actions">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'mettreajouremploye',
                        'route_parameters' => array(
                            'id' => 'id',
                        ),
                        'icon' => 'glyphicon glyphicon-eye-open',
                        'label' => 'mettre à jour ',
                        'confirm' => true,
                        'confirm_message' => 'Are you sure?',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'Show',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button',
                        ),

                        'start_html' => '<div class="start_show_action">',
                        'end_html' => '</div>',
                    ),
                    array(
                        'icon' => 'glyphicon glyphicon-star',
                        'label' => 'mettreajour',
                        'confirm' => false,
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'Show',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button',
                        ),
                        'button' => true,
                        'button_value' => 'id',
                        'button_value_prefix' => true,
                        'render_if' => function ($row) {
                            return $this->authorizationChecker->isGranted('ROLE_ADMIN');
                        },
                        'start_html' => '<div class="start_show_action">',
                        'end_html' => '</div>',
                    ),
                ),
            ))

            ->add(null, ActionColumn::class, array(
                'title' => 'Actions',
                'start_html' => '<div class="start_actions">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'supprimeremploye',
                        'route_parameters' => array(
                            'id' => 'id',

                        ),
                        'icon' => 'glyphicon glyphicon-eye-open',
                        'label' => 'supprimer',
                        'confirm' => true,
                        'confirm_message' => 'Are you sure?',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'supprimer',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button',
                        ),
                        'start_html' => '<div class="start_show_action">',
                        'end_html' => '</div>',
                    ),
                    array(
                        'icon' => 'glyphicon glyphicon-star',
                        'label' => 'supprimer',
                        'confirm' => false,
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'Show',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button',
                        ),
                        'button' => true,
                        'button_value' => 'id',
                        'button_value_prefix' => true,
                        'render_if' => function ($row) {
                            return $this->authorizationChecker->isGranted('ROLE_ADMIN');
                        },
                        'start_html' => '<div class="start_show_action">',
                        'end_html' => '</div>',
                    ),
                ),
            ))
//            ->add ('datedebut',VirtualColumn::class,['title'=>'datedebut'])



         ;





    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'App\Entity\User';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'EmployeDataTable';
    }

}