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
 * 
 */

/**
 * .Crud services implements basic CRUD services, orginally intended for REST controllers/ HTTP exception handlers
 *  Consequently the signatures and behaviors is closer to REST and throwing HTTP like exceptions
 *  
 *
 *
 */
class taoSubjects_models_classes_CrudSubjectsService
    extends taoSubjects_models_classes_SubjectsService
{
    public function __construct()
    {
        
		parent::__construct();
    }
    public function getTestTaker($uri){
	return parent::get($uri);
    }
    public function getAllTestTakers(){
	return parent::getAll();
    }
    
    public function deleteTestTaker( $resource){
	return parent::delete($resource);
    }
     public function deleteAll(){
	return parent::deleteAll();
    }
    /**
     * @param array parameters an array of property uri and values
     */
    public function createTestTaker(array $parameters){
	
		//mandatory parameters
		if (!isset($parameters[PROPERTY_USER_LOGIN])) {
			throw new common_exception_MissingParameter("login");
		}
		if (!isset($parameters[PROPERTY_USER_PASSWORD])) {
			throw new common_exception_MissingParameter("password");
		}
		//default values
		if (!isset($parameters[PROPERTY_USER_UILG])) {
			$parameters[PROPERTY_USER_UILG] = tao_helpers_I18n::getLangResourceByCode(DEFAULT_LANG);
		}
		if (!isset($parameters[PROPERTY_USER_DEFLG])) {
			$parameters[PROPERTY_USER_DEFLG] = tao_helpers_I18n::getLangResourceByCode(DEFAULT_LANG);
		}
		if (!isset($parameters[RDFS_LABEL])) {
			$parameters[RDFS_LABEL] = "";
		}
		//check if login already exists
		$userService = tao_models_classes_UserService::singleton();
		if ($userService->loginExists($parameters[PROPERTY_USER_LOGIN])) {
			throw new common_exception_PreConditionFailure("login already exists");
		}
		$parameters[PROPERTY_USER_PASSWORD] = md5($parameters[PROPERTY_USER_PASSWORD]);
		$type = isset($parameters[RDF_TYPE]) ? $parameters[RDF_TYPE] : $this->getRootClass();
		$label = $parameters[RDFS_LABEL];
		//hmmm
		unset($parameters[RDFS_LABEL]);
		unset($parameters[RDF_TYPE]);

		$resource =  parent::create($label, $type, $parameters);
		
		$roleProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
		$subjectRole = new core_kernel_classes_Resource(INSTANCE_ROLE_DELIVERY);
		$resource->setPropertyValue($roleProperty, $subjectRole);
		return $resource;
    }

	public function updateTestTaker($uri = null,array $parameters){
		if (is_null($uri)){
		    throw new common_exception_MissingParameter("uri");
		}
		if (isset($parameters[PROPERTY_USER_LOGIN])) {
			throw new common_exception_PreConditionFailure("login update not allowed");
		}
		if (isset($parameters[PROPERTY_USER_PASSWORD])) {
			$parameters[PROPERTY_USER_PASSWORD] = md5($parameters[PROPERTY_USER_PASSWORD]);
		}
		parent::update($uri, $parameters);
		//throw new common_exception_NotImplemented();
	}
    
} /* end of class taoSubjects_models_classes_SubjectsService */

?>
