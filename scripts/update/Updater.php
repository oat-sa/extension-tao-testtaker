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

use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\user\TaoRoles;
use oat\taoTestTaker\actions\Api;
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
        
        $this->skip('2.6', '3.0.0');
        // fix anonymous access
        if ($this->isVersion('3.0.0')) {
            AclProxy::revokeRule(new AccessRule(AccessRule::GRANT, TaoRoles::ANONYMOUS, Api::class));
            $this->setVersion('3.0.1');
        }
        $this->skip('3.0.1', '3.0.2');
	}
}
