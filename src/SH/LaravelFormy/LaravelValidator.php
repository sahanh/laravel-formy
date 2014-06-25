<?php
namespace SH\LaravelFormy;

use SH\Formy\ValidationInterface;
use SH\Formy\Form;
use Validator;

class LaravelValidator implements ValidationInterface
{
    protected $data;

    protected $form;

    protected $rules;

    protected $validator;

    protected $messages;

    protected $attribute_names;

    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    public function setRules($rules)
    {
        $this->rules = $rules;
        return $this;
    }

    public function validate()
    {
        $this->validator = Validator::make($this->form->getData(), array_dot($this->rules));
        $this->validator->setAttributeNames($this->getAttributeNames());
        
        foreach ($this->validator->messages()->toArray() as $key => $errors)
            array_set($this->messages, $key, $errors);

        return $this->validator->passes();
    }

    public function getErrors()
    {
        return $this->messages;
    }

    public function getFieldsetErrors($fieldset_name)
    {
        return array_get($this->messages, $fieldset_name);
    }

    protected function getAttributeNames()
    {
        foreach ($this->form->getFieldsets() as $fieldset_name => $fieldset) {

            foreach ($fieldset->getElements() as $element_name => $element)
                array_set($this->attribute_names, "{$fieldset_name}.{$element_name}", $element->getLabel());

        }

        return array_dot($this->attribute_names);
    }
}