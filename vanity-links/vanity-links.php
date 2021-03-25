<?php namespace toolbox;
class vanity_links_controller {

    /**
     * Applies to this controller and all child controllers
     */
    static function passThru(){

        //Set the active menu item
        sidebarV2::get('top_nav')
            ->setActive('sales')
            ->getLink('sales')
            ->setActive('vanity-links');

    }

    function __construct(){

        title::get()
            ->setSubtitle('Vanity Links')
            ->addCrumb('Vanity Links');

        //widget for the datatable
        widgetHelper::createStandard('Vanity Links')
            ->setHook('content')//add to full widget content hook in the main template
            //add datatable that lists existing vanity links
            ->add(datatableV2::createStandardFromSQL('SELECT * FROM `vanity_links`', false)
                //create button for adding new links
                ->addView(function($dt){ ?>
                    <div class="datatable-info datatable-section">
                        <a data-overlay-id="add_vanity_link" data-ajax_caching="false" data-max_width="900px"
                           href="/admin/vanity-links/edit?widget_unique_id=vanity_links_form" data-closeonfadeclick="false"
                           data-close_click="#<?php
                           echo $dt->datatable_id; ?> > .datatable > .link"
                           class="btn btn-primary btn-small">
                            Add New Vanity Link
                        </a>
                    </div>
                <?php }, 'footer')
                ->defineCol('link_url', 'URL')
                ->defineCol('redirect_to', 'Redirect To')
                ->defineCol('date_added', 'Date Added', function($val, $cols){
                    return date('M d, Y h:ia', strtotime($val));
                }, array('sort' => 'desc', 'daterange' => true))
                ->defineCol('vanity_link_id', 'Action', function($vanity_link_id, $cols, $dt) { ?>
                    <a data-overlay-id="edit_vanity_link" data-max_width="600px"
                       data-close_click="#<?php echo $dt->datatable_id; ?> &gt; .datatable &gt; .link"
                       href="/admin/vanity-links/edit/<?php echo $vanity_link_id; ?>?widget_unique_id=vanity_links_form"
                       class="btn btn-silver btn-small dropdown-select">
                        Edit
                    </a>
                    <div data-dropdown="true" data-dropdown-fixed="true" class="dropdown dropbtn pull-right">
                            <span class="controls btn btn-silver btn-small">
                                <span><i class="icon-down-open open"></i></span>
                            </span>
                        <div style="min-width: 200px;" data-dropdown-contents="true" class="dropdown-contents">
                            <a data-overlay-id="edit_vanity_link" data-max_width="600px"
                                data-open_confirmation="Delete this vanity link?"
                                data-close_click="#<?php echo $dt->datatable_id; ?> &gt; .datatable &gt; .link"
                                href="/admin/vanity-links/edit/<?php echo $vanity_link_id; ?>?action=delete"
                                class="btn btn-red btn-small dropdown-select">
                                    Delete
                            </a>
                        </div>
                    </div>
                <?php }, array('search' => false,'sort' => false)),
                'widget-reload.php', 'vanity_links_datatable');

    }

}