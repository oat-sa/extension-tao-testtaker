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

use Exception;
use common_exception_RestApi;
use common_exception_ValidationFailed;
use oat\generis\model\OntologyRdf;
use oat\generis\model\user\PasswordConstraintsException;
use oat\generis\model\user\UserRdf;
use oat\tao\model\TaoOntology;
use oat\taoTestTaker\models\CrudService;

/**
 * @OA\Info(title="TAO Test Taker API", version="2.0")
 * @OA\Post(
 *     path="/taoTestTaker/api/testTakers",
 *     summary="Create new test taker",
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(ref="#/components/schemas/taoTestTaker.TestTaker.New")
 *         )
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
 *         response="400",
 *         description="Invalid request data",
 *         @OA\JsonContent(ref="#/components/schemas/tao.RestTrait.FailureResponse")
 *     )
 * )
 *
 */
class RestTestTakers extends \tao_actions_CommonRestModule
{
    /**
     * @OA\Schema(
     *     schema="taoTestTaker.TestTaker.New",
     *     type="object",
     *     allOf={
     *          @OA\Schema(ref="#/components/schemas/tao.GenerisClass.Search"),
     *          @OA\Schema(ref="#/components/schemas/taoTestTaker.TestTaker.Update")
     *     },
     *     @OA\Property(
     *         property="login",
     *         type="string",
     *         description="Login"
     *     ),
     *     required={"login", "password"}
     * )
     * @OA\Schema(
     *     schema="taoTestTaker.TestTaker.Update",
     *     type="object",
     *     @OA\Property(
     *         property="label",
     *         type="string",
     *         description="Label"
     *     ),
     *     @OA\Property(
     *         property="login",
     *         type="string",
     *         description="Login"
     *     ),
     *     @OA\Property(
     *         property="password",
     *         type="string",
     *         description="Password"
     *     ),
     *     @OA\Property(
     *         property="uiLg",
     *         type="string",
     *         description="Interface language (uri or language code, 'fr-FR' or 'http://www.tao.lu/Ontologies/TAO.rdf#Langfr-FR' for example)"
     *     ),
     *     @OA\Property(
     *         property="defLg",
     *         type="string",
     *         description="Default language (uri or language code, 'fr-FR' or 'http://www.tao.lu/Ontologies/TAO.rdf#Langfr-FR' for example)"
     *     ),
     *     @OA\Property(
     *         property="firstName",
     *         type="string",
     *         description="First name"
     *     ),
     *     @OA\Property(
     *         property="lastName",
     *         type="string",
     *         description="Last name"
     *     ),
     *     @OA\Property(
     *         property="mail",
     *         type="string",
     *         description="Email"
     *     )
     * )
     */

    const ROOT_CLASS = TaoOntology::CLASS_URI_SUBJECT;

    public function __construct()
    {
        parent::__construct();
        $this->service = CrudService::singleton();
    }

    public function index()
    {
        $this->returnFailure(new common_exception_RestApi('Not implemented.'));
    }

    /**
     * Optionally a specific rest controller may declare
     * aliases for parameters used for the rest communication
     */
    protected function getParametersAliases()
    {
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
    protected function getParametersRequirements()
    {
        return [
            'post' => array("login", "password")
        ];
    }

    /**
     * @param null $uri
     * @return mixed
     * @throws \common_exception_NotImplemented
     */
    public function get($uri = null)
    {
        $this->returnFailure(new common_exception_RestApi('Not implemented.'));
    }

    /**
     * @param string $uri
     * @return mixed
     * @throws \common_exception_NotImplemented
     */
    public function put($uri)
    {
        $this->returnFailure(new common_exception_RestApi('Not implemented.'));
    }

    public function post()
    {
        try {
            /** @var \core_kernel_classes_Resource $testTakerResource */
            $testTakerResource = parent::post();

            $this->returnSuccess([
                'success' => true,
                'uri' => $testTakerResource->getUri(),
            ], false);
        } catch (PasswordConstraintsException $e) {
            $this->returnFailure(new common_exception_RestApi($e->getMessage()));
        } catch (common_exception_ValidationFailed $e) {
            $alias = $this->reverseSearchAlias($e->getField());
            $this->returnFailure(new common_exception_ValidationFailed($alias, null, $e->getCode()));
        } catch (Exception $e) {
            $this->returnFailure($e);
        }
    }

    /**
     * @param string $uri
     * @return mixed
     * @throws \common_exception_NotImplemented
     */
    public function delete($uri = null)
    {
        $this->returnFailure(new common_exception_RestApi('Not implemented.'));
    }

    protected function getParameters()
    {
        $parameters = parent::getParameters();

        if ($this->getRequestMethod() === 'POST' &&
            $classResource = $this->getClassFromRequest($this->getClass(self::ROOT_CLASS))
        ) {
            $parameters[OntologyRdf::RDF_TYPE] = $classResource->getUri();
        }

        return $parameters;
    }
}