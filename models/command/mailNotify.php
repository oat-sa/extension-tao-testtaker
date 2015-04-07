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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 * @author Mikhail Kamarouski, kamarouski@1pt.com
 */
namespace oat\taoTestTaker\models\command;

use oat\tao\helpers\Template;
use oat\tao\model\messaging\Message;
use oat\tao\model\messaging\MessagingService;
use oat\taoTestTaker\actions\form\InformForm;

class MailNotify implements CommandInterface
{
    /**
     * @param array $testTakers
     * @param array $options
     *
     * @return mixed|void
     * @throws \common_exception_Error
     */
    public function invoke( array $testTakers, array $options = array() )
    {
        $result           = array();
        $count            = 0;
        $template         = $options[InformForm::TEMPLATE];
        $messagingService = MessagingService::singleton();
        if ( ! $messagingService->isAvailable()) {
            throw new \common_exception_Error( 'Messaging service is not available.' );
        }
        /** @var \core_kernel_users_GenerisUser $testTaker */
        foreach ($testTakers as $testTaker) {
            $userMail = (string) current( $testTaker->getPropertyValues( PROPERTY_USER_MAIL ) );

            if ( ! filter_var( $userMail, FILTER_VALIDATE_EMAIL )) {
                throw new \common_exception_Error( 'User email is not valid.' );
            }

            $message = new Message();
            $message->setTo( $testTaker );
            $message->setBody(
                $this->getMailContent(
                    $template,
                    array(
                        '%name%'     => (string) current( $testTaker->getPropertyValues( PROPERTY_USER_FIRSTNAME ) ),
                        '%password%' => (string) $testTaker->password,
                    )
                )
            );
            $message->setTitle( __( "Notification about your TAO Password" ) );

            $sendResult = $messagingService->getTransport()->send( $message );

            \common_Logger::i( 'notification mail was send to ' . $testTaker->getIdentifier() );
            if ( ! $sendResult) {
                $result['messages']['error'][] = $messagingService->getErrors();
                \common_Logger::i( 'sending error occured ' . $messagingService->getErrors() );
            } else {
                $count ++;
            }
            $result['messages']['success'][] = __( 'Notification send to %d testakers', $count );
        }

        return $result;
    }

    /**
     * @param string $template
     * @param array $messageData
     *
     * @return string
     * @throws \common_Exception
     */
    private function getMailContent( $template, array $messageData = array() )
    {
        $renderer = new \Renderer();
        $renderer->setTemplate( Template::getTemplate( 'mail/layout.tpl', 'taotesttaker' ) );

        $template = str_replace( array_keys( $messageData ), array_values( $messageData ), $template );
        $renderer->setData( 'message', $template );

        return $renderer->render();
    }
}