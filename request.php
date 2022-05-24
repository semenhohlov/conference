<?php

namespace Request;

class Request
{    
    public function __construct()
    {
        
        // input
        foreach($_REQUEST as $key => $value)
        {
            $this->$key = trim(htmlspecialchars($value));
        }
    }
    public function input($key)
    {
        if (isset($this->$key))
        {
            return $this->$key;
        }
        return null;
    }
}

/*
Rules
required => true / false,
minLength => value,
maxLength => value,
date ? valid => true /false,
time ? valid => true / false.
*/

/*
array $validators [
    ['Название поля' => array $rules],
    ...
];
array $rules = [
    'reqired' => true,
    'minLength' => 2,
    ...
];
*/

class Validator
{
    public bool $isValid;
    public string $errorMessage;
    public function __construct(Request $request, array $validators)
    {
        $this->isValid = true;
        $this->errorMessage = '';
        foreach($validators as $key => $rules)
        {
            foreach ($rules as $rule => $ruleValue)
            {
                switch($rule)
                {
                    case 'required':
                        if (strlen($request->$key) < 1)
                        {
                            $this->isValid = false;
                            $this->errorMessage .= 'Заполните поле '.$key.'.<br>';
                        }
                        break;
                    case 'minLength':
                        if (strlen($request->$key) < $ruleValue)
                        {
                            $this->isValid = false;
                            $this->errorMessage .= 'Поле '.$key.' менше минимального значения '.$ruleValue.'.<br>';
                        }
                        break;
                    case 'maxLength':
                        if (strlen($request->$key) > $ruleValue)
                        {
                            $this->isValid = false;
                            $this->errorMessage .= 'Поле '.$key.' больше максимального значения '.$ruleValue.'.<br>';
                        }
                        break;
                    case 'date':
                        if (!preg_match("/\d{4}-\d{2}-\d{2}/", $request->$key))
                        {
                            $this->isValid = false;
                            $this->errorMessage .= 'Поле '.$key.' должно быть правильной датой. '.$request->$key.'<br>';
                        }
                        break;
                    case 'time':
                        if (!preg_match("/\d{2}:\d{2}/", $request->$key))
                        {
                            $this->isValid = false;
                            $this->errorMessage .= 'Поле '.$key.' должно быть правильным временем.<br>';
                        }
                        break;
                    default:
                    break;
                }
            }
        }
    }
}

?>