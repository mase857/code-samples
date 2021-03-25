<?php
namespace toolbox;
class external_products_controller {



    function __construct(){


		sidebarV2::get('top_nav')->setActive('report', 'report');

        title::get()->setSubtitleDisabled();

        widgetHelper::createStandard('Report')
            ->setHook('content')
            ->add(datatableV2::createStandardFromSQL('
            SELECT external_products.*
            FROM external_products
            LEFT JOIN inventory_items ON external_products.item_id = inventory_items.item_id

                ', false)
                ->setLimit(20)
                ->defineCol('provider', 'Provider', function($val, $col) {
                    return utils::htmlEncode($val);
                })
                ->defineCol('product_name', 'Product Name', function($val, $col) {
                    return utils::htmlEncode($val);
                })
                ->defineCol('thumbnail_url', 'Image URL', function($val, $col) {
                    return utils::htmlEncode($val);
                })
                ->defineCol('date_created', 'Date Created', function($val){
                    return utils::htmlEncode($val);
                }, array('sort'=>'desc'))
                ->defineCol('external_product_id', 'Action', function($external_product_id, $cols, $dt) {
                   $item_id = $cols->item_id;

                   if(!isset($item_id)) {
                    ?>
                    <a data-overlay-id="edit_swag_link" data-max_width="600px" data-link ="<?php echo $cols->thumbnail_url;?>"
                        data-close_click="#<?php echo $dt->datatable_id; ?> &gt; .datatable &gt; .link"
                        href="/admin/shipments/inventory/add/?sw_id=<?php echo $external_product_id;?>"
                        class="btn btn-silver btn-small dropdown-select" id="<?php echo $external_product_id; ?>">
                        Create WG Inventory
                        </a>

                    <?php } else {?>

                    <a data-overlay-id="edit_swag_link" data-max_width="600px" data-link ="<?php echo $cols->thumbnail_url;?>"
                       data-close_click="#<?php echo $dt->datatable_id; ?> &gt; .datatable &gt; .link"
                       href="/admin/shipments/inventory/add/<?php echo $item_id; ?>"
                       class="btn btn-silver btn-small dropdown-select swag_item" id="<?php echo $external_product_id; ?>">
                        View WG Inventory
                    </a>

                <?php } }, array('search' => false,'sort' => false))

            );


    }


}

?>