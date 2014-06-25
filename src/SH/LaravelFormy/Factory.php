<?php
namespace SH\LaravelFormy;

use SH\Formy\Form;
use SH\Formy\Fieldset;
use SH\Formy\Input\Element;

class Factory
{

    public function make($config)
    {
        if (is_string($config))
            $config = new $config;

        return $this->makeFormConfig($config);
    }

    public function makeFormConfig(FormConfig $config)
    {
        $validation_rules = $config->getValidationRules();
        $val = new LaravelValidator;
        $val->setRules($validation_rules);

        $form = new Form;
        $form->setAttribute('action', $config->getAction());
        $form->setTemplate($config->getTemplateInstance());

        $fields_data = $config->getFields();

        foreach ($fields_data as $fieldset_name => $fields) {

            $fs = new Fieldset;
            $fs->setName($fieldset_name);

            foreach ($fields as $field_name => $field_data) {

                $element = new Element($field_name, $field_data['type'], array_get($field_data, 'a', array()));
                $element->setLabel(array_get($field_data, 'l'));

                if (array_get($field_data, 'a'))
                    $element->setAttributes(array_get($field_data, 'a'));

                if ($descrip = array_get($field_data, 'description'))
                    $element->setMeta('description', $descrip);

                $fs->addElement($element);

            }

            $form->addFieldset($fs);
        }

        $form->setValidator($val);

        return $form;
    }
}