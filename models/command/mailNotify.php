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
use oat\tao\model\messaging\transportStrategy\MailAdapter;
use oat\taoTestTaker\actions\form\InformForm;

class MailNotify implements CommandInterface
{
    /**
     * @param array $testTakers
     * @param array $options
     *
     * @return mixed|void
     * @throws \Exception
     */
    public function invoke( array $testTakers, array $options = array() )
    {
        $template = $options[InformForm::TEMPLATE];

        /** @var \core_kernel_users_GenerisUser $testTaker */
        foreach ($testTakers as $testTaker) {
            $userMail = (string) current( $testTaker->getPropertyValues( PROPERTY_USER_MAIL ) );

            if ( ! filter_var( $userMail, FILTER_VALIDATE_EMAIL )) {
                throw new \Exception( 'User email is not valid.' );
            }

            $message = new Message();
            $message->setTo( $testTaker );
            $message->setBody(
                $this->getMailContent(
                    $template,
                    array(
                        '%name%'     => (string) current( $testTaker->getPropertyValues( PROPERTY_USER_FIRSTNAME ) ),
                        '%password%' => (string) $testTaker->password ,
                    )
                )
            );
            $message->setTitle( __( "Notification about your TAO Password" ) );
            $mailAdapter = new MailAdapter();
            $mailAdapter->send();
            \common_Logger::i( 'notification mail was send to ' . $testTaker->getIdentifier() );

        }
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