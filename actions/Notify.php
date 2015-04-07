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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author "Mikhail Kamarouski"
 *
 **/

namespace oat\taoTestTaker\actions;

use oat\tao\model\controllerMap\ActionNotFoundException;
use oat\taoTestTaker\actions\form\InformForm;
use oat\taoTestTaker\models\InformationService;

/**
 * Subjects Controller provide actions performed from url resolution
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTestTaker
 * @license GPLv2 http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
class Notify extends \tao_actions_SaSModule
{

    public function inform()
    {
        $messages = $result = array();
        $clazz    = $this->getCurrentClass();
        $instance = null;
        if ($this->getRequestParameter( 'uri' )) {
            $instance = $this->getCurrentInstance();
        }


        $formContainer = new InformForm( $clazz, $instance );
        $form          = $formContainer->getForm();

        if ($form->isSubmited()) {
            $this->setData( 'reload', false );

            if ($form->isValid()) {
                $values     = $form->getValues();
                $result     = InformationService::singleton()->invoke(
                    $values[InformForm::ACTION_CONTROL],
                    $instance ? $instance : $clazz,
                    $values
                );

                if (array_intersect(
                    $form->getValue( InformForm::ACTION_CONTROL ),
                    array( InformationService::PRINT_SEPARATE_PAGES, InformationService::PRINT_SINGLE_TABLE )
                )) {
                    $hashResult = spl_object_hash( $result );
                    if ( ! \common_cache_FileCache::singleton()->has( $hashResult )) {
                        \common_cache_FileCache::singleton()->put( $result, $hashResult );
                    }
                    $this->setData(
                        'resultUrl',
                        \tao_helpers_Uri::url( 'result', 'Notify', 'taoTestTaker', array( 'hash' => $hashResult ) )
                    );
                }

            }
        }

        $this->setData( 'form', $form->render() );
        $this->setData( 'messages', array_merge( $messages, array_filter((array)$result,function($e){
            return $e['messages'] ? $e['messages'] : array();
        }) ) );
        $this->setView( 'inform.tpl' );
    }

    /**
     * @throws ActionNotFoundException
     * @throws \common_cache_NotFoundException
     */
    public function result()
    {
        $hash = $this->getRequestParameter( 'hash' );
        if ( ! $hash || ! \common_cache_FileCache::singleton()->has( $hash )) {
            throw new ActionNotFoundException;
        }
        $result = \common_cache_FileCache::singleton()->get( $hash );
        $this->setData( 'result', implode( (array) $result ) );
        $this->setView( 'layout.tpl', 'tao' );
        $this->setData( 'content-template', array( 'print.tpl', 'taoTestTaker' ) );
    }
}