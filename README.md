## Requirements

The following versions of PHP are supported by this version.

* PHP 5.4
* PHP 5.5
* PHP 5.6

## How to use it ?


<?php
//We need to add the library first !

require_once __DIR__."class/parser.php" ;

$path = 'path/to/mail.txt';

$Parser = new parser();

//There are three input methods of the mime mail to be parsed

//specify a file path to the mime mail :

$Parser->stream($path); 

// Get Header Data

$Parser->head_parser();

$from = $Parser->headerarray['from'][0];

$subject = $Parser->headerarray['subject'][0];

//or

print_r($Parser->headerarray);

// Get Body Data multipart/alternative 

$Parser->body_parser();

$text = $Parser->bodyarray["txt"]["content-type"];

$html = $Parser->bodyarray["html"]["content-type"];
//or

print_r($Parser->bodyarray);

// Get Body Data multipart/mixed 

if($uye->bodyarray["parser_ready"]){

    $uye->parser_ready($uye->bodyarray["parser_ready"][$uye->bodyarray["parser_ready"]["content-type"]],$uye->bodyarray["parser_ready"]["boundary"])
    ;
}
$text = $Parser->bodyarray["txt"]["content-type"];

$html = $Parser->bodyarray["html"]["content-type"];

// and the attachments also

$Parser->bodyarray["file-*(file extension)"][0]["content-type"];

//or

print_r($Parser->bodyarray);

// Get Covert mime content-transfer-encoding base64 and quoted-printable 

$html  = $uye->covert($Parser->bodyarray["html"]["content-type"],$Parser->bodyarray["html"]["content-transfer-encoding"])

?>


## Contributing ?

Feel free to contribute.  
To add issue, please provide the raw email with it.

### License

The phpmailmparser/php-mime-mail-parser is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
