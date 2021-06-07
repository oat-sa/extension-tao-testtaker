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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 *
 */

declare(strict_types=1);

namespace oat\taoTestTaker\actions;

use oat\oatbox\event\EventManager;
use oat\taoTestTaker\models\events\TestTakerUpdatedEvent;
use tao_actions_GenerisTree;
use tao_helpers_form_GenerisTreeForm;

class TestTakerGenerisTree extends tao_actions_GenerisTree
{

    /**
     * @see tao_actions_GenerisTree::setValues()
     */
    public function setValues()
    {
        $parentResult = parent::setValues();

        $this->getEventManager()->trigger(new TestTakerUpdatedEvent($this->getRequestParameter('resourceUri'), []));

        return $parentResult;
    }

    /**
     * @see tao_actions_GenerisTree::setReverseValues()
     */
    public function setReverseValues()
    {
        if (!$this->isXmlHttpRequest()) {
            throw new common_exception_IsAjaxAction(__FUNCTION__);
        }

        $values = tao_helpers_form_GenerisTreeForm::getSelectedInstancesFromPost();

        $resource = $this->getResource($this->getRequestParameter('resourceUri'));
        $property = $this->getProperty($this->getRequestParameter('propertyUri'));

        $ttClass = $this->getClass('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
        $instances = $ttClass->searchInstances([
            $property->getUri() => $resource
        ], ['recursive' => true, 'like' => false]);

        $currentValues = array_merge([], array_keys($instances));

        $toAdd = array_diff($values, $currentValues);
        $toRemove = array_diff($currentValues, $values);

        $success = true;
        foreach ($toAdd as $uri) {
            $subject = $this->getResource($uri);
            $success = $success && $subject->setPropertyValue($property, $resource);
        }

        foreach ($toRemove as $uri) {
            $subject = $this->getResource($uri);
            $success = $success && $subject->removePropertyValue($property, $resource);
        }

        $touchedValues = array_merge_recursive($toRemove, $values);
        foreach ($touchedValues as $uri) {
            $this->getEventManager()->trigger(new TestTakerUpdatedEvent($uri, []));
        }

        return $this->returnJson(['saved'  => $success]);
    }

    /**
     * @return EventManager
     */
    private function getEventManager(): EventManager
    {
        return $this->getServiceLocator()->get(EventManager::SERVICE_ID);
    }
}
