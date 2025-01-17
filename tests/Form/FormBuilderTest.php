<?php

namespace MiniCore\Tests\Form;

use PHPUnit\Framework\TestCase;
use MiniCore\Form\FormBuilder;
use MiniCore\Form\Fields\TextField;
use MiniCore\Form\Fields\SelectField;

/**
 * Unit tests for the FormBuilder class.
 *
 * This test suite verifies the correct behavior of the FormBuilder class,
 * ensuring that forms are built and rendered properly with various types of fields and configurations.
 */
class FormBuilderTest extends TestCase
{
    /**
     * Helper method to load expected HTML from a file.
     *
     * @param string $filename Name of the HTML file in the Data directory.
     * @return string Contents of the HTML file.
     */
    private function loadExpectedHtml(string $filename): string
    {
        return file_get_contents(__DIR__ . "/StaticData/" . $filename);
    }

    /**
     * Tests form initialization with correct action and method.
     */
    public function testFormInitialization(): void
    {
        $form = new FormBuilder('/submit', 'post');
        $expectedHtml = $this->loadExpectedHtml('form_initialization.html');

        $this->assertEquals($expectedHtml, $form->render(), 'Form was not initialized correctly.');
    }

    /**
     * Tests adding a text field to the form.
     */
    public function testAddTextField(): void
    {
        $textField = new TextField('username', 'JohnDoe', ['class' => 'form-control']);
        $form = new FormBuilder('/submit', 'POST');
        $form->addField($textField);

        $expectedHtml = $this->loadExpectedHtml('add_text_field.html');

        $this->assertEquals($expectedHtml, $form->render(), 'Text field was not added or rendered correctly.');
    }

    /**
     * Tests adding a select field to the form.
     */
    public function testAddSelectField(): void
    {
        $selectField = new SelectField('gender', 'male', [
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other'
        ], ['class' => 'form-select']);

        $form = new FormBuilder('/submit', 'POST');
        $form->addField($selectField);

        $expectedHtml = $this->loadExpectedHtml('add_select_field.html');

        $this->assertEquals($expectedHtml, $form->render(), 'Select field was not added correctly.');
    }

    /**
     * Tests adding a fieldset group to the form.
     */
    public function testAddGroup(): void
    {
        $firstNameField = new TextField('first_name', '', ['placeholder' => 'First Name']);
        $lastNameField = new TextField('last_name', '', ['placeholder' => 'Last Name']);

        $form = new FormBuilder('/submit', 'POST');
        $form->addGroup('User Info', [$firstNameField, $lastNameField]);

        $expectedHtml = $this->loadExpectedHtml('add_group.html');

        $this->assertEquals($expectedHtml, $form->render(), 'Field group was not added correctly.');
    }

    /**
     * Tests adding a submit button to the form.
     */
    public function testAddSubmitButton(): void
    {
        $form = new FormBuilder('/submit', 'POST');
        $form->addSubmitButton('Send', ['class' => 'btn btn-primary']);

        $expectedHtml = $this->loadExpectedHtml('add_submit_button.html');

        $this->assertEquals($expectedHtml, $form->render(), 'Submit button was not added correctly.');
    }

    /**
     * Tests applying custom attributes to the form.
     */
    public function testSetAttribute(): void
    {
        $form = new FormBuilder('/submit', 'POST');
        $form->setAttribute('class', 'custom-form')->setAttribute('id', 'form-id');

        $expectedHtml = $this->loadExpectedHtml('set_attribute.html');

        $this->assertEquals($expectedHtml, $form->render(), 'Form attributes were not added correctly.');
    }

    /**
     * Tests adding raw HTML content to the form.
     */
    public function testAddRawHtml(): void
    {
        $form = new FormBuilder('/submit', 'POST');
        $form->addField('<p>Custom HTML block</p>');

        $expectedHtml = $this->loadExpectedHtml('add_raw_html.html');

        $this->assertEquals($expectedHtml, $form->render(), 'Raw HTML was not added correctly.');
    }

    /**
     * Tests adding multiple fields to the form.
     */
    public function testAddMultipleFields(): void
    {
        $emailField = new TextField('email', '', ['placeholder' => 'Enter your email']);
        $passwordField = new TextField('password', '', ['type' => 'password', 'placeholder' => 'Enter your password']);

        $form = new FormBuilder('/login', 'POST');
        $form->addField($emailField)
            ->addField($passwordField)
            ->addSubmitButton('Login');

        $expectedHtml = $this->loadExpectedHtml('add_multiple_fields.html');

        $this->assertEquals($expectedHtml, $form->render(), 'Multiple fields were not added correctly.');
    }

    /**
     * Tests that the default method is POST.
     */
    public function testDefaultMethodIsPost(): void
    {
        $form = new FormBuilder('/submit');
        $expectedHtml = $this->loadExpectedHtml('default_method_post.html');

        $this->assertEquals($expectedHtml, $form->render(), 'Default form method should be POST.');
    }
}
