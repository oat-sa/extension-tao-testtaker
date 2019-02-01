<?php /** @noinspection UnNecessaryDoubleQuotesInspection */

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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoTestTaker\actions;

use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;
use oat\tao\model\TaoOntology;
use oat\taoTestTaker\models\CrudService;


class ApiV2 extends \tao_actions_CommonRestModule
{

    public function __construct() {
        parent::__construct();
        $this->service = CrudService::singleton();
    }

    /**
     * Optionally a specific rest controller may declare
     * aliases for parameters used for the rest communication
     */
    protected function getParametersAliases(){
        return array_merge(parent::getParametersAliases(), [
            'login' => GenerisRdf::PROPERTY_USER_LOGIN,
            'password' => GenerisRdf::PROPERTY_USER_PASSWORD,
            'guiLg' => GenerisRdf::PROPERTY_USER_UILG,
            'dataLg' => GenerisRdf::PROPERTY_USER_DEFLG,
            'firstName'=> GenerisRdf::PROPERTY_USER_FIRSTNAME,
            'lastName' => GenerisRdf::PROPERTY_USER_LASTNAME,
            'mail' => GenerisRdf::PROPERTY_USER_MAIL,
            'type' => OntologyRdf::RDF_TYPE
        ]);
    }

    /**
     * Optional Requirements for parameters to be sent on every service
     */
    protected function getParametersRequirements() {
        return [
            'post' => array("login", "password")
        ];
    }

    protected function getParameters()
    {
        $classResource = $this->getClassFromRequest($this->getClass(TaoOntology::CLASS_URI_SUBJECT));

        return array_merge(
            parent::getParameters(),
            [
                OntologyRdf::RDF_TYPE => $classResource->getUri()
            ]);
    }


}