<?php
/**
 * Второй файл примера правильного документирования
 * 
 * @author poiskiinfo <poiskiinfo@yandex.ru>
 * @package txtbd
 */

/**
 * @namespace
 */
namespace cmscode\txtbd;

/**
 * класс для работы с текстовой базой данны
 *
 *Создание базы данных, update, insert и куча других методов для полноценной
 *текстовой базы данных своя струткура и файлы 
 *@package files
 *@subpackage classes
 *@version 0.0.5
 */
class DataBase
{
    /**
     * @var string
     */
    private $version = '0.0.5';

    /**
     * получить название столбцов выбранной таблицы
     *
	 * смотря какой параметр выбра в поле $number если 0или1 то return
	 *вернет string, если 2или3 то вернет array массив
	 *
     *$number - int от 0 до 3 целое число;
     *0- передает массив вида [0]=>value и тд
     *1- передает массив вида [value]=>0 и тд
     *
     *
     *
     *@param string $namebd должен содержать путь относительный без обратных слешей 
     *@param string $p содержит путь относительный может принемать значения: ./,../,../../ и так далее.
     *@param int $number щаблон вывода значений принемает 4 целых  значения от 0-3
	 *@return  string or array
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

    /**
     * функция для считывания параметров базы данных.
	 *
	 *Параметры такиеже как в 1й функции класса
	 *@return array
	 */
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

    /**
	 *генератор перебора полей файла
	 *
     *для более быстрого открывания в
	 *генераторе и экономии памяти при больших файлах бд.
	 *пример работы с генератором $gen->send('stop'); точка выхода из генератора
	 * аналог return
	 *<pre>$gen=select('bd/com_an_k2/categories/cmscode.php','');
	 *foreach ($gen as $line) {
	 *$data=explode('^',$line);
	 *if($line['0']=='21'){ $massiv[]=$data;$gen->send('stop');} 
     *}</pre>
     */
    public function select($namebd,$p) 
	{
     $file = fopen($p.$namebd.'.php', "r"); $line= fgets($file);
         while (!feof($file))  {
	     $line= fgets($file);
         $cmd=(yield $line);
             if ($cmd == 'stop') {
             fclose($file);
             return;
            }
	    }
	 fclose($file);
    }
	
    /**
     *Служит для добавления новых строк в текстовую бд
	 *	 
	 *с названиями столбцов бд в виде массив и их значений <br>
	 *в ввиде массива в том же порядке должны идти.
	 *@param array $field_name array($fildNameBd1,$fildNameBd2,..$fildNameBd_n);
	 *@param array $value array(value1,value2,...,value_n);
	 *@param string $namebd
	 *@param string $p
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
         if(array_key_exists($field_name[$k], $stolbci)){$data[$stolbci[$field_name[$k]]['0']]=$this->obrezka_poley_cc($this->zss_replace_cc($value[$k]),$stolbci[$field_name[$k]]['1']);}	
	    }
     $znachenie=trim(implode('^',$data));
     $a="\n".$znachenie;	
     $dfdsg=file_put_contents($file, $a, FILE_APPEND | LOCK_EX);
     if($dfdsg===false){return 'no';}
     else{return 'ok';}
    }

    /**
	 *служит для обновления строки тектовой бд указанных столбцов $fn и их значений $v в виде массива;
	 *id - айди строки которую надо обновить
	 *
	 *@param array $field_name array($fildNameBd1,$fildNameBd2,..$fildNameBd_n);
	 *@param array $value array(value1,value2,...,value_n);
	 *@param string $n ==$namebd
	 *@param string $p
	 *@param string $id номер id редактируемой строки
     */
     public function update($n,$fn,$v,$id,$p)
	{
     $file = $p."$n.php";
     $h= fopen("$file", "rb");
     $d= fgets($h);
     $messeg='no';
         while (($d = fgets($h)) !== FALSE) {
         $data=explode('^',$d);
             if($id==$data['0']){
             $messeg='ok';		
             $b=implode('^',$data);
             $b=$this->trim_replace_cc($b);
             break;
            }
	    }
	 fclose($h);
	     if($messeg=='ok'){
		     $stolbci=$this->getFieldsName("$n","$p",'3');
		     $vs=count($fn);
		     for($k=0;$k<$vs;$k++){ 
			     if(array_key_exists($fn[$k], $stolbci)){
			     $data[$stolbci[$fn[$k]]['0']]=$this->obrezka_poley_cc($this->zss_replace_cc($v[$k]),$stolbci[$fn[$k]]['1']);}	
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

    /**
	 *генератор перебора полей файла для более быстрого открывания в генераторе
	 *
	 *и экономии памяти при больших файлах бд используется в кешировании. отличие
	 *от select только незначительное но это необходимо чтобы правильно подсчитать
	 *количество символов строки каждой от начала
	 */
public function fileCacheLines($filename) {
    $file = fopen($filename.'.php', "r");
    while (!feof($file))  {$line= fgets($file);
$cmd=(yield $line);
if ($cmd == 'stop') {
fclose($file);
return;
        }
		}fclose($file);
}

/*cache_cc служит для создания кеша запросов к бд и более быстрому открыванию нужной строки из нее*/

public function cache($p,$puti,$namebd,$datecache,$config_site){

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
/* if($zzzzz['0']=='21'){ $gen->send('stop');} пример остановки генератора*/
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

/*del_cc удаляет строку из файла и сохраняет изменения. по его id если столбца id небудет то и неудалит ничего*/
public function delLine($namebd,$id,$p){

$gen=$this->select($namebd,$p);
	 foreach ($gen as $line) {
	 $data=explode('^',$line);
	 if($line['0']==$id){ $old_data=$line; $gen->send('stop');} 
                        }
if($old_data!=null){}
else{}


}

/* delbd_cc удаляет бд текстовую с категорией и всеми её файлами*/
public function delBd($namebd,$p){}

/* create_cc - создание столбцов тектсовой бд
$znachenie - строка с введенными в нее названиями столбцов будущей бд как с параметром который ставится через пробел так и без него 
пример1 с параметром:
id SMALLTEXT^title 62500^shablon 256
В конце строки не должно быть никаких переводов на другую строку, хотя мы на всякий случай удаляем проблемные переводы \r \n 
^ - служит разделителем названий столбцов. его лучше неиспользовать в именах столбцов.
пример2 без параметров:
id^title^shablon
без параметров ставится значение длины каждого стобца 65535 - это число символов которое может поместить в себя стобец остальное будет обрезано.
создает так же текстовый файл .txt в котором хранится число id последней статьи добавленной и при добавлении новой статьи происходит ++ данного числа.

id должен идти 1м полем без вареантов по нему будет происходить обновление строк удаление и вставка новых строк. либо переопределяете функции под себя.

*/
public function create($namebd,$znachenie,$p)
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

/* createcod_cc запись и создание любого кода который нам необходим */
public function createcod($name_puti,$znachenie,$p){
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

$t = mb_substr($t, 0, $chislosimvol,"UTF-8");/* если 5 то если они русс то и выведет 5ть русских букв но они по 2 символа считаются в вардампе*/
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
/* если дата подходит то проходит по всем пунктами пока нет проверки на точность указанной даты*/
$t[1]; /* год токо с годом траблы будут елси пользователь укажет к примерe 0090 год то он тоже прокатит и как бы неочень*/
if($t[2]>12){$t[2]='01';}
elseif($t[2]=='00'){$t[2]='01';}
if($t[3]>31){$t[3]='01';}
elseif($t[3]=='00'){$t[3]='01';}
if($t[4]>23){$t[4]='00';}
if($t[5]>59){$t[5]='00';}
if($t[6]>59){$t[6]='00';}
return $t[1].'.'.$t[2].'.'.$t[3].'.'.$t[4].'.'.$t[5].'.'.$t[6];}
             return date('Y.m.d.H.i.s');                      }

 /* perep служит для переопределения путей компонентов и модулей чтобы можно было свои правки делать едре и они не будут затираться
 для создания файла переопределения вам надо в папке components или modul создать папку новую с прифексом perep_названиекомопонента или perep_названиемодуля вставить файл в тойже структуре папок что и в основном модуле или комопненте
 
 Другими словами вас не устраивает функционал в компоненте или модуле то вы всегда можете написать свой переопределенный, но учтите если ядро обновилось то переопределенные файлы будете править вы сами иначе будет куча ошибок в 
 
 $putibezcom - путь до файла без названия папки комопнента или модуля
 $p - относитетельный параметр пути может принемать значения ./,../
 $com - принемает 2 значения components или modul
 $namecome - название компонента или модуля без приcтавки com_ или mod_ и без дефиса / \
 функция должна возвратить путь до файла переопределенного или же дефолтный путь без переопределения.
 пример perep ('an_k2/addcat/default.txt',$p,'components','an_k2')
 */

 public function perep_cc ($putibezcom,$p,$com,$namecome)
 { clearstatcache();
 if($com=='components'){$pristavka1='components/perep_';$pristavka2='components/com_';}
 elseif($com=='modul'){$pristavka1='modul/perep_'; $pristavka2='modul/mod_';}
 else{return 'error '.$p.'functions/_cc.php 404 lines не указан 3й параметр компонент это или модуль';}
$file=$p.$name;
$put1=$p.$pristavka1.$namecome.'/'.$putibezcom;
$put2=$p.$pristavka2.$namecome.'/'.$putibezcom;
 if(file_exists($put1)){ return $put1;}
 else{ return $put2;}//вернет файл пути основной но учтите если файла нет вылезет ошибка.

 }			 
			 
			 
			 
			 



}

?>