<?php


namespace App\DataTables;

use App\Entity\Conge;
use App\Entity\Employe;
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

class CongeAdminDataTable extends AbstractDatatable
{
    /**
     * {@inheritdoc}
     */
private $row;
    public function getLineFormatter()
    {

        $formatter = function ($row) {
        $conge = $this->em->getRepository(Conge::class)->find($row['id']);

        $row['nbjour']=$conge->calculernbjour();

            return $row;
        };

        return $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {

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
            'order' => array(array(6, 'asc')),
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
            ->add('datedebut', DateTimeColumn::class, array(
                'title' => 'datedebut',
                'date_format' => 'L',
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

            ->add('datefin', DateTimeColumn::class, array(
                'title' => 'datefin',
                'date_format' => 'L',
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

            ->add('state', Column::class, array(
                'title' => 'state',
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
            ->add('nbjour', VirtualColumn::class, array(
                'title' => 'nbjour',
            ))

            ->add('typeconge', Column::class, array(
                'title' => 'typeconge',
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
            ->add('employe.id', Column::class, array(
                'title' => 'id employe',
                'searchable' => true,
                'orderable' => true,
                'default_content'=>'null'

            ))
            ->add('employe.nom', Column::class, array(
                'title' => 'nom employe',
                'searchable' => true,
                'orderable' => true,
                'default_content'=>'null'

            ))
            ->add('employe.prenom', Column::class, array(
                'title' => 'prenom employe',
                'searchable' => true,
                'orderable' => true,
                'default_content'=>'null'
            ))
             ->add('administrateur.id', Column::class, array(
                 'title' => 'id admin en conge',
                 'searchable' => true,
                 'orderable' => true,
                 'default_content'=>'null',
                 'sent_in_response'=>false,
                 

             ))
             ->add('administrateur.nom', Column::class, array(
                 'title' => 'nom admin en conge',
                 'searchable' => true,
                 'orderable' => true,
                 'default_content'=>'null'
             ))
             ->add('administrateur.prenom', Column::class, array(
                 'title' => 'prenom admin en conge',
                 'searchable' => true,
                 'orderable' => true,
                 'default_content'=>'null'
             ))

            ->add(null, ActionColumn::class, array(
                'title' => 'Actions',
                'start_html' => '<div class="start_actions">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'validercongeform',
                        'route_parameters' => array(
                            'id' => 'id',
                        ),
                        'icon' => 'glyphicon glyphicon-eye-open',
                        'label' => 'valider',
                        'confirm' => true,
                        'confirm_message' => 'Are you sure?',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'Show',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button',
                        ),
                        'render_if' =>function ($row)
                        {
                            return ($row['state']==='no check') ;
                        }
                    ,
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
                         'route' => 'mettreajourconge',
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
                         'render_if' =>function ($row)
                         {
                             return ($row['administrateur']!=null) ;
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
                         'route' => 'supprimerconge',
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
                         'render_if' =>function ($row)
                         {
                             return ($row['administrateur']!=null) ;
                         },

                         'start_html' => '<div class="start_show_action">',
                         'end_html' => '</div>',
                     ),

                 ),
             ))


        ;







    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'App\Entity\Conge';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'CongeAdminDataTable';
    }

}