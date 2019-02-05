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

use common_exception_PreConditionFailure;
use common_exception_RestApi;
use oat\generis\model\OntologyRdf;
use oat\generis\model\user\UserRdf;
use oat\tao\model\TaoOntology;
use oat\taoTestTaker\models\CrudService;

/**
 * @OA\Info(title="TAO Test Taker API", version="2.0")
 */
class ApiV2 extends \tao_actions_CommonRestModule
{
    /**
     * @OA\Schema(
     *     schema="taoTestTaker.TestTaker.New",
     *     type="object",
     *     allOf={
     *          @OA\Schema(ref="#/components/schemas/taoTestTaker.TestTaker.Update"),
     *     },
     *     @OA\Property(
     *         property="login",
     *         type="string",
     *         description="Test taker login"
     *     ),
     *     required={"login", "password"}
     * )
     * @OA\Schema(
     *     schema="taoTestTaker.TestTaker.Update",
     *     type="object",
     *     allOf={
     *          @OA\Schema(ref="#/components/schemas/tao.GenerisClass.NewOrExistent"),
     *     },
     *     @OA\Property(
     *         property="label",
     *         type="string",
     *         description="Test taker label"
     *     ),
     *     @OA\Property(
     *         property="login",
     *         type="string",
     *         description="Test taker login"
     *     ),
     *     @OA\Property(
     *         property="password",
     *         type="string",
     *         description="Test taker password"
     *     ),
     *     @OA\Property(
     *         property="uiLg",
     *         type="string",
     *         description="Test taker interface language"
     *     ),
     *     @OA\Property(
     *         property="defLg",
     *         type="string",
     *         description="Test taker default language"
     *     ),
     *     @OA\Property(
     *         property="firstName",
     *         type="string",
     *         description="Test taker first name"
     *     ),
     *     @OA\Property(
     *         property="lastName",
     *         type="string",
     *         description="Test taker last name"
     *     ),
     *     @OA\Property(
     *         property="mail",
     *         type="string",
     *         description="Test taker email"
     *     )
     * )
     */

    const ROOT_CLASS = TaoOntology::CLASS_URI_SUBJECT;

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
            'login' => UserRdf::PROPERTY_LOGIN,
            'password' => UserRdf::PROPERTY_PASSWORD,
            'uiLg' => UserRdf::PROPERTY_UILG,
            'defLg' => UserRdf::PROPERTY_DEFLG,
            'firstName'=> UserRdf::PROPERTY_FIRSTNAME,
            'lastName' => UserRdf::PROPERTY_LASTNAME,
            'mail' => UserRdf::PROPERTY_MAIL
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

    /**
     * @OA\Post(
     *     path="/taoTestTaker/ApiV2",
     *     summary="Create new test taker",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(ref="#/components/schemas/taoTestTaker.TestTaker.New")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Test taker created",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="success",
     *                     type="boolean",
     *                     description="`false` on failure, `true` on success",
     *                 ),
     *                 @OA\Property(
     *                     property="uri",
     *                     type="string",
     *                     description="Created test taker URI",
     *                 ),
     *                 example={
     *                     "success": true,
     *                     "uri": "http://sample/first.rdf#i1536680377163171"
     *                 }
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 example={
     *                     "success": false,
     *                     "errorCode": 0,
     *                     "errorMsg": "Missed required parameter: login",
     *                     "version": "3.3.0-sprint95"
     *                 }
     *             )
     *         ),
     *     )
     * )
     *
     * @return mixed
     * @throws \common_Exception
     * @throws \common_exception_InconsistentData
     * @throws \common_exception_MissingParameter
     * @throws \common_exception_RestApi
     */
    protected function post()
    {
        $parameters = $this->getParameters();

        try {
            $classResource = $this->getOrCreateClassFromRequest($this->getClass(self::ROOT_CLASS));
        }
        catch (\common_exception_NotFound $notFoundException) {
            throw new \common_exception_RestApi($notFoundException->getMessage());
        }

        if ($classResource) {
            $parameters[OntologyRdf::RDF_TYPE] = $classResource->getUri();
        }

        try {
            /** @noinspection PhpUndefinedMethodInspection */
            return $this->getCrudService()->createFromArray($parameters);
        }
        /** @noinspection PhpRedundantCatchClauseInspection */
        catch (common_exception_PreConditionFailure $e) {
            throw new common_exception_RestApi($e->getMessage());
        }
    }

    /**
     * @param null $uri
     * @return mixed
     * @throws \common_exception_NotImplemented
     */
    protected function get($uri = null) {
        throw new \common_exception_NotImplemented('Not implemented');
    }

    /**
     * @param string $uri
     * @return mixed
     * @throws \common_exception_NotImplemented
     */
    protected function put($uri) {
        throw new \common_exception_NotImplemented('Not implemented');
    }

    /**
     * @param string $uri
     * @return mixed
     * @throws \common_exception_NotImplemented
     */
    protected function delete($uri = null) {
        throw new \common_exception_NotImplemented('Not implemented');
    }
}