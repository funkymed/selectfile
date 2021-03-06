<?php
// selectfile.js v1.0
//
// Copyright (c) 2007 Cyril Pereira
// Author: Cyril Pereira | http://www.cyrilpereira.com
//
// selectfile is freely distributable under the terms of an MIT-style license.
//
/*-----------------------------------------------------------------------------------------------*/

class dirLister{

    var $root_dir, $current_dir, $dossier, $dirObj,$html,$directory_file_size;
    var $all_file=array();
    var $all_dir=array();
    var $imageFileExt=array('.jpg','.png','.gif');
    var $validExt="";
    var $thumbSize=128;

    /**
     * @param null $root_dir
     * @param $validExt
     * @return void
     */
    function dirLister($root_dir=null,$validExt){
        $this->validExt=explode(",",$validExt);
        $this->root_dir=$root_dir;
        $this->dossier=$root_dir;
        $this->update_dir();
        $this->directory_file_size=0;
    }

    /**
     * @param null $setdossier
     * @return void
     */
    function enterDir($setdossier=null){
        $this->dossier.=$setdossier;
    }

    /**
     * @return void
     */
    function update_dir(){
        if (is_dir($this->dossier)){
            $this->all_file=array(
                "NAME"=>array(),
                "SIZE"=>array(),
                "OCTSIZE"=>array(),
                "EXT"=>array(),
                "DATE"=>array(),
                "PATH"=>array(),
                "ICON"=>array(),
                "COUNT"=>0,
                "DIRECTORY_SIZE"=>0
            );
            $this->all_dir=array(
                "NAME"=>array(),
                "DATE"=>array(),
                "ICON"=>array(),
                "COUNT"=>0
            );
            $this->dirObj = opendir($this->dossier);
            while (false !== ($Fichier=@readdir($this->dirObj))){

                if (is_dir($this->dossier.$Fichier) && $Fichier!="." && $Fichier!=".."){
                    $this->all_dir["NAME"][]=$Fichier;
                    $this->all_dir["DATE"][]=date ("d/m/Y H:i:s", filemtime($this->dossier.$Fichier));
                    $this->all_dir["ICON"][]='filestypes/dir.png';
                }else if (is_file($this->dossier.$Fichier) && in_array(strtolower(strrchr($Fichier, '.')),$this->validExt)){
                    $fileSize=filesize($this->dossier.$Fichier);
                    $testthumb=explode("_",$Fichier);
                    if ($testthumb[0]!="thumb"){
                        $this->all_file["NAME"][]=$Fichier;
                        $this->all_file["EXT"][]=strtolower(strrchr($Fichier, '.'));
                        $this->all_file["SIZE"][]=$this->convertOct($fileSize);
                        $this->all_file["OCTSIZE"][]=$fileSize;
                        $this->all_file["DATE"][]=date ("d/m/Y H:i:s", filemtime($this->dossier.$Fichier));
                        $this->all_file["PATH"][]=$this->dossier.$Fichier;
                        $this->all_file["ICON"][]=$this->getFileType(strtolower(strrchr($Fichier, '.')));
                        $this->directory_file_size+=$fileSize;
                    }
                }

            }
            $this->all_dir["COUNT"]=count($this->all_dir["NAME"]);
            $this->all_file["COUNT"]=count($this->all_file["NAME"]);
            $this->all_file["DIRECTORY_SIZE"]=$this->convertOct($this->directory_file_size);
        }else{
            $this->error("[PARSE ERROR] directory not recognized");
        }
    }
    /**
     * @param $msg
     * @return void
     */
    function error($msg){
        print $msg."<br/>\n";
    }
    /**
     * @return array
     */
    function get_FileArray(){
        return $this->all_file;
    }
    /**
     * @return array
     */
    function get_DirArray(){
        return $this->all_dir;
    }
    /**
     * @param $ext
     * @return string
     */
    function getFileType($ext){
        $FileExt=substr($ext,1,strlen($ext));
        if (is_file('filestypes/'.$FileExt.'.png')){
            return 'filestypes/'.$FileExt.'.png';
        }else{
            return 'filestypes/default.png';
        }
    }
    /**
     * @return void
     */
    function debugHTML(){
        if (is_dir($this->dossier)){
            $buffer='<div id="FileManager"><table width="100%" border="0" cellspacing="0" cellpadding="0">';
            $buffer.='<tr class="top">';
            $buffer.='<td align="left">Nom</td>';
            $buffer.='<td align="right">Taille</td>';
            $buffer.='<td align="center">Ext</td>';
            $buffer.='<td align="right">Date</td>';
            $buffer.='</tr>';
            /*directory*/
            for ($aa=0;$aa<count($this->all_dir['NAME']);$aa++){
                if ($aa % 2==0){ $class="dir1"; }else{ $class="dir2"; }
                $buffer.= "<tr class=\"".$class."\"><td  colspan=\"3\">".$this->all_dir['NAME'][$aa]."</td>";
                $buffer.= "<td align=\"right\">".$this->all_dir['DATE'][$aa]."</td></tr>";
            }
            /*files*/
            for ($bb=0;$bb<count($this->all_file['NAME']);$bb++){
                if ($bb % 2==0){ $class="file1"; }else{ $class="file2"; }
                $buffer.= "<tr class=\"".$class."\">";
                $buffer.= "<td>".$this->all_file['NAME'][$bb]."</td>";
                $buffer.= "<td align=\"right\">".number_format($this->all_file['SIZE'][$bb], 2, ',', ' ')."ko</td>";
                $buffer.= "<td align=\"center\">".$this->all_file['EXT'][$bb]."</td>";
                $buffer.= "<td align=\"right\">".$this->all_file['DATE'][$bb]."</td>";
                $buffer.= "</tr>";
            }
            $buffer.='</table></div>';
            $this->html=$buffer;
        }else{
            $this->error("[DISPLAY ERROR] directory not recognized");
        }
    }
    /**
     * @return string
     */
    function get_json(){
        $arrayDir=null;
        $arrayFile=null;
        if ($this->all_dir["COUNT"]>0){
            $arrayDir=$this->all_dir;
        }
        if ($this->all_file["COUNT"]>0){
            $arrayFile=$this->all_file;
        }
        return $this->array2json(array(array("DIR"=>$arrayDir,"FILE"=>$arrayFile)));
    }
    /**
    * @return array
    */
    function getFileArray(){
        return $this->all_file;
    }
    /**
     * @return
     */
    function getHTML(){
        return $this->html;
    }
    /**
     * @param $arr
     * @return string
     */
    function array2json($arr) {
        $parts = array();
        $is_list = false;
        $keys = array_keys($arr);
        $max_length = count($arr)-1;
        if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) {
            $is_list = true;
            for($i=0; $i<count($keys); $i++) {
                if($i != $keys[$i]) {
                    $is_list = false;
                    break;
                }
            }
        }
        foreach($arr as $key=>$value) {
            if(is_array($value)) {
                if($is_list) $parts[] = $this->array2json($value);
                else $parts[] = '"' . $key . '":' . $this->array2json($value);
            } else {
                $str = '';
                if(!$is_list) $str = '"' . $key . '":';
                if(is_numeric($value)) $str .= $value;
                elseif($value === false) $str .= 'false';
                elseif($value === true) $str .= 'true';
                else $str .= '"' . addslashes($value) . '"';
                $parts[] = $str;
            }
        }
        $json = implode(',',$parts);
        if($is_list) return '[' . $json . ']';
        return '{' . $json . '}';
    }

    /**
     * @param $val
     * @return string
     */
    function convertOct($val){
        if ($val>1000000000){
            $val/=1024;
            $val/=1024;
            $val/=1024;
            return number_format($val, 2, ',', ' ')." Go";
        }else if ($val>1000000){
            $val/=1024;
            $val/=1024;
            return number_format($val, 2, ',', ' ')." Mo";
        }else if ($val>1000){
            $val/=1024;
            return number_format($val, 2, ',', ' ')." ko";
        }else{
            return number_format($val, 2, ',', ' ')." octs";
        }
    }
}

?>