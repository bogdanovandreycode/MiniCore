<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\Field;
use MiniCore\Form\FieldInterface;
use MiniCore\UI\AssetManager;

/**
 * Class WysiwygField
 *
 * A WYSIWYG editor for entering formatted text.
 * Supports TinyMCE and other editors.
 * 
 * @example Using editor field with form 
 * $form = new FormBuilder('/submit', 'POST');
 * $form->addField(new WysiwygField('content', 'Description', '', [], 'tiny'));
 * echo $form->render();
 * echo AssetManager::renderStyles();
 * echo AssetManager::renderScripts();
 */
class WysiwygField extends Field implements FieldInterface
{
    /**
     * @var string Editor type (only TinyMCE is supported for now).
     */
    private string $editor;

    /**
     * @var string Unique ID for the editor instance.
     */
    private string $editorId;

    /**
     * WysiwygField constructor.
     *
     * @param string $name Field name.
     * @param string $label Label.
     * @param mixed $value Value.
     * @param array $attributes Additional attributes.
     * @param string $editor Which WYSIWYG to use (only "tiny" supported).
     */
    public function __construct(
        string $name,
        string $label = '',
        mixed $value = '',
        array $attributes = [],
        string $editor = 'tiny'
    ) {
        parent::__construct($name, $label, $value, $attributes);
        $this->editor = $editor;
        $this->editorId = 'wysiwyg_' . uniqid(); // Generate unique ID
    }

    /**
     * Render the WYSIWYG field.
     *
     * @return string The HTML code of the field.
     */
    public function render(): string
    {
        // Register TinyMCE assets (only once)
        $this->registerAssets();

        $attributes = $this->buildAttributes();
        return sprintf(
            '<textarea name="%s" id="%s" %s>%s</textarea>',
            htmlspecialchars($this->name),
            htmlspecialchars($this->editorId),
            $attributes,
            htmlspecialchars($this->value)
        ) . PHP_EOL . $this->getEditorScript();
    }

    /**
     * Registers required scripts and styles via AssetManager.
     *
     * @return void
     */
    private function registerAssets(): void
    {
        if ($this->editor === 'tiny') {
            AssetManager::addScript('tinymce', '/assets/js/tinymce/tinymce.min.js');
            AssetManager::addStyle('tinymce-skin', '/assets/js/tinymce/skins/ui/oxide/skin.min.css');
        }
    }

    /**
     * Returns the JS initialization script for the editor.
     *
     * @return string
     */
    private function getEditorScript(): string
    {
        if ($this->editor === 'tiny') {
            return sprintf(
                '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        if (typeof tinymce !== "undefined") {
                            tinymce.init({
                                selector: "#%s",
                                skin: "oxide",
                                content_css: "oxide",
                                plugins: "link image code table",
                                toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | link image | code"
                            });
                        }
                    });
                </script>',
                htmlspecialchars($this->editorId)
            );
        }

        return '';
    }
}
