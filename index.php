<?php

/**
 * @defgroup plugins_pubIds_ark ARK Pub ID Plugin
 */

/**
 * @file plugins/pubIds/ark/index.php
 *
 * Copyright (c) 2021 Yasiel Pérez Vera
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_pubIds_ark
 * @brief Wrapper for urn plugin.
 *
 */
require_once('ARKPubIdPlugin.inc.php');

return new ARKPubIdPlugin();


