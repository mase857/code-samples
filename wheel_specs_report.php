<?php
namespace toolbox;
class wheel_specs_report_controller {



    function __construct(){

		sidebarV2::get('top_nav')->getLink('wheel')->setActive('specs_report');
        title::get()->addCrumb('Specs Report');
        appUtils::useDB('reporting', 'default');

        $datatable = datatableV2::createStandardFromSQL('
            SELECT s.category_id, i.category_name, v.spec_id, s.spec_name, c.spec_category, v.spec_value,sum(w.times_chosen) as `times_chosen`,w.spin_date
            FROM wheel_specs_report w
            LEFT JOIN inventory_spec_values v ON w.spec_value_id = v.spec_value_id
            LEFT JOIN inventory_spec_categories c ON v.spec_category_id = c.spec_category_id
            LEFT JOIN inventory_specs s ON v.spec_id = s.spec_id
            LEFT JOIN inventory_categories i ON s.category_id = i.category_id
            GROUP BY w.spec_value_id', false);

        $datatable->setLimit(20)
            ->defineCol('category_name', 'Product Category', function($val){
                return $val;
            })
            ->defineCol('spec_name', 'Spec Name', function($val) {
                return $val;
            })
            ->defineCol('spec_category', 'Spec Group', function($val) {
                return $val;
            })
            ->defineCol('spec_value', 'Spec Value', function($val) {
                return $val;
            })
            ->defineCol('times_chosen', 'Times Chosen', function($val){
                return formatter::number_commas($val);
            }, array('sort'=>'desc', 'search' => false))
            ->defineCol('spin_date', 'Date Range', function($val, $columns, datatableV2 $datatable){
                $searched_data_range = $datatable->getSearch(5);
                if($searched_data_range == ''){
                    return 'All Time';
                }

                //format the daterange
                $range = explode(',',str_replace(' to ',',',$searched_data_range));
                return date('M. jS, Y', strtotime($range[0])). ' thru '.date('M. jS, Y', strtotime($range[0]));

            }, array('daterange' => true));


        //widget for containing the datatable
        widgetHelper::createStandard('Specs Chosen on The Wheel')
            ->setHook('content')
            ->add($datatable, 'widget-reload.php', 'specs_report');

    }

}