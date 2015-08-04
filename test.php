<?php
require_once __DIR__."/class/parser.php" ;

    
    
   
   
$path = 'mails/m0008.txt';
$parse = new parser();
$parse->stream($path); 
$parse->head_parser();
$parse->body_parser();

if($parse->bodyarray["parser_ready"]){
    $parse->parser_ready($parse->bodyarray["parser_ready"][$parse->bodyarray["parser_ready"]["content-type"]],$parse->bodyarray["parser_ready"]["boundary"]);
}

print_r($parse->bodyarray);
?>