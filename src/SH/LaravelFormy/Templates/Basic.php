<?php
namespace SH\LaravelFormy\Templates;

use SH\Formy\Form as FormyForm;
use Illuminate\Support\Facades\Form;

class Basic
{
    protected $form;

    public function setForm(FormyForm $form)
    {
        $this->form = $form;
        return $this;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function render()
    {

        $h = Form::open(['action' => $this->getForm()->getAttribute('action')]);

        foreach ($this->getForm()->getFieldsets() as $fieldset) {

            foreach ($fieldset->getElements() as $field) {
                $h .= $this->renderWrappedElement($field);
            }

        }

        $h .= Form::close();
        return $h;
    }

    public function renderElement($element)
    {
        $e     = $element;
        $type  = $e->getType();

        $render   = [];

        switch ($type) {
            case 'text':
            case 'textarea':
            case 'password':
            case 'hidden':
                    $render[] = Form::label($e->getName(), $e->getLabel());
                    $render[] = Form::{$type}($e->getName(), $e->getValue(), ['class' => 'form-control']);
                break;
            
            case 'checkbox':
            case 'radio':

                break;

            case 'select':
                # code...
                break;

            case 'submit':
                    $render[] = Form::submit($e->getLabel());
                break;
        }

        if ($errors = $e->getMeta('errors')) {
            $render[] = '<p class="text-danger">'.implode('<br />', $errors).'</p>';
        }

        return implode("\n", $render);
    }

    public function renderWrappedElement($element)
    {
        $h  = '<div class="form-group">';
        $h .= $this->renderElement($element);
        $h .= '</div>';

        return $h;
    }

    public function elementHasError($element)
    {
        //$form->getErrors($element);
    }
}