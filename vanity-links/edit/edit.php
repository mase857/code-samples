<?php namespace toolbox;
class edit_controller {

    static function passThru(){
        //extract the vanity link id if it is in the URL
        router::get()->extractParam('vanity_link_id');
    }

    function __construct(){

        //set the default title
        title::get()->setSubtitle('Add New Vanity Link');

        //get the existing id if it was successfully extracted from the URL
        $vanity_link_id = router::get()->getParam('vanity_link_id');//returns false if not set
        if($vanity_link_id !== false){//get the object
            $vanityLink = vanityLink::get($vanity_link_id);
        }

        //if deleting
        if(isset($_GET['action']) && $_GET['action'] === 'delete') {
            $vanityLink->delete();
            messages::setSuccessMessage('Vanitly link deleted successfully: '.$vanityLink->getLinkUrl());
            appUtils::widgetFormClose('Vanity Link');//renders the message as a form widget
        }

        //if submitting a new record
        if(isset($_POST['action']) && $_POST['action'] === 'save' && $vanity_link_id === false) {

            if($this->isValid()) {//is valid
                vanityLink::createVanityLink($_POST);
                messages::setSuccessMessage('Saved Successfully');
                appUtils::widgetFormClose('Add New Link');//renders the message as a form widget

            } else {//invalid submission
                messages::setErrorMessage('Please correct the errors below and try again.');
                formV2::storeValues();//restore user's input
            }

        //if updating an existing record
        } elseif(isset($_POST['action']) && $_POST['action'] === 'save' && $vanity_link_id !== false){

            if($this->isValid()){
                //$vanityLink->setLinkName($_POST['link_name']);
                $vanityLink->setLinkUrl($_POST['link_url']);
                $vanityLink->setRedirectTo($_POST['redirect_to']);

                messages::setSuccessMessage('Saved Successfully');
            } else{
                messages::setErrorMessage('Please correct the errors below and try again.');
                formV2::storeValues();
            }

        //else get details of existing record
        }else if($vanity_link_id !== false) {
            title::get()->setSubtitle('Edit Vanity Link');
            $restore_data = array();
            $restore_data['link_url'] = $vanityLink->getLinkUrl();
            $restore_data['redirect_to'] = $vanityLink->getRedirectTo();

            formV2::storeValues($restore_data);//prefills form fields with data
        }

        widgetHelper::createStandard(title::get()->getSubtitle())
            ->set('vanity_link_id', $vanity_link_id)
            ->addAjaxFormWithContainer('vanity_links_form', function($tpl){ ?>
                <div class="form_panel style2">
                    <?php messages::printMessages('messages', 'style5'); ?>
                    <div class="grid-12 grid-m-12">
                        <?php
                        formV2::textfield()
                            ->setLabel('Link Url')
                            ->setName('link_url')
                            ->setNote('Ex: https://www.watchgang.com/r/< Link Url>')
                            ->renderViews();
                        ?>
                        <div class="catchall spacer-1"></div></div><?php
                    ?>
                    <div class="grid-12 grid-m-12">
                        <?php
                        formV2::textfield()
                            ->setLabel('Redirect To')
                            ->setName('redirect_to')
                            ->setNote('Ex: https://www.google.com')
                            ->renderViews();
                        ?>
                        <div class="catchall spacer-1"></div></div><?php
                    ?>

                    <div class="catchall spacer-2"></div>
                    <div class="catchall"></div>
                </div>
                <div class="datatable">
                    <div class="datatable-info datatable-section">
                        <button type="submit" name="action" value="save"
                                class="btn btn-medium btn-3d btn-full_width btn-primary"><?php
                                if($tpl->vanity_link_id !== false){ ?>
                                    Save Changes
                                    <?php } else{ ?>
                                    Add Vanity Link
                                <?php } ?></button>
                    </div>
                </div>
            <?php });

    }

    function isValid(){

        validator::validate('link_url', 'general');

        validator::validate('redirect_to', 'url');

        return validator::isValid();

    }

}
