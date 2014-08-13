<?php
namespace SH\LaravelFormy;

use SH\Formy\Form;
use SH\Formy\Fieldset;
use SH\Formy\Input\Element;
use SH\Formy\Input\ArrayElement;
use Illuminate\Container\Container;

class Factory
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function make($config)
    {
        if (is_string($config))
            $config = $this->container->make($config);

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
                $element = $this->makeElement($field_name, $field_data);
                $fs->addElement($element);
            }

            $form->addFieldset($fs);
        }

        $form->setValidator($val);

        return $form;
    }

    protected function makeElement($field_name, $field_data)
    {
        $generator = 'SH\Formy\Input\Element';

        if (ends_with($field_name, '[]')) {
            $field_name = str_replace('[]', '', $field_name);
            $generator  = 'SH\Formy\Input\ArrayElement';
        }

        $element = new $generator($field_name, $field_data['type'], array_get($field_data, 'a', array()));
        $element->setLabel(array_get($field_data, 'l'));

        if (array_get($field_data, 'a'))
            $element->setAttributes(array_get($field_data, 'a'));

        if ($descrip = array_get($field_data, 'description'))
            $element->setMeta('description', $descrip);

        return $element;
    }
}