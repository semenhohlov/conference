<?php
/*
шаблон partial (частичный)
@include('имя файла без расширения')
@foreach($arr as $key)
@endforeach
@isset($var)
@endisset
{{@url}}

{{$variableName}}

1. Открываем файл шаблона
2. Читаем построчно
    если строка начинается с @command -> обрабативаем комманду
ГОТОВО    если строка содрежит {{$variableName}} -> заменяем на переменную из массива $arguments
ГОТОВО    @include partials
ГОТОВО    @foreach
ГОТОВО    @isset
ГОТОВО    {{@url}}

*/


namespace View;

class Parser{
    private string $input;
    private string $templateName;
    private string $output;
    private string $httpUrl;
    private array $arguments;
    public function __construct(string $templateName, ?array $arguments=[])
    {
        $this->input = '';
        $this->templateName = $templateName;
        $this->output = '';
        $this->arguments = $arguments;
        $this->httpUrl = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME'], 2);
    }
    public function parse()
    {
        // читаем файл шаблона
        $f = fopen($this->templateName, 'r');
        if ($f) {
            while(!feof($f))
            {
                $str = fgets($f);
                // функция @include
                $str = $this->includes($str);
                // функция forEach
                $str = $this->forEach($f, $str);
                // функция isSet
                $str = $this->isSet($f, $str);
                $this->input .= $str;
            }
            fclose($f);
        }
        // переменные
        $this->output = $this->vars($this->input);
        // @url
        $this->output = str_replace('{{@url}}', $this->httpUrl, $this->output);
        return $this->output;
    }
    private function vars($str)
    {
        // vars
        $str = preg_replace_callback("/\{\{\\$([[:alnum:],_]*)\}\}/", function($matches){
            return $this->arguments[$matches[1]];
        }, $str);
        // objects
        $str = preg_replace_callback("/\{\{\\$([[:alnum:],_]*)->([[:alnum:],_]*)\}\}/", function($matches){
            $obj = $this->arguments[$matches[1]];
            $prop = $matches[2];
            return $obj->$prop;
        }, $str);
        return $str;
    }
    private function includes(string $input)
    {
        return preg_replace_callback("/@include\(\'(.*)\'\)/", function($matches){
            return View::render($matches[1], $this->arguments);
        }, $input);
    }
    private function forEach($f, string $input)
    {
        if (preg_match("/@foreach\((.*)\)/", $input, $match))
        {
            $condition = $match[1];
            // дочитываем файл до @endforeach
            $body = '';
            while(!feof($f))
            {
                $str = fgets($f);
                // выход из цикла
                if (preg_match("/@endforeach/", $str))
                {
                    break;
                }
                $body .= $str;
            }
            // формируем цикл
            $input = '';
            preg_match_all("/\\$([[:alnum:],_]*)/", $condition, $match);
            $arrayVariable = $match[1][0];
            $keyVariable = $match[1][1];
            $valueVariable = $match[1][2];
            $array = $this->arguments[$arrayVariable];
            if ($valueVariable){
                foreach($array as $key => $value)
                {
                    // добавляем временные переменные
                    $this->arguments[$keyVariable] = $key;
                    $this->arguments[$valueVariable] = $value;
                    $input .= $this->vars($body);
                }
                // очищаем временные переменные
                unset($this->argumnets[$keyVariable]);
                unset($this->argumnets[$valueVariable]);
            } else
            {
                foreach($array as $key)
                {
                    $this->arguments[$keyVariable] = $key;
                    $input .= $this->vars($body);
                }
                unset($this->argumnets[$keyVariable]);
            }
        }
        return $input;
    }
    private function isSet($f, string $input)
    {
        if (preg_match("/@isset\((.*)\)/", $input, $match))
        {
            $condition = $match[1];
            // дочитываем файл до @endisset
            $body = '';
            while(!feof($f))
            {
                $str = fgets($f);
                // выход из цикла
                if (preg_match("/@endisset/", $str))
                {
                    break;
                }
                $body .= $str;
            }
            // формируем условие
            $input = '';
            // $class->prop
            if (preg_match("/\\$([[:alnum:],_]*)->([[:alnum:],_]*)/", $condition, $match))
            {
                $obj = $this->arguments[$match[1]];
                $prop = $match[2];
                if ($obj->$prop)
                {
                    return $body;
                }
                return $input;
            } elseif (preg_match("/\\$([[:alnum:],_]*)/", $condition, $match))
            {
                $var = $this->arguments[$match[1]];
                if ($var)
                {
                    return $body;
                }
                return $input;
            }
        }
        return $input;
    }
}

// Возвращает строку с обработанным шаблоном
class View{
    private string $templatesDir;
    private string $templateName;
    private string $output;
    private Parser $parser;
    public function __construct(string $templateName='', ?array $arguments=[])
    {
        $this->templatesDir = dirname($_SERVER['SCRIPT_FILENAME'], 2).'/templates/';
        $this->templateName = $this->templatesDir.$templateName.'.html';
        $this->parser = new Parser($this->templateName, $arguments);
        $this->output = $this->parser->parse();
    }
    static public function render(string $templateName='', ?array $arguments=[])
    {
        $tmp =  new static($templateName, $arguments);
        return $tmp->output;
    }
}

?>