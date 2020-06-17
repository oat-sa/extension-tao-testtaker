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
 * Class TestTakerImportedEvent
 * @package oat\taoTestTaker\models\events
 */
class TestTakerImportedEvent extends AbstractTestTakerEvent implements WebhookSerializableEventInterface
{
    private const WEBHOOK_EVENT_NAME = 'test-taker-imported';

    /**
     * @inheritDoc
     */
    public function getWebhookEventName()
    {
        return self::WEBHOOK_EVENT_NAME;
    }

    /**
     * @inheritDoc
     */
    public function serializeForWebhook()
    {
        return [
            'testTakerUri' => $this->testTakerUri,
            'unit' => 1
        ];
    }
}
