<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\Field;
use MiniCore\Form\FieldInterface;

/**
 * Class WysiwygField
 *
 * Is a WYSIWYG editor for entering formatted text.
 * upports TinyMCE, Fckeditor and other editors.
 */
class WysiwygField extends Field implements FieldInterface
{
    /**
     * @var string Specifying an editor (tinymce, ckeditor, quill)
     */
    private string $editor;

    /**
     * WysiwygField constructor.
     *
     * @param string $name is the name of the field.
     * @param string $label Label.
     * @param mixed $value Value.
     * @param array $attributes Additional attributes.
     * @param string $editor Which WYSIWYG to use (tiny, ckeditor, quill).
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
    }

    /**
     * Render the WYSIWYG field.
     *
     * @return string The HTML code of the field.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();
        $html = sprintf(
            '<textarea name="%s" id="%s" %s>%s</textarea>',
            htmlspecialchars($this->name),
            htmlspecialchars($this->name),
            $attributes,
            htmlspecialchars($this->value)
        );

        $script = $this->getEditorScript();

        return $html . $script;
    }

    /**
     * Returns the JS code for initializing the editor.
     *
     * @return string
     */
    private function getEditorScript(): string
    {
        switch ($this->editor) {
            case 'tiny':
                return '<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
                <script>tinymce.init({ selector: "#' . htmlspecialchars($this->name) . '" });</script>';

            case 'ckeditor':
                return '<script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
                <script>CKEDITOR.replace("' . htmlspecialchars($this->name) . '");</script>';

            case 'quill':
                return '<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
                <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
                <div id="editor-container"></div>
                <script>
                    var quill = new Quill("#editor-container", { theme: "snow" });
                    document.querySelector("form").addEventListener("submit", function() {
                        document.getElementById("' . htmlspecialchars($this->name) . '").value = quill.root.innerHTML;
                    });
                </script>';

            default:
                return '';
        }
    }
}
