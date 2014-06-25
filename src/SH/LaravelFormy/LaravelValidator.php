<?php
namespace SH\LaravelFormy;

use SH\Formy\ValidationInterface;
use SH\Formy\Form;
use Validator;
use Session;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Contracts\MessageProviderInterface;

class LaravelValidator implements ValidationInterface, MessageProviderInterface
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
        $this->populateErrorsFromSession();
    }

    public function populateErrorsFromSession()
    {
        $errors = Session::get('errors');
        
        if (!$errors)
            return false;

        $this->messages = $errors->toArray();
        $this->prepareForm();
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

        $this->prepareForm();

        return $this->validator->passes();
    }

    public function getMessageBag()
    {
        return new MessageBag($this->messages);
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
        if (empty($this->attribute_names)) {
            foreach ($this->form->getFieldsets() as $fieldset_name => $fieldset) {

                foreach ($fieldset->getElements() as $element_name => $element)
                    array_set($this->attribute_names, "{$fieldset_name}.{$element_name}", $element->getLabel());

            }
        }

        return array_dot($this->attribute_names);
    }

    protected function prepareForm()
    {
        foreach ($this->form->getFieldsets() as $fieldset_name => $fieldset) {
            
            $messages = $this->getFieldsetErrors($fieldset_name);

            $this->prepareFormFieldset($fieldset, $messages);

        }
    }

    protected function prepareFormFieldset($fieldset, $messages)
    {
        //foreach element in the fieldset
        foreach ($fieldset->getElements() as $element_name => $element) {
            //set error messages
            if ($element_errors = array_get($messages, $element_name))
                $element->setMeta('errors', $element_errors);
        }
    }
}