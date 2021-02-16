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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

declare(strict_types=1);

namespace oat\taoTestTaker\models;

use oat\oatbox\service\ConfigurableService;

/**
 * Class TestTakerFormService
 * @package oat\taoTestTaker\models
 */
class TestTakerFormService extends ConfigurableService
{
    const SERVICE_ID = 'taoTestTaker/TestTakerFormService';
    const OPTION_ADDITIONAL_FORM_PROPERTIES = 'additional_form_properties';

    /**
     * @param \core_kernel_classes_Resource $subject
     * @return array
     */
    public function renderAdditionalForms(\core_kernel_classes_Resource $subject): array
    {
        $forms = [];
        $props = $this->getOption(self::OPTION_ADDITIONAL_FORM_PROPERTIES) ?? [];
        foreach ($props as $propUri) {
            $property = new \core_kernel_classes_Property($propUri);
            $groupForm = \tao_helpers_form_GenerisTreeForm::buildTree($subject, $property);
            $forms[] = $groupForm->render();
        }
        return $forms;
    }
}
