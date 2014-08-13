<?php
/**
 * Configuration class to hold data for form generation.
 * The class will be extended when implementing
 */
namespace SH\LaravelFormy;

use InvalidArgumentException;
use OutOfRangeException;
use SH\LaravelFormy\Exception\E;

abstract class FormConfig
{
    protected $template   = 'SH\\LaravelFormy\\Templates\\Basic';

    protected $action     = null;

    protected $attributes = array();

    protected $fields     = array();

    public function getFields()
    {
        return $this->fields;
    }

    public function getFieldsetFields($fieldset_name)
    {
        if (!isset($this->fields[$fieldset_name]))
            throw new E\InvalidFieldsetException("No fields found for the fieldset [$fieldset_name]");
        
        return $this->fields[$fieldset_name];
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getTemplateInstance()
    {
        return new $this->template;
    }

    public function getValidationRules()
    {
        $rules = array();
        
        foreach ($this->fields as $fieldset_name => $fields)
            $rules[$fieldset_name] = $this->getFieldsetValidationRules($fieldset_name);

        return $rules;
    }

    public function getFieldsetValidationRules($fieldset_name)
    {
        $field_set_rules = array();

        foreach ($this->getFieldsetFields($fieldset_name) as $field_name => $field_data) {
            if (isset($field_data['v']))
                $field_set_rules[$field_name] = $field_data['v'];
        } 
            
        
        return $field_set_rules;
    }

    public function ammendFieldAttributes($field_path, array $attributes)
    {
        $field = array_get($this->fields, $field_path);
        if (!$field)
            throw new OutOfRangeException("No field exist for {$field}");

        $existing_attributes = (array) array_get($field, 'a');
        $new_attributes      = array_merge($existing_attributes, $attributes);

        array_set($this->fields, "{$field_path}.a", $new_attributes);
        return $this->fields;
    }
}