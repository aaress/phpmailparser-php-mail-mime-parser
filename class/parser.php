<?php

/**
 * parser short summary.
 *
 * parser description.
 *
 * @version 1.0
 * @author ghost
 */
class parser {
    
    private $file,$email,$body,$head;
    function stream($file){
        $dosya = fopen($file,"r");
        $this -> p1(fread($dosya, filesize($file)));

    }
    
    function p1($email){
        
        list($head, $body) = preg_split("/\r\n\r\n/", $email, 2);
        $this ->head = $head;
        $this ->body = $body;
    }
    
    
    function head_parser(){
        $head=preg_replace("/(\n{1})(\s{1})/", "-r-n", utf8_encode($this ->head));
        preg_match_all("/([\S].*?)[:{1}](.*)/", $head, $headers[]);

        foreach ($headers[0][1] as $i => $dilimler) {
            
            
            $headers[0][2][$i]=preg_replace("/(-r-n)/", "\n\t", utf8_encode($headers[0][2][$i]));
            
            
            
            

            
            if($headerarray[strtolower(trim($dilimler))]){
                
                $headerarray[] = array_push($headerarray[strtolower(trim($dilimler))],$headers[0][2][$i])  ;
            }else{
                
                $yeni = array(strtolower(trim($dilimler))=>array(trim($headers[0][2][$i])));
                $headerarray = array_merge($headerarray,$yeni) ; 
                
            }
            
            
            unset($headerarray[0]);
            
            
            $boundary_parcala = explode(";",$headerarray["content-type"][1]);
            
            $headerarray["content-type"][0] = $boundary_parcala[0];
            preg_match("#([a-zA-Z]+[^-])[=](.*)#", $boundary_parcala[1], $cikan);
            if(preg_match('#"(.*)"#', $cikan[2], $cikans)){
                $cikan[2] = $cikans[1];
            }
            $yeni_boundray = array($cikan[1]=> array($cikan[2]));
            $headerarray = array_merge($headerarray,$yeni_boundray) ;
            
            unset($headerarray[""]);
            
        }

        
        if($headerarray["from"]){
            preg_match('#^(.*?)([\w\+\-a-z]+[@]+[\w\-a-z].*[^<>])#',$headerarray["from"][0],$froms);
            
            $headerarray["from"]["adress"] =$froms[2];
            
            preg_match('#[\w/?\-\=\s\.*]+[^"<>]#',$froms[1],$name);
            
            
            $headerarray["from"]["name"] =iconv_mime_decode($name[0],0,"UTF-8");
            
            
        }
        
        $headerarray["subject"][0] =iconv_mime_decode($headerarray["subject"][0],0,"UTF-8");
        if($headerarray["to"]){
            preg_match('#^(.*?)([\w\+\-a-z]+[@]+[\w\-a-z].*[^<>])#',$headerarray["to"][0],$froms);
            
            $headerarray["to"]["adress"] =$froms[2];
            
            preg_match('#[\w/?\-\=\s\.*]+[^"<>]#',$froms[1],$name);
            
            
            $headerarray["to"]["name"] =$name[0];
            
            
        }   
        
        $this->headerarray = $headerarray;
        
    }
    function body_parser(){



        if((stristr($this->headerarray["content-type"][0],"multipart/alternative")) || (stristr($this->headerarray["content-type"][0],"multipart/mixed"))){
            
            $bodys = explode("--".trim($this->headerarray["boundary"][0]), $this ->body);
            
            unset($bodys[0]);
            
            $i = 0;
            
            foreach ($bodys as $bodyayir) {
                
                if($olustur = preg_split("/\r\n\r\n/", $bodyayir, 2)){
                    
                    $bodyarray[$i] = array();
                    $olustur[0]=preg_replace("/(\n{1})(\s{1})/", " ", utf8_encode($olustur[0]));
                    
                    
                    $headers = explode("\r\n", $olustur[0]);
                    
                    $isboddy =$olustur[1];
                    
                    
                    foreach ($headers as $olustur) {
                        
                        $dilimler = explode(":",  $olustur);
                        
                        
                        $yeni = array(strtolower(trim($dilimler[0]))=>trim($dilimler[1]));
                        
                        $bodyarray[$i] = array_merge($bodyarray[$i],$yeni) ;
                        
                        
                        $yeniarray = explode(";",  $bodyarray[$i]["content-type"]);
                        
                        
                        preg_match("#([a-zA-Z]+[^-])[=](.*)#",  $yeniarray[1],$yeniarrays);
                        if(preg_match('#"(.*)"#', $yeniarrays[2], $cikans)){
                            $yeniarrays[2] = $cikans[1];
                        }
                        if($bodyarray[$i]["content-type"]){
                            $bodyarray[$i]["content-type"] = $yeniarray[0]; 
                        }
                        
                        $bodyarray[$i][trim($yeniarrays[1])] = $yeniarrays[2];
                        
                        
                        
                        $yeniarray = explode(";",  $bodyarray[$i]["content-disposition"]);
                        
                        
                        if($bodyarray[$i]["content-disposition"]){
                            $bodyarray[$i]["content-disposition"] = $yeniarray[0];
                        }
                        preg_match("#([a-zA-Z]+[^-])[=](.*)#",  $yeniarray[1],$yeniarrays);
                        
                        if(preg_match('#"(.*)"#', $yeniarrays[2], $cikans)){
                            $yeniarrays[2] = $cikans[1];
                        }
                        
                        $bodyarray[$i][trim($yeniarrays[1])] = $yeniarrays[2];
                        
                        
                        
                        
                        
                        
                        
                        
                        $bodyekle = array($bodyarray[$i]["content-type"] =>  $isboddy );
                        
                        $bodyarray[$i] = array_merge($bodyarray[$i],$bodyekle) ;
                        
                        
                        unset($bodyarray[$i][""]);
                        
                    }
                    
                    
                    if(stristr($bodyarray[$i]["content-disposition"],"attachment")){
                        
                        if($bodyarray[$i]["name"]){
                            $uzanti = explode(".",$bodyarray[$i]["name"]);
                        }else{
                            $uzanti = explode(".",$bodyarray[$i]["filename"]);
                        }
                        if($bodyarray["file-".$uzanti[1]]){
                            $bodyarray[]=array_push($bodyarray["file-".$uzanti[1]],$bodyarray[$i]) ;
                            
                        }else{
                            $bodyarray["file-".$uzanti[1]] =  array($bodyarray[$i]);
                            
                        }
                        unset($bodyarray[$i]); 
                        
                    }
                    
                    
                    switch ($bodyarray[$i]["content-type"]) {
                        case "text/html":
                            $bodyarray["html"] =  $bodyarray[$i]; 
                            
                            break;
                        
                        case "text/plain":
                            $bodyarray["txt"] =  $bodyarray[$i]; 
                            
                            break;
                        case "multipart/alternative":
                            $bodyarray["parser_ready"] =  $bodyarray[$i]; 
                            
                            break;    
                        case "multipart/mixed":
                            $bodyarray["parser_ready"] =  $bodyarray[$i]; 
                            
                            break; 
                        
                        
                    }
                    unset($bodyarray[$i]); 
                    
                    
                    $i++;
                    
                    
                }
                
                
            }
            
            
            
        }else{
            
            
            
            if($this->headerarray["content-type"]=="text/html") {
            
            $bodyarray["html"]["text/html"] = $this ->body;
            }else{
            
                $bodyarray["txt"]["text/plain"] = $this ->body;
                
            }
            
        }
        
        
        
        $this-> bodyarray =  $bodyarray;
        


    }
    

    function parser_ready($parser_ready,$boundary){

        $bodys = explode("--".$boundary, $parser_ready);
        

        
        
        $i = 0;
        
        foreach ($bodys as $bodyayir) {
            
            if($olustur = preg_split("/\r\n\r\n/", $bodyayir, 2)){
                
                
                $olustur[0]=preg_replace("/(\n{1})(\s{1})/", " ", utf8_encode($olustur[0]));
                
                
                $headers = explode("\r\n", $olustur[0]);
                
                $isboddy =$olustur[1];
                
                
                foreach ($headers as $olustur) {
                    
                    $dilimler = explode(":",  $olustur);
                    
                    
                    $yeni = array(strtolower(trim($dilimler[0]))=>trim($dilimler[1]));
                    
                    $bodyarray[$i] = array_merge($bodyarray[$i],$yeni) ;
                    
                    
                    $yeniarray = explode(";",  $bodyarray[$i]["content-type"]);
                    
                    
                    preg_match("#([a-zA-Z]+[^-])[=](.*)#",  $yeniarray[1],$yeniarrays);
                    if(preg_match('#"(.*)"#', $yeniarrays[2], $cikans)){
                        $yeniarrays[2] = $cikans[1];
                    }
                    if($bodyarray[$i]["content-type"]){
                        $bodyarray[$i]["content-type"] = $yeniarray[0]; 
                    }
                    
                    $bodyarray[$i][trim($yeniarrays[1])] = $yeniarrays[2];
                    
                    
                    
                    $yeniarray = explode(";",  $bodyarray[$i]["content-disposition"]);
                    
                    
                    if($bodyarray[$i]["content-disposition"]){
                        $bodyarray[$i]["content-disposition"] = $yeniarray[0];
                    }
                    preg_match("#([a-zA-Z]+[^-])[=](.*)#",  $yeniarray[1],$yeniarrays);
                    
                    if(preg_match('#"(.*)"#', $yeniarrays[2], $cikans)){
                        $yeniarrays[2] = $cikans[1];
                    }
                    
                    $bodyarray[$i][trim($yeniarrays[1])] = $yeniarrays[2];
                    
                    $bodyekle = array($bodyarray[$i]["content-type"] =>  $isboddy );
                    
                    $bodyarray[$i] = array_merge($bodyarray[$i],$bodyekle) ;
                    
                    
                    unset($bodyarray[$i][""]);
                    
                }
                
                if(stristr($bodyarray[$i]["content-disposition"],"attachment")){
                    
                    if($bodyarray[$i]["name"]){
                        $uzanti = explode(".",$bodyarray[$i]["name"]);
                    }else{
                        $uzanti = explode(".",$bodyarray[$i]["filename"]);
                    }
                    if($bodyarray["file-".$uzanti[1]]){
                        $this->bodyarray[]=array_push($bodyarray["file-".$uzanti[1]],$bodyarray[$i]) ;
                        
                    }else{
                        $this->bodyarray["file-".$uzanti[1]] =  array($bodyarray[$i]);
                        
                    }
                    unset($bodyarray[$i]); 
                    
                }
                
                switch (strtolower($bodyarray[$i]["content-type"])) {
                    case "text/html":
                        $this->bodyarray["html"] =  $bodyarray[$i]; 
                        
                        break;
                    
                    case "text/plain":
                        $this->bodyarray["txt"] =  $bodyarray[$i]; 
                        
                        break;
                    case "multipart/alternative":
                        $bodyarray["parser_ready"] =  $bodyarray[$i]; 
                        
                        break;                             
                    
                }
                unset($bodyarray); 
                unset( $this->bodyarray["parser_ready"]); 
            }
            
            
        }
        
        
        
        


    }


    function covert($covert,$cte){


        switch ($cte) {
            case "base64":
                
                $isboddy = base64_decode($covert);
                break;
            case "quoted-printable":
                
                $isboddy = quoted_printable_decode($covert);
                break;
            
        }

        return $isboddy;

    }
    
}
?>
