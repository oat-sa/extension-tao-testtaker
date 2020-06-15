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
 * Copyright (c) 2016  (original work) Open Assessment Technologies SA;
 *
 * @author Ivan klimchuk <klimchuk@1pt.com>
 */

namespace oat\taoTestTaker\models\events;

use oat\tao\model\webhooks\WebhookSerializableEventInterface;

/**
 * Class TestTakerRemovedEvent
 * @package oat\taoTestTaker\models\events
 */
class TestTakerRemovedEvent extends AbstractTestTakerEvent implements WebhookSerializableEventInterface
{
    const EVENT_NAME = __CLASS__;

    /**
     * @inheritDoc
     */
    public function getWebhookEventName()
    {
        return 'test-taker-removed';
    }

    /**
     * @inheritDoc
     */
    public function serializeForWebhook()
    {
        return [
            'name' => $this->getWebhookEventName(),
            'payload' => [
                'testTakerUri' => $this->testTakerUri,
                'unit' => 1,
                'tenant' => defined('LOCAL_NAMESPACE') ? LOCAL_NAMESPACE : ROOT_URL,
            ],
        ];
    }
}
