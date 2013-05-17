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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2002-2008 (update and modification) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Extends the common Import class to update the behavior
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoSubjects
 * @subpackage actions
 * 
 */
class taoSubjects_actions_SubjectsImport extends tao_actions_Import {
	
	public function __construct(){
		
		parent::__construct();
		
		$this->excludedProperties = array_merge($this->excludedProperties, array(PROPERTY_USER_DEFLG, PROPERTY_USER_ROLES));
		
		$lang = '';
		$langResource = tao_helpers_I18n::getLangResourceByCode(DEFAULT_LANG);
		if($langResource instanceof core_kernel_classes_Resource){
			$lang = $langResource->getUri();
		}else{
			throw new Exception('cannot find the default system language during subjects import');
		}
		
		$this->staticData = array(
			PROPERTY_USER_DEFLG => $lang,
			PROPERTY_USER_ROLES => INSTANCE_ROLE_DELIVERY
		);
		
		$this->additionalAdapterOptions = array(
			'callbacks' => array(
				'*' => array('trim'),
				PROPERTY_USER_PASSWORD => array('md5')
			)
		);

		// Anonymous function to be applied after resource import.
		$applyRole = function(core_kernel_classes_Resource $resource) {
			$rolesProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
			$resource->setPropertyValue($rolesProperty, INSTANCE_ROLE_DELIVERY);
		};
		
		$this->additionalAdapterOptions['onResourceImported'] = array($applyRole);
	}
	
}
?>