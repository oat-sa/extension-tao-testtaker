<?php
/**
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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA
 *
 */
namespace oat\taoTestTaker\models;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;

/**
 *
 * Crud services implements basic CRUD services, orginally intended for
 * REST controllers/ HTTP exception handlers.
 * Consequently the signatures and behaviors is closer to REST and throwing
 * HTTP like exceptions.
 *
 * @author Patrick Plichart, patrick@taotesting.com
 *
 */
class CrudService extends \tao_models_classes_CrudService
{

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_CrudService::getClassService()
     */
    protected function getClassService(){
        return TestTakerService::singleton();
    }

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_CrudService::delete()
     */
    public function delete($resource)
    {
        $this->getClassService()->deleteSubject(new \core_kernel_classes_Resource($resource));
        return true;
    }

    /**
     *
     * @author Patrick Plichart, patrick@taotesting.com
     * @param  array $propertiesValues
     * @return \core_kernel_classes_Resource
     * @throws \common_exception_Error
     * @throws \common_exception_MissingParameter
     * @throws \common_exception_PreConditionFailure
     * @throws \common_exception_ValidationFailed
     * @throws \oat\generis\model\user\PasswordConstraintsException
     */
    public function createFromArray($propertiesValues = array())
    {

        // mandatory parameters
        if (! isset($propertiesValues[GenerisRdf::PROPERTY_USER_LOGIN])) {
            throw new \common_exception_MissingParameter("login");
        }
        if (! isset($propertiesValues[GenerisRdf::PROPERTY_USER_PASSWORD])) {
            throw new \common_exception_MissingParameter("password");
        }
        // default values and validation
        $propertiesValues[GenerisRdf::PROPERTY_USER_UILG] =
            self::readLangProperty($propertiesValues, GenerisRdf::PROPERTY_USER_UILG);

        $propertiesValues[GenerisRdf::PROPERTY_USER_DEFLG] =
            self::readLangProperty($propertiesValues, GenerisRdf::PROPERTY_USER_DEFLG);


        if (! isset($propertiesValues[OntologyRdfs::RDFS_LABEL])) {
            $propertiesValues[OntologyRdfs::RDFS_LABEL] = "";
        }
        // check if login already exists
        $userService = \tao_models_classes_UserService::singleton();
        if ($userService->loginExists($propertiesValues[GenerisRdf::PROPERTY_USER_LOGIN])) {
            throw new \common_exception_PreConditionFailure("login already exists");
        }
        $propertiesValues[GenerisRdf::PROPERTY_USER_PASSWORD] = \core_kernel_users_Service::getPasswordHash()->encrypt($propertiesValues[GenerisRdf::PROPERTY_USER_PASSWORD]);
        $type = isset($propertiesValues[OntologyRdf::RDF_TYPE]) ? $propertiesValues[OntologyRdf::RDF_TYPE] : $this->getRootClass();
        $label = $propertiesValues[OntologyRdfs::RDFS_LABEL];
        // hmmm
        unset($propertiesValues[OntologyRdfs::RDFS_LABEL]);
        unset($propertiesValues[OntologyRdf::RDF_TYPE]);

        $resource = parent::create($label, $type, $propertiesValues);
        
        $this->getClassService()->setTestTakerRole($resource);
        
        return $resource;
    }

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_CrudService::update()
     */
    public function update($uri = null, $propertiesValues = array())
    {
        if (is_null($uri)) {
            throw new \common_exception_MissingParameter("uri");
        }
        if (isset($propertiesValues[GenerisRdf::PROPERTY_USER_LOGIN])) {
            throw new \common_exception_PreConditionFailure("login update not allowed");
        }
        if (isset($propertiesValues[GenerisRdf::PROPERTY_USER_PASSWORD])) {
            $propertiesValues[GenerisRdf::PROPERTY_USER_PASSWORD] = \core_kernel_users_Service::getPasswordHash()->encrypt($propertiesValues[GenerisRdf::PROPERTY_USER_PASSWORD]);
        }
        parent::update($uri, $propertiesValues);
        // throw new common_exception_NotImplemented();
    }

    /**
     * @param array $properties
     * @param string $propKey
     * @return string
     * @throws \common_exception_Error
     * @throws \common_exception_ValidationFailed
     */
    protected static function readLangProperty(array &$properties, $propKey) {
        if (!isset($properties[$propKey])) {
            return \tao_helpers_I18n::getLangResourceByCode(DEFAULT_LANG)->getUri();
        }

        $existingUri = \tao_models_classes_LanguageService::getExistingLanguageUri($properties[$propKey]);
        if ($existingUri === null) {
            throw new \common_exception_ValidationFailed($propKey);
        }

        return $existingUri;
    }
}
