<?php
namespace SH\LaravelFormy\Templates;

use SH\Formy\Form as FormyForm;
use SH\Formy\TemplateInterface;
use Illuminate\Support\Facades\Form;

class Basic implements TemplateInterface
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
        $h = Form::open($this->getForm()->getAttributes());

        foreach ($this->getForm()->getFieldsets() as $fieldset) {

            foreach ($fieldset->getElements() as $field) {
                $h .= $this->renderWrappedElement($field);
            }

        }
        $h .= Form::submit('Submit', array('class' => 'btn btn-primary'));
        $h .= Form::close();
        return $h;
    }

    public function renderElement($element)
    {
        if (is_string($element))
            $e = $this->form->getElement($element);
        else
            $e = $element;

        $type  = $e->getType();
        $atts  = $e->getAttributes();

        $atts['class'] = array_get($atts, 'class').' '.'form-control';

        $render   = [];

        switch ($type) {
            case 'text':
            case 'textarea':
            case 'password':
            case 'hidden':
                    $render[] = Form::label($e->getName(), $e->getLabel());
                    $render[] = Form::{$type}($e->getName(), $e->getValue(), $atts);
                break;
            
            case 'checkbox':
            case 'radio':

                break;

            case 'select':
                    $render[] = Form::label($e->getName(), $e->getLabel());
                    $render[] = Form::select($e->getName(), (array) $e->getAttribute('options'), $e->getValue(), array_except($atts, ['options']));
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