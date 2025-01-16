<?php

namespace MiniCore\Tests\Form;

use PHPUnit\Framework\TestCase;
use MiniCore\Form\FormBuilder;
use MiniCore\Form\Fields\TextField;
use MiniCore\Form\Fields\SelectField;

/**
 * Class FormBuilderTest
 *
 * Unit tests for the FormBuilder class with real field classes.
 */
class FormBuilderTest extends TestCase
{
    /**
     * Проверка инициализации формы с правильными action и method.
     */
    public function testFormInitialization(): void
    {
        $form = new FormBuilder('/submit', 'post');
        $expectedHtml = '<form action="/submit" method="POST" ></form>';

        $this->assertEquals($expectedHtml, $form->render(), 'Форма была инициализирована некорректно.');
    }

    /**
     * Проверка добавления текстового поля в форму.
     */
    public function testAddTextField(): void
    {
        $textField = new TextField('username', 'JohnDoe', ['class' => 'form-control']);
        $form = new FormBuilder('/submit', 'POST');
        $form->addField($textField);

        $expectedHtml = '<form action="/submit" method="POST" ><input type="text" name="username" value="JohnDoe" class="form-control"/></form>';

        $this->assertEquals($expectedHtml, $form->render(), 'Текстовое поле было добавлено или отрендерено некорректно.');
    }

    /**
     * Проверка добавления выпадающего списка в форму.
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

        $expectedHtml = <<<HTML
<form action="/submit" method="POST" ><select name="gender" class="form-select"><option value="male" selected>Male</option><option value="female" >Female</option><option value="other" >Other</option></select></form>
HTML;

        $this->assertEquals($expectedHtml, $form->render(), 'Выпадающий список был добавлен некорректно.');
    }

    /**
     * Проверка добавления группы полей (fieldset).
     */
    public function testAddGroup(): void
    {
        $firstNameField = new TextField('first_name', '', ['placeholder' => 'First Name']);
        $lastNameField = new TextField('last_name', '', ['placeholder' => 'Last Name']);

        $form = new FormBuilder('/submit', 'POST');
        $form->addGroup('User Info', [$firstNameField, $lastNameField]);

        $expectedHtml = <<<HTML
<form action="/submit" method="POST" ><fieldset><legend>User Info</legend><input type="text" name="first_name" value="" placeholder="First Name"/><input type="text" name="last_name" value="" placeholder="Last Name"/></fieldset></form>
HTML;

        $this->assertEquals($expectedHtml, $form->render(), 'Группа полей была добавлена некорректно.');
    }

    /**
     * Проверка добавления кнопки отправки формы.
     */
    public function testAddSubmitButton(): void
    {
        $form = new FormBuilder('/submit', 'POST');
        $form->addSubmitButton('Send', ['class' => 'btn btn-primary']);

        $expectedHtml = '<form action="/submit" method="POST" ><button type="submit" class="btn btn-primary">Send</button></form>';

        $this->assertEquals($expectedHtml, $form->render(), 'Кнопка отправки была добавлена некорректно.');
    }

    /**
     * Проверка добавления пользовательских атрибутов к форме.
     */
    public function testSetAttribute(): void
    {
        $form = new FormBuilder('/submit', 'POST');
        $form->setAttribute('class', 'custom-form')->setAttribute('id', 'form-id');

        $expectedHtml = '<form action="/submit" method="POST" class="custom-form" id="form-id"></form>';

        $this->assertEquals($expectedHtml, $form->render(), 'Атрибуты формы были добавлены некорректно.');
    }

    /**
     * Проверка добавления произвольного HTML к форме.
     */
    public function testAddRawHtml(): void
    {
        $form = new FormBuilder('/submit', 'POST');
        $form->addField('<p>Custom HTML block</p>');

        $expectedHtml = '<form action="/submit" method="POST" ><p>Custom HTML block</p></form>';

        $this->assertEquals($expectedHtml, $form->render(), 'Произвольный HTML был добавлен некорректно.');
    }

    /**
     * Проверка добавления нескольких элементов в форму.
     */
    public function testAddMultipleFields(): void
    {
        $emailField = new TextField('email', '', ['placeholder' => 'Enter your email']);
        $passwordField = new TextField('password', '', ['type' => 'password', 'placeholder' => 'Enter your password']);

        $form = new FormBuilder('/login', 'POST');
        $form->addField($emailField)
            ->addField($passwordField)
            ->addSubmitButton('Login');

        $expectedHtml = <<<HTML
<form action="/login" method="POST" ><input type="text" name="email" value="" placeholder="Enter your email"/><input type="text" name="password" value="" type="password" placeholder="Enter your password"/><button type="submit" >Login</button></form>
HTML;

        $this->assertEquals($expectedHtml, $form->render(), 'Несколько полей были добавлены некорректно.');
    }

    /**
     * Проверка, что метод по умолчанию — POST.
     */
    public function testDefaultMethodIsPost(): void
    {
        $form = new FormBuilder('/submit');

        $expectedHtml = '<form action="/submit" method="POST" ></form>';

        $this->assertEquals($expectedHtml, $form->render(), 'Метод формы по умолчанию должен быть POST.');
    }
}
