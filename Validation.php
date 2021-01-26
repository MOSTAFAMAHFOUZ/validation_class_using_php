<?php 

namespace app\core;


class  Validation 
{
    public $errors = [];

    // rules for validations 
    const F_REQUIRED = "required";
    const F_EMAIL = "email";
    const F_STRING = "string";
    const F_MOBILE_EG = "mobile_eg";
    const F_NUMBER = "number";
    const F_MIN = "min";
    const F_MAX = "max";
    const F_IN = "in";
    const F_SAME = "same";




    /**
     * 
     * split errors to (string rules and array rules)
     * if error occure , loop will break 
     * added errors to error propery if 
     */

    public function validate($data)
    {
        foreach ($data as $name => $rules) {
            foreach($rules as $rule)
            {
                $realRule = explode(":",$rule);
                if(!strpos($rule,":"))
                {
                    $this->validStringField($name,$rule);
                }
                if(count($realRule) > 1 )
                {
                    $this->validArrayField($name,$realRule);
                }
            }

            // break from loop if error occuard
            if(count($this->errors))
            {
                break;
            }
        }

        return $this;

    }

    // return true  if errors not exists 
    public function check()
    {
        return count($this->errors) ? false : true;
    }



    /**
     * string rules 
     */

    private function validStringField($name,$rule)
    {
        switch ($rule) {
            case self::F_REQUIRED:
                $this->requiredFiled($name);
                break;
            case self::F_EMAIL:
                $this->emailFiled($name);
                break;
            case self::F_STRING:
                $this->stringFiled($name);
                break;
            case self::F_NUMBER:
                $this->numberFiled($name);
                break;
        }
    }



    /**
     * array rules
     */
    private function validArrayField($name,$rule)
    {
        switch ($rule[0]) {
            case self::F_MIN:
                $this->minFiled($name,$rule);
                break;
            case self::F_MAX:
                $this->maxFiled($name,$rule);
                break;
            case self::F_SAME:
                $this->sameFiled($name,$rule);
                break;
            case self::F_IN:
                $this->inArrayFiled($name,$rule);
                break;
        }
    }










    // sanitize any value for field

    private function sanitizeField($name)
    {
        return htmlspecialchars(filter_var(trim($_POST[$name]),FILTER_SANITIZE_STRING));
    }
    // check if field is required or not 
    private function requiredFiled($name)
    {
        if(!empty($this->sanitizeField($name)))
        {
            return true;
        }
        else 
        {
            $name= str_replace('_',' ', $name);
            $this->errors[] = "{$name} is required ";
        }
    }


    // check if value is email or not 
    private function emailFiled($name)
    {
        if(!filter_var($this->sanitizeField($name),FILTER_VALIDATE_EMAIL))
        {
            $this->errors[] = "{$name} must be a valid email";
        }
    }



    // check if value is string or not 
    private function stringFiled($name)
    {
        if(!preg_match('/^[a-zA-z0-9 .]*$/',$this->sanitizeField($name)))
        {
            $name= str_replace('_',' ', $name);
            $this->errors[] = "{$name} must be a string";
        }

    }


    // check if value is number or not 
    private function numberFiled($name)
    {
        if(!preg_match('/^[0-9]+$/',$this->sanitizeField($name)))
        {
            $name= str_replace('_',' ', $name);
            $this->errors[] = "{$name} must be a number";
        }
    }



     // check if value is equal to another value 

    private function sameFiled($name,$rule)
    {
        if($this->sanitizeField($name) !== $this->sanitizeField($rule[1]))
        {
            $name= str_replace('_',' ', $name);
            $this->errors[] = "{$name} must be equal  {$rule[1]}";
        }
    } 











    //  array rules 


    //  check  minimum of value
    private function minFiled($name,$rule)
    {
        if(strlen($this->sanitizeField($name)) < $rule[1])
        {
            $name= str_replace('_',' ', $name);
            $this->errors[] = "{$name} must be greater than  {$rule[1]}";
        }
    } 



    // check maximum of value
    private function maxFiled($name,$rule)
    {
        if(strlen($this->sanitizeField($name)) > $rule[1])
        {
            $name= str_replace('_',' ', $name);
            $this->errors[] = "{$name} must be less than  {$rule[1]}";
        }
    } 

     // check maximum of value
     private function inArrayFiled($name,$rule)
     {
         $array = explode(',',$rule[1]);
         if(! in_array($this->sanitizeField($name),$array) )
         {
             $name= str_replace('_',' ', $name);
             $this->errors[] = "{$name} not valid";
         }
     } 


    // echo "<pre>";
    //     var_dump($data);
    // echo "</pre>";
    // die("dfgdsfgdfg");


}