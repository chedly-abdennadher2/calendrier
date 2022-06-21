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

class SuiviCongeAdminDataTable extends AbstractDatatable
{
    /**
     * {@inheritdoc}
     */
/*    public function getLineFormatter()
    {
        $formatter = function($row) {
            $row['test'] = 'Post from ' . $row['createdBy']['username'];

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
       // refaire fonction
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



            ->add('mois', Column::class, array(
                'title' => 'mois',
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
            ->add('annee', Column::class, array(
                'title' => 'année',
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
            ->add('nbjourpris', Column::class, array(
                'title' => 'nbjourpris',
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
            ->add('nbjourrestant', Column::class, array(
                'title' => 'nbjourrestant',
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

            ))
            ->add('employe.nom', Column::class, array(
                'title' => 'nom employe',
                'searchable' => true,
                'orderable' => true,

            ))
            ->add('employe.prenom', Column::class, array(
                'title' => 'prenom employe',
                'searchable' => true,
                'orderable' => true,

            ))


            ->add(null, ActionColumn::class, array(
                'title' => 'Actions',
                'start_html' => '<div class="start_actions">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'app_suivi_conge_edit',
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
                ),
            ))

            ->add(null, ActionColumn::class, array(
                'title' => 'Actions',
                'start_html' => '<div class="start_actions">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'app_suivi_conge_delete',
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

                ),
            ))


        ;





    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'App\Entity\SuiviConge';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'SuiviCongeAdminDataTable';
    }

}