<?php

class AdminPuzzleConfigurationController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        if (Tools::isSubmit('submitPuzzleConfiguration')) {
            $this->processConfiguration();
        }

        $this->content .= $this->renderConfigurationForm();

        parent::initContent();
    }

    protected function processConfiguration()
    {
        $maxFilesize = (int) Tools::getValue('PUZZLE_MAX_FILESIZE');
        $defaultDpi = (int) Tools::getValue('PUZZLE_DEFAULT_DPI');
        $minWidth = (int) Tools::getValue('PUZZLE_MIN_IMAGE_WIDTH');
        $minHeight = (int) Tools::getValue('PUZZLE_MIN_IMAGE_HEIGHT');

        if ($maxFilesize < 1 || $maxFilesize > 500) {
            $this->errors[] = $this->l('File size must be between 1 and 500 MB');
            return;
        }

        if ($defaultDpi < 72 || $defaultDpi > 600) {
            $this->errors[] = $this->l('DPI must be between 72 and 600');
            return;
        }

        Configuration::updateValue('PUZZLE_MAX_FILESIZE', $maxFilesize);
        Configuration::updateValue('PUZZLE_DEFAULT_DPI', $defaultDpi);
        Configuration::updateValue('PUZZLE_MIN_IMAGE_WIDTH', $minWidth ? $minWidth : 1000);
        Configuration::updateValue('PUZZLE_MIN_IMAGE_HEIGHT', $minHeight ? $minHeight : 1000);

        $this->confirmations[] = $this->l('Configuration updated successfully');
    }

    protected function renderConfigurationForm()
    {
        $fieldsForm = [
            'form' => [
                'legend' => [
                    'title' => $this->l('General Configuration'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Maximum file size (MB)'),
                        'name' => 'PUZZLE_MAX_FILESIZE',
                        'required' => true,
                        'class' => 'fixed-width-sm',
                        'desc' => $this->l('Maximum size for uploaded images in megabytes'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Default DPI'),
                        'name' => 'PUZZLE_DEFAULT_DPI',
                        'required' => true,
                        'class' => 'fixed-width-sm',
                        'desc' => $this->l('Recommended DPI for print quality (300 is standard)'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Minimum image width (pixels)'),
                        'name' => 'PUZZLE_MIN_IMAGE_WIDTH',
                        'class' => 'fixed-width-sm',
                        'desc' => $this->l('Minimum recommended width for uploaded images'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Minimum image height (pixels)'),
                        'name' => 'PUZZLE_MIN_IMAGE_HEIGHT',
                        'class' => 'fixed-width-sm',
                        'desc' => $this->l('Minimum recommended height for uploaded images'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'configuration';
        $helper->module = $this->module;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = 'id_configuration';
        $helper->submit_action = 'submitPuzzleConfiguration';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminPuzzleConfiguration', false);
        $helper->token = Tools::getAdminTokenLite('AdminPuzzleConfiguration');

        $helper->fields_value['PUZZLE_MAX_FILESIZE'] = Configuration::get('PUZZLE_MAX_FILESIZE', 50);
        $helper->fields_value['PUZZLE_DEFAULT_DPI'] = Configuration::get('PUZZLE_DEFAULT_DPI', 300);
        $helper->fields_value['PUZZLE_MIN_IMAGE_WIDTH'] = Configuration::get('PUZZLE_MIN_IMAGE_WIDTH', 1000);
        $helper->fields_value['PUZZLE_MIN_IMAGE_HEIGHT'] = Configuration::get('PUZZLE_MIN_IMAGE_HEIGHT', 1000);

        return $helper->generateForm([$fieldsForm]);
    }
}
