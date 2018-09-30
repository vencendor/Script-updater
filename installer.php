<?
error_reporting(-1);
ini_set("display_errors",1);

$path_install="install/parse_button.php";

$MOD_PATH=dirname($path_install)."/";
$ROOT_PATH=dirname(__FILE__)."/";

$pat[]="#\\\\#";
$repl[]="\\\\\\\\";
/**/
$pat[]="#([\(\)\{\}\[\]\+\*\.\|]{1})#";
$repl[]="\\\\$1";

$pat[]="#\"|'#";
$repl[]="[\"']+";

$pat[]="#[\s]+#";
$repl[]="[^<>\{\}]*";

$pat[]='#\$#';
$repl[]='\\\$';
/**/
$ERROR=false;

function find_preg_part($reg,$str){
	global $pat,$repl;
	$ret_str="";
	
	$reg=preg_replace("#\s+#"," ",$reg);
	
	$tab_reg=explode(" ",$reg);
	
	$pi=0;
	foreach($tab_reg as $n=>$v){
		$temp_ar=array_slice($tab_reg,0,$n);
		$temp_str=implode(" ",$temp_ar);
		$temp_str="#".preg_replace($pat,$repl,$temp_str)."#Usi";
		if(preg_match($temp_str,$str,$m))  $pi++;   else  break;
		$ex_str=$m[0];
	}
	
	$ret_str=htmlentities($ex_str)." <span style='color:#FF0000'>".htmlentities($tab_reg[$pi-1])."</span> ".htmlentities(implode(" ",array_slice($tab_reg,$pi)));

	return $ret_str;
}

echo "���������� ����� ������������ ... ";
if(is_file($path_install)) {
	include $path_install;
} else die("�� ������ ���� ������������ ");
echo "OK <br>";

if(isset($_GET['do']) and $_GET['do']==="install") {
	if(!is_file($MOD_PATH.$MOD_NAME)) {
	echo "������ ��������� <br>";

	echo "�������� ������������ ...<br>\n ";
	
	if(!is_writable($MOD_PATH)) $ERROR[]="����� ������ �� �������� ��� ������";
	
	if(isset($MOD_FILES) and is_array($MOD_FILES))
	foreach($MOD_FILES as $fl) {
		if(!is_readable($MOD_PATH.$fl['source'])) $ERROR[]=$MOD_PATH.$fl['source']." �� ������";
		if(file_exists($ROOT_PATH.$fl['destin'])) $ERROR[]=$ROOT_PATH.$fl['destin']." ��� ���������� ";
		if(!is_writable (dirname($ROOT_PATH.$fl['destin'])) ) $ERROR[]=$ROOT_PATH.$fl['destin']." �� ������� ��� ������  ";
	}

	foreach($MOD_CHANGE as $n=>$mod) {
		echo "������� ".($n+1)." <br>\n";
		
		if(isset($mod['begin'])) {
			$temp_begin=$mod['begin'];
			$temp_end=$mod['end'];
		} else $temp_reg=$mod['replace'];
		
		if(!is_writable($ROOT_PATH.$mod['file'])) $ERROR[]=$ROOT_PATH.$mod['file']." �� ������� ��� ������ � ���� ";
		if(!is_writable(dirname($ROOT_PATH.$mod['file']))) $ERROR[]=$ROOT_PATH.$mod['file']." �� ������� ��� ������ � ����� ";
		if(is_file($ROOT_PATH.$mod['file']."_temp")) $ERROR[]=$ROOT_PATH.$mod['file']."_temp"." ��� ����������";
		$fstr="";
		$fstr=file_get_contents($ROOT_PATH.$mod['file']);
		$beginflag=false;
		$endflag=false;

		$MOD_CHANGE_F[$mod['file']][$n]=$mod;

		if(!isset($mod['replace'])){
			$mod['begin']="#".preg_replace($pat,$repl,$mod['begin'])."#Uis";
			$mod['end']="#".preg_replace($pat,$repl,$mod['end'])."#Uis";	
			
			if(!preg_match($mod['begin'],$fstr,$m)) {
			$ERROR[]="�� ������� ������ ������� ��� ������. <br> � ����� ".$mod['file']." ������� �����:".($n+1).". <br> ������ : \"".find_preg_part($temp_begin,$fstr)."\"";}
			$mod['begin']=$m[0];
			if(!preg_match($mod['end'],$fstr,$m)) {
			$ERROR[]="�� ������ ����� ������� ��� ������. <br> � ����� ".$mod['file']." ������� �����:".($n+1).". <br> ������ : \"".find_preg_part($temp_begin,$fstr)."\"";}
			$mod['end']=$m[0];

			$beginflag=strpos($fstr,$mod['begin']);
			$endflag=strpos($fstr,$mod['end']);
					
			if($beginflag!==false and $endflag!==false)
			if($endflag>$beginflag){
				$beginflag+=strlen($mod['begin']);
				if(preg_match("#[^\s]+#",substr($fstr,$beginflag,$endflag-$beginflag))) {
					$ERROR[]="������ � ����� �� ���������������. � ����� \"".$mod['file']."\" ";
				} else {
					$MOD_CHANGE_F[$mod['file']][$n]['begin']=$mod['begin'];
					$MOD_CHANGE_F[$mod['file']][$n]['end']=$mod['end'];
				}
			}else{
				$endflag+=strlen($mod['end']);
				if(preg_match("#[^\s]+#",substr($fstr,$endflag,$beginflag-$endflag))) {
					$ERROR[]="������ � ����� �� ���������������. � ����� ".$mod['file']." ������� �����:".($n+1)." <br> 
					������ : \"".find_preg_part($temp_begin,$fstr)."\"";
				} else {
					$MOD_CHANGE_F[$mod['file']][$n]['begin']=$mod['end'];
					$MOD_CHANGE_F[$mod['file']][$n]['end']=$mod['begin'] ;
				}
			}
		} else {
			$mod['replace']="#".preg_replace($pat,$repl,$mod['replace'])."#Uis";
			preg_match($mod['replace'],$fstr,$m);
			$mod['replace']=$m[0];
			if(strpos($fstr,$mod['replace'])===false) { $ERROR[]="�� ������� ������ ��� ������. <br> � ����� ".$mod['file']."  N
			".($n+1)." <br> ������ : \"".find_preg_part($temp_reg,$fstr)."\"";
			}
			else 
			$MOD_CHANGE_F[$mod['file']][$n]['replace']=$mod['replace'] ;
		}
		unset($mod);
	}
	echo "OK <br>";
	} else $ERROR[]="������ ��� ���������� <br/>";
	
	if(!$ERROR) {
		
		echo "������ ��������� ... <br>";
		
		if(isset($MOD_FILES) and is_array($MOD_FILES))
		foreach($MOD_FILES as $fl) {
			copy($MOD_PATH.$fl['source'],$ROOT_PATH.$fl['destin']);
		}

		foreach($MOD_CHANGE_F as $n=>$modf) {
			$fstr=file_get_contents($ROOT_PATH.$n);
			copy($ROOT_PATH.$n,$ROOT_PATH.$n."_temp");
			
			$beginflag=false;
			$endflag=false;
			
			foreach($modf as $mod_rep){
				if(!isset($mod_rep['replace'])){
					$fstr=preg_replace("#".preg_replace($pat,$repl,$mod_rep['begin'])."[\s]*".preg_replace($pat,$repl,$mod_rep['end'])."#Uis",$mod_rep['begin']." \n ".$mod_rep['string']." \n ".$mod_rep['end'],$fstr);
				} else {
					$fstr=str_replace($mod_rep['replace'],$mod_rep['string'],$fstr);
				} 
			}
			
			file_put_contents($ROOT_PATH.$n,$fstr);

		}
		echo "OK <br>";
		$f=fopen($MOD_PATH.$MOD_NAME,"w");
		fwrite($f,"true");
		fclose($f);
		echo "������ ����������! <br>";
		
	} else { 
		echo " <span style='color:#FF0000;' >�������� ������������ �������� ������! </span> <br>";
		foreach($ERROR as $n=>$er) echo ($n+1)." - ".$er."<br>"; 
	}
	echo "<a href='?do=start'> Home </a>";

} elseif(isset($_GET['do']) and $_GET['do']==="uninstall") {
	if(is_file($MOD_PATH.$MOD_NAME)) {
	echo "�������� ����� ������ ...<br/>";
	if(isset($MOD_FILES) and is_array($MOD_FILES))
	foreach($MOD_FILES as $fl) {
		if(is_file($ROOT_PATH.$fl['destin']))
		unlink($ROOT_PATH.$fl['destin']);
		else 
		echo "���� �� ���������� - \"".$ROOT_PATH.$fl['destin']."\"<br>";
	}
	echo "OK<br>";
	
	echo "�������� ������������� ���� ... <br/>";
	$MOD_CHANGE_F=array();
	foreach($MOD_CHANGE as $mod) {
		$MOD_CHANGE_F[$mod['file']]=$mod;
	}
	
	
	foreach($MOD_CHANGE_F as $n=>$modf) {
		//$MOD_CHANGE_F[$mod['file']]=$mod;
		if(is_file($ROOT_PATH.$modf['file']."_temp") and is_writable($ROOT_PATH.$modf['file']."_temp")) {
			unlink($ROOT_PATH.$modf['file']);
			rename($ROOT_PATH.$modf['file']."_temp",$ROOT_PATH.$modf['file']);
		} else echo "�� ������� ����� ���������� ����� ����� - \"".$ROOT_PATH.$modf['file']."\"";
	}
	echo "OK<br>";
	unlink($MOD_PATH.$MOD_NAME);
	} else echo "������ �� ���������� <br/>";
	echo "<a href='?do=start'> Home </a>";
} else {
	if(!is_file($MOD_PATH.$MOD_NAME)) {
	?><a href='?do=install'> ���������� </a><?
	} else {
	?><a href='?do=uninstall'> ���������������� </a><?
	}
}

?>