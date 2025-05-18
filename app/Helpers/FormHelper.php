<?php

namespace App\Helpers;

class FormHelper
{
    /**
     * @var array Opgeslagen formulierwaarden
     */
    private $values = [];
    
    /**
     * @var array Fouten per veld
     */
    private $errors = [];
    
    /**
     * @var array Algemene fouten (niet gekoppeld aan een veld)
     */
    private $generalErrors = [];
    
    /**
     * @var array Validatieregels per veld
     */
    private $rules = [];
    
    /**
     * Constructor
     * 
     * @param array $values Initiële waarden (optioneel)
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }
    
    /**
     * Stel formulierwaarden in
     * 
     * @param array $values Array met veldnamen en -waarden
     * @return $this
     */
    public function setValues(array $values)
    {
        $this->values = array_merge($this->values, $values);
        return $this;
    }
    
    /**
     * Haal alle formulierwaarden op
     * 
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }
    
    /**
     * Haal één formulierwaarde op
     * 
     * @param string $field Veldnaam
     * @param mixed $default Standaardwaarde als het veld niet bestaat
     * @return mixed
     */
    public function getValue($field, $default = '')
    {
        return $this->values[$field] ?? $default;
    }
    
    /**
     * Voeg een validatieregel toe
     * 
     * @param string $field Veldnaam
     * @param string $rule Regelnaam
     * @param mixed $param Parameter voor de regel (optioneel)
     * @param string $message Aangepast foutbericht (optioneel)
     * @return $this
     */
    public function addRule($field, $rule, $param = null, $message = null)
    {
        $this->rules[$field][] = [
            'rule' => $rule,
            'param' => $param,
            'message' => $message
        ];
        
        return $this;
    }
    
    /**
     * Valideer formulier op basis van regels
     * 
     * @param array $data Data om te valideren (standaard $_POST)
     * @return bool True als validatie succesvol is
     */
    public function validate(array $data = null)
    {
        if ($data === null) {
            $data = $_POST;
        }
        
        $this->values = $data;
        $this->errors = [];
        $this->generalErrors = [];
        
        foreach ($this->rules as $field => $fieldRules) {
            foreach ($fieldRules as $ruleData) {
                $rule = $ruleData['rule'];
                $param = $ruleData['param'];
                $value = $data[$field] ?? null;
                
                // Valideer op basis van regeltype
                $valid = true;
                $message = '';
                
                switch ($rule) {
                    case 'required':
                        $valid = !empty($value);
                        $message = $ruleData['message'] ?? "Dit veld is verplicht.";
                        break;
                        
                    case 'email':
                        $valid = empty($value) || filter_var($value, FILTER_VALIDATE_EMAIL);
                        $message = $ruleData['message'] ?? "Ongeldig e-mailadres.";
                        break;
                        
                    case 'min':
                        $valid = empty($value) || strlen($value) >= $param;
                        $message = $ruleData['message'] ?? "Dit veld moet minimaal {$param} tekens bevatten.";
                        break;
                        
                    case 'max':
                        $valid = empty($value) || strlen($value) <= $param;
                        $message = $ruleData['message'] ?? "Dit veld mag maximaal {$param} tekens bevatten.";
                        break;
                        
                    case 'matches':
                        $valid = $value === ($data[$param] ?? null);
                        $message = $ruleData['message'] ?? "Dit veld moet overeenkomen met {$param}.";
                        break;
                        
                    case 'regex':
                        $valid = empty($value) || preg_match($param, $value);
                        $message = $ruleData['message'] ?? "Dit veld heeft een ongeldig formaat.";
                        break;
                        
                    case 'unique':
                        // Param moet een array zijn: [Database $db, string $table, string $column, ?int $excludeId]
                        if (is_array($param) && count($param) >= 3) {
                            $db = $param[0];
                            $table = $param[1];
                            $column = $param[2];
                            $excludeId = $param[3] ?? null;
                            
                            if (empty($value)) {
                                $valid = true;
                            } else {
                                $query = "SELECT id FROM {$table} WHERE {$column} = ?";
                                $queryParams = [$value];
                                
                                if ($excludeId) {
                                    $query .= " AND id != ?";
                                    $queryParams[] = $excludeId;
                                }
                                
                                $result = $db->fetch($query, $queryParams);
                                $valid = empty($result);
                            }
                            
                            $message = $ruleData['message'] ?? "Deze waarde is al in gebruik.";
                        } else {
                            // Ongeldige parameter, overslaan
                            $valid = true;
                        }
                        break;
                        
                    case 'callback':
                        // Param moet een callable zijn
                        if (is_callable($param)) {
                            $valid = call_user_func($param, $value, $data);
                            $message = $ruleData['message'] ?? "Dit veld bevat een ongeldige waarde.";
                        } else {
                            // Ongeldige parameter, overslaan
                            $valid = true;
                        }
                        break;
                }
                
                if (!$valid) {
                    $this->errors[$field][] = $message;
                    break; // Stop met valideren van dit veld na eerste fout
                }
            }
        }
        
        return empty($this->errors) && empty($this->generalErrors);
    }
    
    /**
     * Voeg een algemene fout toe
     * 
     * @param string $message Foutbericht
     * @return $this
     */
    public function addError($message)
    {
        $this->generalErrors[] = $message;
        return $this;
    }
    
    /**
     * Voeg een fout toe voor een specifiek veld
     * 
     * @param string $field Veldnaam
     * @param string $message Foutbericht
     * @return $this
     */
    public function addFieldError($field, $message)
    {
        $this->errors[$field][] = $message;
        return $this;
    }
    
    /**
     * Heeft het formulier fouten?
     * 
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors) || !empty($this->generalErrors);
    }
    
    /**
     * Haal alle fouten op
     * 
     * @return array
     */
    public function getErrors()
    {
        return [
            'fields' => $this->errors,
            'general' => $this->generalErrors
        ];
    }
    
    /**
     * Haal alle fouten op voor een veld
     * 
     * @param string $field Veldnaam
     * @return array
     */
    public function getFieldErrors($field)
    {
        return $this->errors[$field] ?? [];
    }
    
    /**
     * Haal alle algemene fouten op
     * 
     * @return array
     */
    public function getGeneralErrors()
    {
        return $this->generalErrors;
    }
    
    /**
     * Heeft een veld fouten?
     * 
     * @param string $field Veldnaam
     * @return bool
     */
    public function hasFieldError($field)
    {
        return !empty($this->errors[$field]);
    }
    
    /**
     * Genereer een input veld
     * 
     * @param string $type Type input (text, email, password, etc.)
     * @param string $name Veldnaam
     * @param array $attributes Extra attributen (class, id, etc.)
     * @return string HTML
     */
    public function input($type, $name, array $attributes = [])
    {
        $value = $this->getValue($name);
        $hasError = $this->hasFieldError($name);
        
        $attrs = [];
        foreach ($attributes as $key => $val) {
            $attrs[] = "{$key}=\"" . htmlspecialchars($val) . "\"";
        }
        
        $attrString = !empty($attrs) ? ' ' . implode(' ', $attrs) : '';
        $errorClass = $hasError ? ' form-control-error' : '';
        
        $result = "<input type=\"{$type}\" name=\"{$name}\" value=\"" . htmlspecialchars($value) . "\" class=\"form-control{$errorClass}\"{$attrString}>";
        
        if ($hasError) {
            $result .= $this->showFieldErrors($name);
        }
        
        return $result;
    }
    
    /**
     * Genereer een textarea veld
     * 
     * @param string $name Veldnaam
     * @param array $attributes Extra attributen (class, id, etc.)
     * @return string HTML
     */
    public function textarea($name, array $attributes = [])
    {
        $value = $this->getValue($name);
        $hasError = $this->hasFieldError($name);
        
        $attrs = [];
        foreach ($attributes as $key => $val) {
            $attrs[] = "{$key}=\"" . htmlspecialchars($val) . "\"";
        }
        
        $attrString = !empty($attrs) ? ' ' . implode(' ', $attrs) : '';
        $errorClass = $hasError ? ' form-control-error' : '';
        
        $result = "<textarea name=\"{$name}\" class=\"form-control{$errorClass}\"{$attrString}>" . htmlspecialchars($value) . "</textarea>";
        
        if ($hasError) {
            $result .= $this->showFieldErrors($name);
        }
        
        return $result;
    }
    
    /**
     * Genereer een select veld
     * 
     * @param string $name Veldnaam
     * @param array $options Opties (value => label)
     * @param array $attributes Extra attributen (class, id, etc.)
     * @return string HTML
     */
    public function select($name, array $options, array $attributes = [])
    {
        $value = $this->getValue($name);
        $hasError = $this->hasFieldError($name);
        
        $attrs = [];
        foreach ($attributes as $key => $val) {
            $attrs[] = "{$key}=\"" . htmlspecialchars($val) . "\"";
        }
        
        $attrString = !empty($attrs) ? ' ' . implode(' ', $attrs) : '';
        $errorClass = $hasError ? ' form-control-error' : '';
        
        $result = "<select name=\"{$name}\" class=\"form-control{$errorClass}\"{$attrString}>";
        
        foreach ($options as $optValue => $label) {
            $selected = ($value == $optValue) ? ' selected' : '';
            $result .= "<option value=\"" . htmlspecialchars($optValue) . "\"{$selected}>" . htmlspecialchars($label) . "</option>";
        }
        
        $result .= "</select>";
        
        if ($hasError) {
            $result .= $this->showFieldErrors($name);
        }
        
        return $result;
    }
    
    /**
     * Genereer een radio button
     * 
     * @param string $name Veldnaam
     * @param string $value Waarde
     * @param array $attributes Extra attributen (class, id, etc.)
     * @return string HTML
     */
    public function radio($name, $value, array $attributes = [])
    {
        $checked = ($this->getValue($name) == $value) ? ' checked' : '';
        
        $attrs = [];
        foreach ($attributes as $key => $val) {
            $attrs[] = "{$key}=\"" . htmlspecialchars($val) . "\"";
        }
        
        $attrString = !empty($attrs) ? ' ' . implode(' ', $attrs) : '';
        
        return "<input type=\"radio\" name=\"{$name}\" value=\"" . htmlspecialchars($value) . "\"{$checked}{$attrString}>";
    }
    
    /**
     * Genereer een checkbox
     * 
     * @param string $name Veldnaam
     * @param string $value Waarde
     * @param array $attributes Extra attributen (class, id, etc.)
     * @return string HTML
     */
    public function checkbox($name, $value, array $attributes = [])
    {
        $formValue = $this->getValue($name);
        $checked = '';
        
        if (is_array($formValue)) {
            $checked = in_array($value, $formValue) ? ' checked' : '';
        } else {
            $checked = ($formValue == $value) ? ' checked' : '';
        }
        
        $attrs = [];
        foreach ($attributes as $key => $val) {
            $attrs[] = "{$key}=\"" . htmlspecialchars($val) . "\"";
        }
        
        $attrString = !empty($attrs) ? ' ' . implode(' ', $attrs) : '';
        
        return "<input type=\"checkbox\" name=\"{$name}\" value=\"" . htmlspecialchars($value) . "\"{$checked}{$attrString}>";
    }
    
    /**
     * Toon foutmeldingen voor een veld
     * 
     * @param string $field Veldnaam
     * @return string HTML
     */
    public function showFieldErrors($field)
    {
        $errors = $this->getFieldErrors($field);
        
        if (empty($errors)) {
            return '';
        }
        
        $html = '<div class="form-error-messages">';
        foreach ($errors as $error) {
            $html .= '<div class="form-error-message">' . htmlspecialchars($error) . '</div>';
        }
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Toon alle algemene foutmeldingen
     * 
     * @return string HTML
     */
    public function showGeneralErrors()
    {
        $errors = $this->getGeneralErrors();
        
        if (empty($errors)) {
            return '';
        }
        
        $html = '<div class="alert alert-danger"><ul>';
        foreach ($errors as $error) {
            $html .= '<li>' . htmlspecialchars($error) . '</li>';
        }
        $html .= '</ul></div>';
        
        return $html;
    }
}