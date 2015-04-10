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

use common_ext_ExtensionsManager;
use Exception;
use tao_models_classes_Service;

/**
 * Service to generate string base on setting, currently used for password generators
 */
class GeneratorService extends tao_models_classes_Service
{
    const TYPE_ALPHA_UPPER = 'type_alpha_upper';
    const TYPE_NUMBERS = 'type_numbers';
    const TYPE_SPECIAL = 'type_special';
    const TYPE_HUMAN = 'type_human';

    /**
     * Cached filtered wordlist
     * @var array
     */
    protected $wordList = array();

    /**
     * Served types with human readable comments
     * @return array
     */
    public function getAvailableTypes()
    {
        return array(
            self::TYPE_ALPHA_UPPER => __( 'Use uppercase alpha' ),
            self::TYPE_NUMBERS     => __( 'Use numbers' ),
            self::TYPE_SPECIAL     => __( 'Use special characters' ),
            self::TYPE_HUMAN       => __( 'Human friendly' )
        );
    }

    /**
     * Returns password of given type
     *
     * @param integer $length required password length
     * @param array $options constrains for password
     *
     * @return string
     */
    public function generatePassword( $length, array $options = array() )
    {
        $settings           = array();
        $settings['length'] = (int) $length;
        $settings['upper']  = array_search( self::TYPE_ALPHA_UPPER, $options ) !== false;
        $settings['number'] = array_search( self::TYPE_NUMBERS, $options ) !== false;
        $settings['spec']   = array_search( self::TYPE_SPECIAL, $options ) !== false;

        if (in_array( self::TYPE_HUMAN, $options )) {
            return $this->humanReadable( $settings );
        }

        return $this->randomPassword( $settings );

    }

    /**
     * Generates random string that is strictly satisfy settings ( must contain all required types of characters )
     *
     * @param array $settings
     *
     * @return string $password
     */
    private function randomPassword( array $settings )
    {
        $password = '';

        list( $chars, $caps, $nums, $syms ) = $this->getCharacterSets();

        $length = $settings['length'];
        $c      = $n = $s = 0;
        if ($settings['upper']) {
            $c = mt_rand( 1, max( floor( $length / 4 ), 1 ) );
        }
        if ($settings['number']) {
            $n = mt_rand( 1, max( floor( $length / 4 ) - $c, 1 ) );
        }
        if ($settings['spec']) {
            $s = mt_rand( 1, max( floor( $length / 4 ) - $c - $n, 1 ) );
        }

        // build the base password of all lower-case letters
        for ($i = 0; $i < $length; $i ++) {
            $password .= substr( $chars, mt_rand( 0, strlen( $chars ) - 1 ), 1 );
        }
        $count = $c + $n + $s;

        if ($count) {
            // split base password to array; create special chars array
            $tmp1 = str_split( $password );
            $tmp2 = array();

            // add required special character(s) to second array
            for ($i = 0; $i < $c; $i ++) {
                array_push( $tmp2, substr( $caps, mt_rand( 0, strlen( $caps ) - 1 ), 1 ) );
            }
            for ($i = 0; $i < $n; $i ++) {
                array_push( $tmp2, substr( $nums, mt_rand( 0, strlen( $nums ) - 1 ), 1 ) );
            }
            for ($i = 0; $i < $s; $i ++) {
                array_push( $tmp2, substr( $syms, mt_rand( 0, strlen( $syms ) - 1 ), 1 ) );
            }

            $tmp1 = array_slice( $tmp1, 0, $length - $count );
            // merge special character(s) array with base password array
            $tmp1 = array_merge( $tmp1, $tmp2 );
            // mix the characters up
            shuffle( $tmp1 );
            // convert to string for output
            $password = implode( '', $tmp1 );
        }

        return $password;
    }

    /**
     * @param array $settings
     *
     * @return string
     * @throws Exception
     */
    private function humanReadable(array $settings)
    {
        $config = $this->getConfig();

        //get a few words from dictionary
        $wordList = $this->getWords( $config );

        $wordsCount = count($wordList);
        if (!$wordsCount) {
            throw new Exception('No words selected.');
        }
        $selected = array();

        for ($i = 0; $i < 3; $i ++) {
            $x = mt_rand( 0, $wordsCount - 1 );
            $selected[] = $wordList[$x];
        }

        //apply modificators
        list( , , $nums, $syms ) = $this->getCharacterSets();

        if ($settings['upper']) {
            $i = mt_rand( 0, count( $selected ) - 1 );
            $selected[$i] = ucfirst( $selected[$i] );
        }

        if ($settings['number']) {
            $selected[mt_rand( 0, count( $selected ) - 1 )] .= substr( $nums, mt_rand( 0, strlen( $nums ) - 1 ), 1 );
        }

        if ($settings['spec']) {
            $selected[mt_rand( 0, count( $selected ) - 1 )] .= substr( $syms, mt_rand( 0, strlen( $syms ) - 1 ), 1 );
        }

        shuffle( $selected );

        return implode( '', $selected );
    }

    /**
     * Returns configuration
     * @return array
     * @throws \common_ext_ExtensionException
     */
    protected function getConfig()
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById( 'generis' );

        return $ext->getConfig( 'passwords' );
    }

    /**
     * Returns mapped settings for generator and forms
     * @return array
     */
    public function getSettings()
    {
        $options = $this->getConfig();

        $settings = array();
        in_array( 'upper', $options ) !== false && $options['upper'] ? $settings[] = self::TYPE_ALPHA_UPPER : null;
        in_array( 'number', $options ) !== false && $options['number'] ? $settings[] = self::TYPE_NUMBERS : null;
        in_array( 'spec', $options ) !== false && $options['spec'] ? $settings[] = self::TYPE_SPECIAL : null;

        return array_filter( $settings );
    }

    /**
     * @return array
     */
    private function getCharacterSets()
    {
        $config  = $this->getConfig();
        $similar = $config['similar'];

        $charactersToRemove = str_split( $similar );

        $chars = str_replace( $charactersToRemove, '', $config['chars'] );
        $caps  = str_replace( $charactersToRemove, '', strtoupper( $chars ) );
        $nums  = str_replace( $charactersToRemove, '', $config['nums'] );
        $syms  = str_replace( $charactersToRemove, '', $config['syms'] );

        return array( $chars, $caps, $nums, $syms );
    }

    /**
     * @param $config
     *
     * @return array
     * @throws Exception
     */
    protected function getWords( $config )
    {
        if ( ! array_key_exists( 'dictionary', $config ) || ! file_exists( $config['dictionary'] )) {
            throw new Exception( 'Password dictionary not found, please contact you system administrator' );
        }

        if ( ! $this->wordList) {
            $wordList       = explode( "\n", \file_get_contents( $config['dictionary'] ) );
            $wordList       = array_filter(
                $wordList,
                function ( $e ) {
                    return strlen( $e ) > 3 && strlen( $e ) < 8;
                }
            );
            $this->wordList = array_values( $wordList );
        }

        return $this->wordList;
    }

}