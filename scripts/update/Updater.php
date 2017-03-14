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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoTestTaker\scripts\update;

/**
 * Class Updater
 * @package oat\taoTestTaker\scripts\update
 */
class Updater extends \common_ext_ExtensionUpdater 
{
    /**
     * @param $initialVersion
     * @return string $versionUpdatedTo
     * @internal param string $currentVersion
     */
    public function update($initialVersion) {
        
        $currentVersion = $initialVersion;
		if ($currentVersion == '2.6' || $currentVersion == '2.6.1'  || $currentVersion == '2.7') {
			$currentVersion = '2.7.1';
		}

		$this->setVersion($currentVersion);

        $this->skip('2.7.1', '2.10.3');

		return null;
	}
}
