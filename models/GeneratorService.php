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
 *
 * @author Mikhail Kamarouski, <Komarouski@1pt.com>
 */
namespace oat\taoTestTaker\models;

use Hackzilla\PasswordGenerator\Generator\AbstractPasswordGenerator;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Hackzilla\PasswordGenerator\Generator\HumanPasswordGenerator;
use tao_models_classes_Service;

/**
 * Service to generate string base on setting, currently used for password generators
 */
class GeneratorService extends tao_models_classes_Service
{
    const TYPE_ALPHA = 'type_alpa';
    const TYPE_MIXED = 'type_mixed';
    const TYPE_HUMAN = 'type_human';

    private static $generators = array();

    /**
     * Served types with human readable comments
     * @return array
     */
    public function getAvailableTypes()
    {
        return array(
            self::TYPE_ALPHA => __( 'Alpha only' ),
            self::TYPE_MIXED => __( 'Alpha & digits' ),
            self::TYPE_HUMAN => __( 'Human friendly' )
        );
    }

    /**
     * @param $type
     *
     * @return AbstractPasswordGenerator
     * @throws \Exception
     */
    protected function getGenerator( $type )
    {
        if (isset( self::$generators[$type] )) {
            return self::$generators[$type];
        }
        throw new \Exception( 'Generator is not initialized' );
    }


    /**
     * Returns password of given type
     *
     * @param $length
     * @param $type
     *
     * @return string
     */
    public function generatePassword( $length, $type )
    {
        $this->init();

        $generator = $this->getGenerator( $type );
        if ($generator instanceof ComputerPasswordGenerator) {
            $generator->setLength( intval( $length ) );
        }

        return $generator->generatePassword();
    }

    /**
     * Initialize all generators
     * @throws \Exception
     */
    private function init()
    {

        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('taotesttaker');
        $creatorConfig = $ext->getConfig('password');
        $dictionaryFile = trim($creatorConfig['dictionary']);

        if ( ! file_exists( $dictionaryFile )) {
            throw new \Exception( 'No proper dictionary file configured ' );
        }

        if ( ! ( self::$generators )) {
            self::$generators = array(
                self::TYPE_ALPHA => new ComputerPasswordGenerator(),
                self::TYPE_MIXED => new ComputerPasswordGenerator(),
                self::TYPE_HUMAN => new HumanPasswordGenerator(),
            );
        }

        $this->getGenerator( self::TYPE_ALPHA )->setOptionValue( ComputerPasswordGenerator::OPTION_NUMBERS, false );
        $this->getGenerator( self::TYPE_MIXED )->setOptionValue( ComputerPasswordGenerator::OPTION_NUMBERS, true );
        $this->getGenerator( self::TYPE_HUMAN )->setWordList( $dictionaryFile )->setOptionValue(
            HumanPasswordGenerator::OPTION_MAX_WORD_LENGTH,
            6
        );

    }


}
