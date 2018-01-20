<?php
/**
 * cmscode.ru php library php data base
 *
 * @copyright cmscode.ru
 * @link https://github.com/nixsolutions/yandex-php-library
 */

/**
 * @namespace
 */
namespace Cmscode\Bd;

/**
 * Class DataBase
 *
 * @category Cmscode
 * @package bd
 *
 * @author   Ivan Shebarshov <poiskiinfo@yandex.ru>
 * @created  19.01.18 12:35
 *
 * @see https://tech.yandex.com/disk/doc/dg/concepts/api-methods-docpage/
 */
class DataBase
{
    /**
     * @var string
     */
    private $version = '0.0.5';
/* �����: ���� ��������. ���� cmscode.ru
������ ����� ���������� ���������� ��� ����� cms:cmscode �� ��������� ������.
$namebd - �������� �������� �� ��� ����� � ���������� .php
$p - ������������� ���� ./, ../, ../../, � ��. ������ ��� ���� ����� ������� ����� ������ ������������ ���� � ����� ����� �����������.
������� �� �� ������������ ������������ ���������� php 
������: 
$h= fopen($p."$n.php", "r"); // ��������� ������ �� ��� $n ������ ���� �� ����� ��� ./ � ../ � ���������� .php
$d= fgets($h); // ����������� ���������� 1� ������ � ���������� �������� � �� ��� ����� <?php die();?>
while (($d = fgets($h)) !== FALSE) 
	{$data=explode('^',$d); // ���� ������������ � ����� ������ \r\n �� ������ ���������� trim ����� ������� explode
      if($id==$data['0']){ //���������� ������� �� �� �� id ���� ������ ������ ������� � �� ������� ����� ��������.
// ���������� ����� �������� ��� ������ ��, ����� ������� ��������� ������ � ������ � ����� ������� �� ������������ ��� ����� ������� � ������ �����. ��� ������������� ����� �� ����. ���� �������� �������� � ������� �������� �� ����� getFieldsName � ��������� ������ ����� �� ����� ���� � $data['0']; �������� �� ������� ����� ���� ������ ��� ���� �������� � ������� �� ��;
}	}fclose($h); //��������� ���� ��.

��� ������� ������� ������ ����� ����������� cache_cc ����������� � 30 ��� ������� ��������� ������ �� � ��������� ������������� �����.
���� ����� � �������� �� �������� ��������� �� �� ��������� �������� �� ������ ��� ��� ��������� ��� �� ��������� ������ ����� �����������.

*/

/* getFieldsName ����� �������� �������� ��������� �������
$number - int �� 0 �� 3 ����� �����;
0- �������� ������ ���� [0]=>value � ��
1- �������� ������ ���� [value]=>0 � ��
*/
	public function getFieldsName($namebd,$p,$number='0')
	{
     $handle = fopen($p.$namebd.'.php', "r");
     $datas = fgets($handle);
     $datas=str_replace('<?php die();?>','',$datas); 
     $datas=trim($datas);
     $data_string=explode('^',$datas);
     $v=count($data_string);
     if($number=='0')
		{
        	 for($i=0;$i<$v;$i++)
			{
         	 $massiv=explode(' ',trim($data_string[$i]));	
         	 $vm=count($massiv);	
         	 if($vm=='1'){$data[]=$massiv['0'];}
          	 elseif($vm>'1'){$data[]=$massiv['0'];}	
       	 	}
    	}					  
	 elseif($number=='1')
		{
          for($i=0;$i<$v;$i++)
			{
         	 $massiv=explode(' ',trim($data_string[$i]));	
         	 $vm=count($massiv);	
         	 if($vm=='1'){$data[$massiv['0']]=$i;}
         	 elseif($vm>'1'){$data[$massiv['0']]=$i;}	
        	}
    	}	
     elseif($number=='2')
		{
          for($i=0;$i<$v;$i++)
			{
         	 $massiv=explode(' ',trim($data_string[$i]));	
         	 $vm=count($massiv);	
         	 if($vm=='1'){$data[]=array($data_string[$i],'TEXT');}
         	 elseif($vm>'1'){$data[]=array("$massiv[0]","$massiv[1]");}
	    	}
    	}
     elseif($number=='3')
		{
          for($i=0;$i<$v;$i++)
			{
         	 $massiv=explode(' ',trim($data_string[$i]));	
         	 $vm=count($massiv);	
         	 if($vm=='1'){$data[$massiv['0']]=array("$i",'TEXT');}
         	 elseif($vm>'1'){$data[$massiv['0']]=array("$i","$massiv[1]");}	
        	}
    	}
					  
 			  
	 return $data;
 	}

     /* getParamsBd ������� ��� ���������� ���������� ���������.*/

     public function getParamsBd($namebd,$p)
    {
     $file = $p.$namebd.'.php';
     $h= fopen("$file", 'r');
     $d= fgets($h);
     while (($d = fgets($h)) !== FALSE) 
	    {
	     $data=trim($d);
	     $data=explode('^',$data);
	     break;
        }
	 fclose($h);
     return $data;
	}

/* select_cc - ��������� �������� ����� ����� ��� ����� �������� ���������� � ���������� � �������� ������ ��� ������� ������ �� .
������ ������ � ����������� 
$gen=select_cc('bd/com_an_k2/categories/cmscode.php','');
foreach ($gen as $line) {
$data=explode('^',$line);
if($line['0']=='21'){ $massiv[]=$data;$gen->send('stop');} //�������� ������ � ������� � ����� ��� ����������. ������� ����� ���� �������.
}


*/

public function select($namebd,$p) {
    $file = fopen($p.$namebd.'.php', "r"); $line= fgets($file);
    while (!feof($file))  {$line= fgets($file);
$cmd=(yield $line);
if ($cmd == 'stop') {
fclose($file);
return; /* ����� �� ����������.*/
        }
		}fclose($file);
}
	
	


/*
 insert_cc ������ ��� ���������� ����� ����� � ��������� �� � ���������� �������� �� � ���� ������ $field_name � �� �������� � ����� ������� � ��� �� ������� ������ ����
*/

public function insert($namebd,$field_name,$value,$p)
{
$file = $p."$namebd.php";
$stolbci=$this->getFieldsName("$namebd","$p",'0');
$vs=count($stolbci);
for($k=0;$k<$vs;$k++){$data[$k]='';}
$stolbci=$this->getFieldsName("$namebd","$p",'3');
$vs=count($field_name);
for($k=0;$k<$vs;$k++){ 
if(array_key_exists($field_name[$k], $stolbci)){$data[$stolbci[$field_name[$k]][0]]=$this->obrezka_poley_cc($this->zss_replace_cc($value[$k]),$stolbci[$field_name[$k]][1]);}	
	                 }
$znachenie=trim(implode('^',$data));
$a="\n".$znachenie;	
$dfdsg=file_put_contents($file, $a, FILE_APPEND | LOCK_EX);
if($dfdsg===false){return 'no';}
else{return 'ok';}
}

/* updates_cc - ������ ��� ���������� ������ �������� �� ��������� �������� $fn � �� �������� $v � ���� �������;
id - ���� ������ ������� ���� ��������
$n=$namebd;
 */

public function update($n,$fn,$v,$id,$p){
$file = $p."$n.php";
$h= fopen("$file", "rb");
$d= fgets($h);
$messeg='no';
while (($d = fgets($h)) !== FALSE) 
	{$data=explode('^',$d);
		if($id==$data['0']){
$messeg='ok';		
$b=implode('^',$data);
$b=$this->trim_replace_cc($b);
break;
}	}fclose($h);

if($messeg=='ok'){
$stolbci=$this->getFieldsName("$n","$p",'3');
$vs=count($fn);
for($k=0;$k<$vs;$k++){ 
if(array_key_exists($fn[$k], $stolbci)){$data[$stolbci[$fn[$k]]['0']]=$this->obrezka_poley_cc($this->zss_replace_cc($v[$k]),$stolbci[$fn[$k]]['1']);}	
	                                }			 
$data['0']=$id;		 		 
$znachenie=implode('^',$data);
$znachenie=$this->trim_replace_cc($znachenie);
$a=file_get_contents("$file");	
$a=str_replace("$b","$znachenie",$a);	
file_put_contents($file, $a, LOCK_EX);
return $messeg;
}
else{ return $messeg;}
}

/* fileCacheLines - ��������� �������� ����� ����� ��� ����� �������� ���������� � ���������� � �������� ������ ��� ������� ������ �� ������������ � �����������.*/
public function fileCacheLines($filename) {
    $file = fopen($filename.'.php', "r");
    while (!feof($file))  {$line= fgets($file);
$cmd=(yield $line);
if ($cmd == 'stop') {
fclose($file);
return; /* ����� �� ����������.*/
        }
		}fclose($file);
}

/*cache_cc ������ ��� �������� ���� �������� � �� � ����� �������� ���������� ������ ������ �� ���*/

public function cache_cc($p,$puti,$namebd,$datecache,$config_site){

 clearstatcache();
 if(!is_dir($p.$config_site->dir_cache)) {mkdir($p.$config_site->dir_cache);}
  if(!is_dir($p.$config_site->dir_cache.'/'.$puti)) {mkdir($p.$config_site->dir_cache.'/'.$puti);}
  $filebd_norsashir=$p.$config_site->dir_bd.'/'.$config_site->dir_cat.'/'.$namebd;
 $filebd=$p.$config_site->dir_bd.'/'.$config_site->dir_cat.'/'.$namebd.'.php';
$filename =$p.$config_site->dir_cache.'/'.$puti.'/'.$namebd.'.php';
$filename1 =$p.$config_site->dir_cache.'/'.$puti.'/'.$namebd.'.txt';
if (file_exists($filebd)) {$time_1=date("YmdHis",filemtime($filebd));}
else{$time_1=date("YmdHis",filemtime($filebd));}
  $times=trim($time_1-$datecache);
if($time_1!=$datecache){$ceshreloads='1';}
elseif($time_1==$datecache){$ceshreloads='0';}
if($ceshreloads=='1'){
	$datecache=$time_1;
	$vvvss='0';  $k='0';
/*
	$handless = fopen($filebd, 'r');
 while (!feof($handless)) {
 $bufferss = fgets($handless);
*/

 $gen=$this->fileCacheLines($filebd_norsashir);
foreach ($gen as $bufferss) {
 $zzzzz=explode('^',$bufferss);	
/* if($zzzzz['0']=='21'){ $gen->send('stop');} ������ ��������� ����������*/
$vvvss+=strlen($bufferss);
$fseesk[]=$vvvss; 
    if($zzzzz['18']=='1'){                
$massivs[]=array("$zzzzz[0]","$zzzzz[5]","$zzzzz[17]","$zzzzz[10]","$zzzzz[19]","$k");  }
$k++;
                        }				
/*fclose($handless); 	*/

$vv=count($massivs);
for($g=0;$g<$vv;$g++){$fg=$g;
	$df=$massivs[$g];
$s=$df['5']-1;
	
$massiv[$g]='$a[]=array(\''.$df['0'].'\',\''.$df['1'].'\',\''.$df['2'].'\',\''.$df['3'].'\',\''.$df['4'].'\',\''.$fseesk[$s].'\');';



}

if(!isset($massiv)){$x='';}
elseif(is_array($massiv)){$x=implode(" ",$massiv);}
					 	
$vsegov=count($massiv);
$m=fopen($filename,"w");
flock($m, LOCK_EX);
fwrite($m,'<?php 
'.$x.'
?>
'); 
flock($m, LOCK_UN);
fclose($m);  
$m=fopen($filename1,"w");
flock($m, LOCK_EX);
fwrite($m,$datecache); 
flock($m, LOCK_UN);
fclose($m);  
                }
				
}

/*del_cc ������� ������ �� ����� � ��������� ���������. �� ��� id ���� ������� id ������� �� � �������� ������*/
public function delLine_cc($namebd,$id,$p){}

/* delbd_cc ������� �� ��������� � ���������� � ����� � �������*/
public function delBd_cc($namebd,$p){}

/* create_cc - �������� �������� ��������� ��
$znachenie - ������ � ���������� � ��� ���������� �������� ������� �� ��� � ���������� ������� �������� ����� ������ ��� � ��� ���� 
������1 � ����������:
id SMALLTEXT^title 62500^shablon 256
� ����� ������ �� ������ ���� ������� ��������� �� ������ ������, ���� �� �� ������ ������ ������� ���������� �������� \r \n 
^ - ������ ������������ �������� ��������. ��� ����� �������������� � ������ ��������.
������2 ��� ����������:
id^title^shablon
��� ���������� �������� �������� ����� ������� ������ 65535 - ��� ����� �������� ������� ����� ��������� � ���� ������ ��������� ����� ��������.
������� ��� �� ��������� ���� .txt � ������� �������� ����� id ��������� ������ ����������� � ��� ���������� ����� ������ ���������� ++ ������� �����.

id ������ ���� 1� ����� ��� ��������� �� ���� ����� ����������� ���������� ����� �������� � ������� ����� �����. ���� ��������������� ������� ��� ����.

*/
public function create_cc($namebd,$znachenie,$p)
{
$file=$p.$namebd;
$znachenie=$this->trim_replace_cc($znachenie);
$m=fopen($file.'.php',"w");
flock($m, LOCK_EX);
fwrite($m,'<?php die();?>'.$znachenie);
flock($m, LOCK_UN);
fclose($m);
$m=fopen($file.'.txt',"w");
flock($m, LOCK_EX);
fwrite($m,'0');
flock($m, LOCK_UN);
fclose($m);

}

/* createcod_cc ������ � �������� ������ ���� ������� ��� ��������� */
public function createcod_cc($name_puti,$znachenie,$p){
$m=fopen($p.$name_puti,"w");
flock($m, LOCK_EX);
fwrite($m,$znachenie);
flock($m, LOCK_UN);
fclose($m);
}


public function trim_replace_cc($text)
{
$text =str_replace("\n",'',$text);
$text =str_replace("\r",'',$text);
$text =trim($text);
return $text;
}

public function ozss_replace_cc($text)
{
$text =str_replace('c_m_s_c_o_d_e__n__',"\n",$text);
$text =str_replace('c_m_s_c_o_d_e__r__',"\r",$text);
$text =str_replace('c_m_s_c_o_d_e__/\__',"^",$text);
$text=str_replace('c_m_s_c_o_d_e__t__',"\t",$text);
$text=str_replace('c_m_s_c_o_d_e__v__',"\v",$text);
$text =trim($text);
return $text;
}
public function zss_replace_cc($text)
{
$text =str_replace("\n",'c_m_s_c_o_d_e__n__',$text);
$text =str_replace("\r",'c_m_s_c_o_d_e__r__',$text);
$text =str_replace("^",'c_m_s_c_o_d_e__/\__',$text);
$text=str_replace("\t",'c_m_s_c_o_d_e__t__',$text);
$text=str_replace("\v",'c_m_s_c_o_d_e__v__',$text);
$text =trim($text);
return $text;
}

public function obrezka_poley_cc($t,$type='')
{
if(is_numeric($type)){$chislosimvol=$type;}
else{
if($type=='SMALLTEXT'){$chislosimvol='255';}
elseif($type=='TEXT'){$chislosimvol='65535';}
elseif($type=='MEDIUMTEXT'){$chislosimvol='16777215';}
elseif($type=='LONGTEXT'){$chislosimvol='4294967295';}
else{$chislosimvol='65535';}
    }
	
if(mb_strlen($t)<=$chislosimvol){$t=$t;}
else{

$t = mb_substr($t, 0, $chislosimvol,"UTF-8");/* ���� 5 �� ���� ��� ���� �� � ������� 5�� ������� ���� �� ��� �� 2 ������� ��������� � ��������*/
$t = trim($t);


}	
	
return $t;
}

/**/
public function strok($file,$b)
{
$ofile =$b.'bd/'.$file.'.php';
$handle = fopen($ofile, "r"); 
$n=0; 
while (!feof($handle)) 
{ 
$bufer = fread($handle,1048576); 
$n+=substr_count($bufer,"\n"); 
}
fclose($handle);
return $n; 
}

/**/
public function ProverkaFormataDati($data){
	
	$data=str_replace('^','',$data);
	$data=str_replace("\n",'',$data);
	$data=str_replace("\r",'',$data);
	$data=str_replace(' ','',$data);
	$data=trim($data);
$regularka = "/^([0-9]{4}).([0-9]{2}).([0-9]{2}).([0-9]{2}).([0-9]{2}).([0-9]{2})$/";
if ( preg_match($regularka, $data, $t) ){ 
/* ���� ���� �������� �� �������� �� ���� �������� ���� ��� �������� �� �������� ��������� ����*/
$t[1]; /* ��� ���� � ����� ������ ����� ���� ������������ ������ � ������e 0090 ��� �� �� ���� �������� � ��� �� �������*/
if($t[2]>12){$t[2]='01';}
elseif($t[2]=='00'){$t[2]='01';}
if($t[3]>31){$t[3]='01';}
elseif($t[3]=='00'){$t[3]='01';}
if($t[4]>23){$t[4]='00';}
if($t[5]>59){$t[5]='00';}
if($t[6]>59){$t[6]='00';}
return $t[1].'.'.$t[2].'.'.$t[3].'.'.$t[4].'.'.$t[5].'.'.$t[6];}
             return date('Y.m.d.H.i.s');                      }

 /* perep ������ ��� ��������������� ����� ����������� � ������� ����� ����� ���� ���� ������ ������ ���� � ��� �� ����� ����������
 ��� �������� ����� ��������������� ��� ���� � ����� components ��� modul ������� ����� ����� � ��������� perep_������������������� ��� perep_�������������� �������� ���� � ����� ��������� ����� ��� � � �������� ������ ��� ����������
 
 ������� ������� ��� �� ���������� ���������� � ���������� ��� ������ �� �� ������ ������ �������� ���� ����������������, �� ������ ���� ���� ���������� �� ���������������� ����� ������ ������� �� ���� ����� ����� ���� ������ � 
 
 $putibezcom - ���� �� ����� ��� �������� ����� ���������� ��� ������
 $p - ��������������� �������� ���� ����� ��������� �������� ./,../
 $com - ��������� 2 �������� components ��� modul
 $namecome - �������� ���������� ��� ������ ��� ���c����� com_ ��� mod_ � ��� ������ / \
 ������� ������ ���������� ���� �� ����� ����������������� ��� �� ��������� ���� ��� ���������������.
 ������ perep ('an_k2/addcat/default.txt',$p,'components','an_k2')
 */

 public function perep_cc ($putibezcom,$p,$com,$namecome)
 { clearstatcache();
 if($com=='components'){$pristavka1='components/perep_';$pristavka2='components/com_';}
 elseif($com=='modul'){$pristavka1='modul/perep_'; $pristavka2='modul/mod_';}
 else{return 'error '.$p.'functions/_cc.php 404 lines �� ������ 3� �������� ��������� ��� ��� ������';}
$file=$p.$name;
$put1=$p.$pristavka1.$namecome.'/'.$putibezcom;
$put2=$p.$pristavka2.$namecome.'/'.$putibezcom;
 if(file_exists($put1)){ return $put1;}
 else{ return $put2;}//������ ���� ���� �������� �� ������ ���� ����� ��� ������� ������.

 }			 
			 
			 
			 
			 



}

?>