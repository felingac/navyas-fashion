<?php
 
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
/**
 * Adding Different Attributes
 */
 
// adding attribute group
$setup->addAttributeGroup('catalog_product', 'Fabric', 'Navyas Options', 1000);
 
// the attribute added will be displayed under the group/tab Special Attributes in product edit page
$setup->addAttribute('catalog_product', 'enquiry', array(
    'group'         => 'Navyas Options',
    'input'         => 'boolean',
    'type'          => 'int',
    'label'         => 'Enquiry',
    'backend'       => '',
    'frontend' 	    => '',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable'    => 0,
    'visible_on_front' => 1,
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
 
$installer->endSetup();
