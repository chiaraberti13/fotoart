<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_1($module)
{
    if (!method_exists($module, 'ensureCustomizationTableIndexes')) {
        return true;
    }

    return (bool) $module->ensureCustomizationTableIndexes();
}

