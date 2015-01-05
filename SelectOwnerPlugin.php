<?php
/**
 * Select Owner
 *
 * @author John Kloor <kloor@bgsu.edu>
 * @copyright 2015 Bowling Green State University Libraries
 * @license MIT
 */

/**
 * Select Owner plugin class.
 *
 * @package SelectOwner
 */
class SelectOwnerPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Plugin hooks.
     */
    protected $_hooks = array(
        'admin_items_panel_fields',
        'admin_items_show_sidebar',
        'before_save_item',
        'admin_collections_panel_fields',
        'admin_collections_show_sidebar',
        'before_save_collection'
    );

    /**
     * Hook to panel fields for editing an item in the admin interface.
     *
     * @param array $args Passed to _addField() method.
     */
    public function hookAdminItemsPanelFields($args)
    {
        // Add field to the Admin Items Panel.
        $this->_addField($args);
    }

    /**
     * Hook to the sidebar when showing an item in the admin interface.
     *
     * @param array $args Passed to _addPanel() method.
     */
    public function hookAdminItemsShowSidebar($args)
    {
        $this->_addPanel($args);
    }

    /**
     * Hook an action to occur before an item is saved.
     *
     * @param array $args Passed to _setOwner() method.
     */
    public function hookBeforeSaveItem($args)
    {
        $this->_setOwner($args);
    }

    /**
     * Hook to panel fields for editing a collection in the admin interface.
     *
     * @param array $args Passed to _addField() method.
     */
    public function hookAdminCollectionsPanelFields($args)
    {
        // Add field to the Admin Collections Panel.
        $this->_addField($args);
    }

    /**
     * Hook to the sidebar when showing a collection in the admin interface.
     *
     * @param array $args Passed to _addPanel() method.
     */
    public function hookAdminCollectionsShowSidebar($args)
    {
        $this->_addPanel($args);
    }


    /**
     * Hook an action to occur before a collection is saved.
     *
     * @param array $args Passed to _setOwner() method.
     */
    public function hookBeforeSaveCollection($args)
    {
        $this->_setOwner($args);
    }

    /**
     * Add panel field when editing an item or collection in admin interface.
     *
     * The field will be labeled "Owner" and provide a select element named
     * "owner_id" with all of the users from the User table, sorted by name.
     * If the record already has an owner, that option will be selected.
     *
     * @param array $args Provides "record" and "view".
     */
    private function _addField($args)
    {
        $record = $args['record'];
        $owner = $record->getOwner();
        $view = $args['view'];

        print(
            '<div class="field">'.
            '<label for="owner_id">'. __('Owner'). '</label>'.
            '<div class="inputs">'.
            $view->formSelect(
                'owner_id',
                $owner ? $owner->id : null,
                array('id' => 'owner_id'),
                get_table_options('User', null, array('sort_field' => 'name'))
            ).
            '</div>'.
            '</div>'
        );
    }

    /**
     * Add sidebar panel when viewing an item or collection in admin interface.
     *
     * The panel will be labeled "Owner" and will provide the name of the owner
     * of the record.
     *
     * @param array $args Provides "record".
     */
    private function _addPanel($args)
    {
        $record = isset($args['item']) ? $args['item'] : $args['collection'];
        $owner = $record->getOwner();

        if ($owner) {
            print(
                '<div class="public-featured panel"><p>'.
                '<span class="label">'. __('Owner'). ':</span> '.
                $owner->name.
                '</p></div>'
            );
        }
    }

    /**
     * Set the owner of an item or collection if submitted on a form.
     *
     * If post data named "owner_id" is available, that ID will looked up in
     * the User table. If the user is available, the records ownership will be
     * changed to that user.
     *
     * @param array $args Provides "record".
     */
    private function _setOwner($args)
    {
        if (!empty($args['post']['owner_id'])) {
            $ownerId = $args['post']['owner_id'];
            $owner = $this->_db->getTable('User')->find($ownerId);

            if ($owner) {
                $record = $args['record'];
                $record->setOwner($owner);
            }
        }
    }
}
