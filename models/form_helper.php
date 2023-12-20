<?php


function getFromPost($x){
    if (isset($_POST[$x]))
        return htmlspecialchars($_POST[$x]);
    return "";
}

function getFormData($key, $source)
{
    if(isset($source[$key]))
        return htmlspecialchars($source[$key]);
    return "";
}