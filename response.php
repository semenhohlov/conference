<?php
namespace Response;

class Response{
    public string $output;
    public function __construct(?string $output='Response пуст.')
    {
        $this->output = $output;
        echo $this->output;
    }
    static public function show(?string $output='Response пуст.')
    {
        return new static($output);
    }
    static public function redirect(string $route='/')
    {
        $rootUrl = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME'], 2);
        header('Location: '.$rootUrl.$route);
    }
}
?>