<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * Short description of class taoQTI_models_classes_ItemModel
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 * @subpackage models_classes_Export
 */
class taoSubjects_models_classes_SubjectCsvImporter extends tao_models_classes_import_CsvImporter
{
    protected function getExludedProperties() {
       return array_merge(parent::getExludedProperties(), array(PROPERTY_USER_DEFLG, PROPERTY_USER_ROLES));
    }
    
    protected function getStaticData() {
        $lang = tao_helpers_I18n::getLangResourceByCode(DEFAULT_LANG)->getUri();
		return array(
			PROPERTY_USER_DEFLG => $lang,
			PROPERTY_USER_ROLES => INSTANCE_ROLE_DELIVERY
		);
    }
    
    protected function getAdditionAdapterOptions() {
		$returnValue = array(
			'callbacks' => array(
				'*' => array('trim'),
				PROPERTY_USER_PASSWORD => array('md5')
			),
			'onResourceImported' => array(
    		    function(core_kernel_classes_Resource $resource) {
        			$rolesProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
        			$resource->setPropertyValue($rolesProperty, INSTANCE_ROLE_DELIVERY);
    		    }
    		)
	    );
        return $returnValue;
    }
}

?>