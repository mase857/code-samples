<?php namespace toolbox;

class external_product {

    private $external_product_data;
    function __construct($external_product_id, $type = 'external_product_id') {

        if($type == 'external_product_id'){
            $this->external_product_data = db::prepare('SELECT *
				FROM `external_products`
				WHERE `external_product_id` = ?', array($external_product_id))
                ->fetchRow();
        }else if($type == 'item_id'){
            $this->external_product_data= db::prepare('SELECT *
				FROM `external_products`
				WHERE `item_id` = ?', array($external_product_id))
                ->fetchRow();
        }  else {
            throw new toolboxException('Invalid type for external product: '.$type);
        }

        if($this->external_product_data === null){
            throw new externalProduct404('External Product was not found with '.$type.' of: ' .$external_product_id);
        }

        external_product::$instances['external_product_id'][$this->external_product_data->external_product_id.''] = $this;

    }

    function setImageId($image_id){
        $this->setData('image_id', $image_id);
    }

    function getImageId($throw_e = false){
        return $this->getData('image_id', $throw_e);
    }

    function setImageUrl($image_url){
        $this->setData('thumbnail_url', $image_url);
    }

    function getImageUrl($throw_e = false){
        return $this->getData('thumbnail_url', $throw_e);
    }

    function setItemId($item_id){
        $this->setData('item_id', $item_id);
    }

    function getItemId($throw_e = false){
        return $this->getData('item_id', $throw_e);
    }

    function setProductName($product_name){
        $this->setData('product_name', $product_name);
    }

    function getProductName($throw_e = false){
        return $this->getData('product_name', $throw_e);
    }

    function setProductDescription($product_description){
        $this->setData('product_description', $product_description);
    }

    function getProductDescription($throw_e = false){
        return $this->getData('product_description', $throw_e);
    }

    protected function setData($field, $value = null){
        if(is_array($field)){
            $sql = 'update `external_products` set ';
            foreach($field as $key => $value){
                $sql .= '`'.$key.'` = '.db::quote($value).', ';
                $this->external_product_data->{$key} = $value;
            }
            $sql = utils::removeStringFromEnd($sql,  ', ');
            $sql = $sql.' where `external_product_id` = '.db::quote($this->getID());
            db::query($sql);
        }else{
            // update for all other columns
            db::query('update `external_products` set `'.$field.'` = '.db::quote($value).'
                where `external_product_id` = '.db::quote($this->getID()));
            $this->external_product_data->{$field} = $value;
        }

        return $this;
    }

    protected function getData($key, $throw_e = true ){
        if($throw_e && $this->external_product_data->{$key} === null){
            throw new externalProductException($key.' not set for '.get_class().' id: '.$this->getID());
        }

        return $this->external_product_data->{$key};
    }

    function getID(){
        return $this->getData('external_product_id');
    }


    private static $instances = array();
    /**
     * @return external_product
     */
    static function get($external_product_id){
        if (!isset(external_product::$instances['external_product_id'][$external_product_id.''])) {
            return new external_product($external_product_id);
        }

        return external_product::$instances['external_product_id'][$external_product_id.''];
    }

    public static function getByItemID($item_id){
        if(!isset(external_product::$instances['item_id'][$item_id])){
            return new external_product($item_id, 'item_id');
        }

        return external_product::$instances['item_id'][$item_id];
    }

    static function deleteExternalProduct($external_product_id){
        return db::prepare("DELETE FROM `external_products` WHERE `external_product_id` = ?", array($external_product_id));
    }
}
class externalProduct404 extends toolboxException{}
class externalProductException extends toolboxException{}