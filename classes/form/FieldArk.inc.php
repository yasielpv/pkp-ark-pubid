<?php
/**
 * @file plugins/pubIds/ark/classes/form/FieldUrn.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class FieldUrn
 * @ingroup classes_controllers_form
 *
 * @brief A field for entering a ARK.
 */

namespace Plugins\Generic\ARK;

use PKP\components\forms\FieldText;

class FieldArk extends FieldText
{
    /** @copydoc Field::$component */
    public $component = 'field-ark';

    /** @var string The arkPrefix from the ark plugin sttings */
    public $arkPrefix = '';

    /**
     * @copydoc Field::getConfig()
     */
    public function getConfig()
    {
        $config = parent::getConfig();
        $config['arkPrefix'] = $this->arkPrefix;

        return $config;
    }
}
