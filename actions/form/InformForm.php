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
 * @author Mikhail Kamarouski, kamarouski@1pt.com
 */
namespace oat\taoTestTaker\actions\form;


use oat\tao\helpers\Template;
use oat\taoTestTaker\models\GeneratorService;
use oat\taoTestTaker\models\InformationService;
use tao_actions_form_Generis;
use tao_helpers_form_FormFactory;
use tao_helpers_Uri;

class InformForm extends tao_actions_form_Generis
{
    const ACTION_CONTROL = 'actionType';
    const PWD_CONTROL    = 'pwdControl';
    const PWD_LENGTH     = 'pwdLength';
    const TEMPLATE       = 'template';


    /**
     * @inheritdoc
     */
    protected function initForm()
    {
        $this->form = tao_helpers_form_FormFactory::getForm( 'Inform', $this->options );

        $proceedElt = \tao_helpers_form_FormFactory::getElement( 'create', 'Button' );
        $proceedElt->setValue( __( 'Proceed' ) );
        $proceedElt->setIcon( "icon-email" );
        $proceedElt->addClass( "form-submitter btn-success small" );

        $this->form->setActions( array( $proceedElt ), 'bottom' );
    }

    /**
     * @inheritdoc
     *
     **/
    protected function initElements()
    {

        $actionElt = \tao_helpers_form_FormFactory::getElement( self::ACTION_CONTROL, 'Checkbox' );
        $actionElt->setDescription( __( 'Desired action' ) . '*' );
        $actionElt->setOptions( InformationService::singleton()->getAvailableTypes() );
        $actionElt->addValidator( tao_helpers_form_FormFactory::getValidator( 'NotEmpty' ) );
        $this->form->addElement( $actionElt );

        $templateElt = \tao_helpers_form_FormFactory::getElement( self::TEMPLATE, 'Textarea' );
        $templateElt->setDescription( __( 'Email template' ) . '*' );

        $render = new \Renderer();
        $render->setTemplate( Template::getTemplate( 'mail/message.tpl', 'taotesttaker' ) );

        $templateElt->setValue( $render->render() );
        $templateElt->setAttribute( 'rows', 10 );
        $this->form->addElement( $templateElt );


        $pwdOptionsElt = \tao_helpers_form_FormFactory::getElement( self::PWD_CONTROL, 'RadioBox' );
        $pwdOptionsElt->setDescription( __( 'Type' ) );
        $pwdOptionsElt->setOptions( GeneratorService::singleton()->getAvailableTypes() );
        $this->form->addElement( $pwdOptionsElt );

        $pwdOptionsElt = \tao_helpers_form_FormFactory::getElement( self::PWD_LENGTH, 'Textbox' );
        $pwdOptionsElt->setDescription( __( 'Length' ) );
        $pwdOptionsElt->setValue( 8 );
        $pwdOptionsElt->addValidators(
            array(
                tao_helpers_form_FormFactory::getValidator( 'Numeric' ),
            )
        );
        $this->form->addElement( $pwdOptionsElt );


        $this->form->createGroup( 'options', __( 'Password Settings' ), array( self::PWD_CONTROL, self::PWD_LENGTH ) );

        $this->addDefaultElements();

    }


    /**
     * Adds elements to keep tracking active models
     * @throws \common_Exception
     */
    protected function addDefaultElements()
    {
        $clazz    = $this->getClazz();
        $instance = $this->getInstance();

        //add an hidden elt for the class uri
        $classUriElt = tao_helpers_form_FormFactory::getElement( 'classUri', 'Hidden' );
        $classUriElt->setValue( tao_helpers_Uri::encode( $clazz->getUri() ) );
        $this->form->addElement( $classUriElt );

        if ( ! is_null( $instance )) {
            //add an hidden elt for the instance Uri
            $instanceUriElt = tao_helpers_form_FormFactory::getElement( 'uri', 'Hidden' );
            $instanceUriElt->setValue( tao_helpers_Uri::encode( $instance->getUri() ) );
            $this->form->addElement( $instanceUriElt );

            $hiddenId = tao_helpers_form_FormFactory::getElement( 'id', 'Hidden' );
            $hiddenId->setValue( $instance->getUri() );
            $this->form->addElement( $hiddenId );
        }
    }

}