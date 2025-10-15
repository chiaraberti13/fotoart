<?php

class PuzzlecustomizerCustomizerModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();

        $options = $this->loadPuzzleOptions();
        $boxColors = $this->loadBoxColors();
        $textColors = $this->loadTextColors();
        $fonts = $this->loadFonts();

        $this->context->smarty->assign([
            'module_dir' => $this->module->getPathUri(),
            'puzzle_options' => $options,
            'box_colors' => $boxColors,
            'text_colors' => $textColors,
            'fonts' => $fonts,
            'customizer_config' => [
                'upload_url' => $this->context->link->getModuleLink($this->module->name, 'upload'),
                'save_url' => $this->context->link->getModuleLink($this->module->name, 'saveconfig'),
                'preview_url' => $this->context->link->getModuleLink($this->module->name, 'preview'),
                'uploads_url' => $this->module->getPathUri() . 'uploads',
                'csrf_token' => Tools::getToken(false),
            ],
        ]);

        $this->setTemplate('module:puzzlecustomizer/views/templates/front/customizer.tpl');
    }

    protected function loadPuzzleOptions()
    {
        $collection = new PrestaShopCollection('PuzzleOption');
        $collection->where('active', '=', 1);

        $options = [];
        foreach ($collection as $option) {
            $options[] = [
                'id' => (int) $option->id,
                'name' => $option->name,
                'width_mm' => (float) $option->width_mm,
                'height_mm' => (float) $option->height_mm,
                'pieces' => (int) $option->pieces,
                'price_impact' => (float) $option->price_impact,
            ];
        }

        return $options;
    }

    protected function loadBoxColors()
    {
        $collection = new PrestaShopCollection('PuzzleBoxColor');
        $collection->where('active', '=', 1);

        $colors = [];
        foreach ($collection as $color) {
            $colors[] = [
                'id' => (int) $color->id,
                'name' => $color->name,
                'hex' => $color->hex,
            ];
        }

        return $colors;
    }

    protected function loadTextColors()
    {
        $collection = new PrestaShopCollection('PuzzleTextColor');
        $collection->where('active', '=', 1);

        $colors = [];
        foreach ($collection as $color) {
            $colors[] = [
                'id' => (int) $color->id,
                'name' => $color->name,
                'hex' => $color->hex,
            ];
        }

        return $colors;
    }

    protected function loadFonts()
    {
        $collection = new PrestaShopCollection('PuzzleFont');
        $collection->where('active', '=', 1);

        $fonts = [];
        foreach ($collection as $font) {
            $fonts[] = [
                'id' => (int) $font->id,
                'name' => $font->name,
                'file' => $font->file,
            ];
        }

        return $fonts;
    }
}
