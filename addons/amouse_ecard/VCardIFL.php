<?php

/**
+-------------------------------------------------------------------------
+---------------------->> In The Name Of Allah <<-------------------------
+-------------------------------------------------------------------------
| Class VCardIFL version 0.0.1 (for php 4)
| Script For Create VCard  Full IFLashLord varsion 0.0.1
| Vcard Create Online [VCard Creator Full]
| Author  Behrouz Pooladrag  (IFLashLord) <Me [at] IFLashLord [dot] Com>
| Email bugs/suggestions to  Me [at] iflashlord.com
| Copyright (c) 2008 By Behrouz Pooladrag ,IFLashLord Co.
+-------------------------------------------------------------------------
| This script has been created and released under
| the GNU GPL and is free to use and redistribute
| only if this copyright statement is not removed
+-------------------------------------------------------------------------
+--------------| Contact 2 Behrouz Pooladrag |----------------------------
| Email : Me [ at ] IFLashLord [dot] Com
| WebSite : http://www.IFLashLord.Com
| Yahoo : BehrouzPC [at] yahoo.Com
| G-Mail : FLashLordX [at] gmail.Com
| Mobile : +98 913 12 777 14
+-------------------------------------------------------------------------
| (Zakate Elame Nasher Aan Ast )
+-------------------------------------------------------------------------
 **/
class VCardIFL {

    public $vcard_f_name;
    public $vcard_compan;//  Company Name
    public $vcard_w_addr;//  Street Address (work)
    public $vcard_w_mail;//  E-mail (work)
    public $vcard_w_phon;//  Phone (work)
    public $vcard_w_titl;//  Title (work)
    public $vcard_uri ;//  WORK URL
    public $vcard_w_photo ;//  WORK URL
    public $vcard_note  ;//  Note

    public $fileName;    //  File Name Download or Save
    public $saveTo;      //  Save To Address Folder To Save (on Server)

    // private var
    private $vcard_addr;
    private $vcard_labl;
    private $vcard;      //  Vcard Data Set


    function __construct ($arData) {
        if(is_array($arData)) {
            $this->fileName=$arData["fileName"];
            $this->saveTo=$arData["saveTo"];
            $this->vcard_f_name=$arData["vcard_f_name"];
            $this->vcard_uri   =$arData["vcard_uri"];
            $this->vcard_compan=$arData["vcard_compan"];
            $this->vcard_w_addr=$arData["vcard_w_addr"];
            $this->vcard_w_mail=$arData["vcard_w_mail"];
            $this->vcard_w_phon=$arData["vcard_w_phon"];
            $this->vcard_note  =$arData["vcard_note"];
            $this->vcard_w_titl=$arData["vcard_w_titl"];
            $this->vcard_w_photo =$arData["vcard_w_photo"];
        }
    }//end of Conrtuct

    function createVcard() {
        //Vcard Time Zone
        $vcard_tz = date("O");
        //Vcard Rev
        $vcard_rev = date("Y-m-d");
        // Start Vcard Scritp
        $this->vcard = "BEGIN:VCARD\r\n";
        $this->vcard .= "VERSION:3.0\r\n";
       /* $this->vcard .= "CLASS:PUBLIC\r\n";
        $this->vcard .= "PRODID:-//IFLashLord.com [Behrouz Pooladrag]//VcardIFL Version 0.0.1//IR\r\n";
        $this->vcard .= "REV:" . $vcard_rev . "\r\n";
        $this->vcard .= "TZ:" . $vcard_tz . "\r\n";*/
        //vcard_f_name
        if ($this->vcard_f_name != ""){
            $this->vcard .= "FN:".$this->vcard_f_name . "\r\n";
            $this->vcard .= "N:".$this->vcard_f_name . "\r\n";
        }
        // vcard_w_titl
        if ($this->vcard_w_titl != ""){
            $this->vcard .= "TITLE:" . $this->vcard_w_titl . "\r\n";
        }
        // vcard_compan
        if ($this->vcard_compan != ""){
            $this->vcard .= "ORG:" . $this->vcard_compan . "\r\n";
        }
        // vcard_w_uri
        if ($this->vcard_uri != ""){
            $this->vcard .= "URL:" . $this->vcard_uri . "\r\n";
        }
        // vcard_addr
        if ($this->vcard_w_addr != ""){
            $this->vcard .= "ADR:;;" . $this->vcard_w_addr . ";;;;\r\n";
        }
        if ($this->vcard_w_phon != ""){
            $this->vcard .= "TEL;TYPE=CELL:" . $this->vcard_w_phon . "\r\n";
        }
        // vcard_w_mail
        if ($this->vcard_w_mail != ""){
            $vcard .= "EMAIL:" . $this->vcard_w_mail . "\r\n";
        }

        if ($this->vcard_note != ""){
            $this->vcard .= "NOTE:" . $this->vcard_note . "\r\n";
        }
       /* if (!empty($this->vcard_w_photo)){
            $this->vcard .= "PHOTO;ENCODING=B;TYPE=PNG:".$this->vcard_w_photo;
        }*/
        $this->vcard .= "END:VCARD\n";
    }//end of constract

    // Save Vcard in Host
    public function SaveVcard ($randName=false) {
        if($randName) {
            $this->fileName=$this->fileName.uniqid(MD5(Rand(0000000,9999999)));
        }
        $handel=@fopen($this->saveTo."/".$this->fileName.".vcf","w");
        $write=@fwrite($handel,$this->vcard,strlen($this->vcard));
        @fclose($handel);
        return $write ? true : false;
    }//end of function

    // Download Vcard
    public function DownloadVcard () {
        header("Content-type: text/directory");
        header("Content-Disposition: attachment; filename=".$this->fileName.".vcf"."");
        header("Pragma: public");
        print $this->vcard;
    }


}//end class VcardIFL
?>