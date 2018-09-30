<?php

session_start();
error_reporting(E_ALL^E_NOTICE);
ini_set('display_errors',1);

define('FLV_PER_PAGE',20);
define('DATALIFEENGINE', true);
define('ROOT_DIR', '../..');
define('ENGINE_DIR', '..');

require_once (ENGINE_DIR.'/inc/include/init.php');
/*
require_once ENGINE_DIR.'/classes/mysql.php';
require_once ENGINE_DIR.'/data/dbconfig.php';
*/

if($is_loged_in == FALSE) {
	die("Нет доступа");
}


class parsing{
  public $useragent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)';
  public $useragent_1 = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.1.7)';
  public $ch;
  public $content;
  public $url;
  function initial_curl($url){
  	
  	$this->ch = curl_init($url);
  	return $this ->ch;
  	
  }
  function get_page_content($url , $agent){
  	//$ips = array("95.169.184.175");
	global $config;
	$ips = $config['server_ip'];
	
  	$min = 0;
  	$max = sizeof($ips) - 1;
  	$num_ip = rand($min , $max);
  	$get_ip = $ips[$num_ip];
  	$this ->ch = $this ->initial_curl($url);
  	curl_setopt($this ->ch , CURLOPT_URL , $url);
  	curl_setopt($this ->ch , CURLOPT_RETURNTRANSFER , 1);
  	curl_setopt($this ->ch , CURLOPT_HEADER , 1);
  	//curl_setopt($this ->ch , CURLOPT_FOLLOWLOCATION , 5);
  	curl_setopt($this ->ch , CURLOPT_ENCODING , "");
  	curl_setopt($this ->ch , CURLOPT_INTERFACE , $get_ip);
  	if ($agent == 5){
  	  $agent = $this ->useragent_1;
  	} elseif ($agent == 4){
  	  $agent = $this ->useragent;	
  	}
  	curl_setopt($this ->ch , CURLOPT_USERAGENT , $agent);
  	curl_setopt($this ->ch , CURLOPT_CONNECTTIMEOUT , 10);
  	curl_setopt($this ->ch , CURLOPT_TIMEOUT , 10);
  	
  	/*---------------- Вытаскивания из страницы ------------------------*/
  	
  	$content = curl_exec($this ->ch);
  	$error = curl_errno($this ->ch);
  	$error_mess = curl_error($this ->ch);
  	$header = curl_getinfo($this ->ch);
  	
  	
	
	// Проверка на существование редиректа и дополнительное переодресовывание 
	if(strpos($content,"Location:")!==false) {
	
		preg_match("#Location: (.*)#",$content,$m);
		$url=trim($m[1]);
		echo $url;
		curl_setopt($this ->ch , CURLOPT_URL , $url);
		curl_setopt($this ->ch , CURLOPT_RETURNTRANSFER , 1);
		curl_setopt($this ->ch , CURLOPT_HEADER , 1);
		//curl_setopt($this ->ch , CURLOPT_FOLLOWLOCATION , 5);
		curl_setopt($this ->ch , CURLOPT_ENCODING , "");
		curl_setopt($this ->ch , CURLOPT_INTERFACE , $get_ip);

		curl_setopt($this ->ch , CURLOPT_USERAGENT , $agent);
		curl_setopt($this ->ch , CURLOPT_CONNECTTIMEOUT , 10);
		curl_setopt($this ->ch , CURLOPT_TIMEOUT , 10);
		
		/*---------------- Вытаскивания из страницы ------------------------*/
		
		$content = curl_exec($this ->ch);
		$error = curl_errno($this ->ch);
		$error_mess = curl_error($this ->ch);
		$header = curl_getinfo($this ->ch);
		
	}
  	
	curl_close($this ->ch);
  	$info['header'] = $header;
  	$info['content'] = $content;
  	$info['error'] = $error;
  	$info['error_mess'] = $error_mess;
  	  	
  	return $info;
  	/*-----------------------------------------------------------------*/
  }
  function extract_flv($content , $site , $url=""){
  	$this ->content = $content;
  	$this ->site = $site;
  	$this ->url = $url;
  	if ($this ->site == "xvideos"){
	  	$this ->content = preg_match('/<embed.+flashvars="(.*)">/U',$this ->content,$matches);
	  	$this ->content = $matches[1];
	  	$this ->content = preg_match('/flv_url=(.+)&amp;/U', $this ->content, $matches);
	  	$this ->content = urldecode($matches[1]);
  	} elseif ($this ->site == "redtube"){
  		
  	  if ($this ->url != null){
   	  	/*$part = explode("redtube.com/",$this ->url);
  	  	$this ->content = $this ->grab_red_tube($part[1]);*/
   	  	$this ->content = $content;
   	  	$this ->content = preg_match('~.+hashlink=([^ "\n\r]+)["|&|\n|\r].+~U',$this ->content, $matches);
   	  	$this ->content = urldecode($matches[1]);
		
  	  }
  		
  	} elseif ($this ->site == "pornhub"){
  		$this ->content = $content;
  		$this ->content = preg_match('~<param.+options=(.*)"/>~U',$this ->content,$matches);
  		$this ->content = $matches[1];
  		$this->content = $this ->get_page_content($this ->content , 5);
  		$this ->content = $this ->content['content'];
  		$this ->content = preg_match('~<flv_url>(.*)</flv_url>~U',$this ->content,$matches);
  		$this ->content = $matches[1];
  	}
  	return $this ->content;
  }
  
  function grab_red_tube($id){
  	
  	/*-----------------------------------------------------*/
  	
  	$s = $id; 
  	if ($s == "") { 
         
      $s = "1"; 
     
    }
    $pathnr = floor($s/1000); 
    $l = strlen($s);
    $i=1;
    while ($i <= (7-$l)) { 
        
    	$s = "0".$s;
        $i++;
        
    }
    $l = strlen($pathnr); 
    for ($i = 1; $i <= 7 - $l; $i++) { 
        $pathnr = "0".$pathnr; 
    } 
    $xc = array("R", "1", "5", "3", "4", "2", "O", "7", "K", "9", "H", "B", "C", "D", "X", "F", "G", "A", "I", "J", "8", "L", "M", "Z", "6", "P", "Q", "0", "S", "T", "U", "V", "W", "E", "Y", "N"); 
    $code = ""; 
    $qsum = 0; 
    for ($i = 0; $i <= 6; $i++) { 
        $qsum = $qsum + substr($s,$i,1)*($i + 1); 
    }

    $s1 = $qsum; 
    $qsum = 0; 
    for ($i = 0; $i < strlen($s1); $i++) { 
        $qsum = $qsum + substr($s1, $i,1); 
    } 
 
    if ($qsum >= 10) $qstr = $qsum; 
    else $qstr = "0".$qsum; 

    $c = ord(substr($s, 3,1)) - 48 + $qsum + 3;
    $code = $code.$xc[$c];

    $code = $code.substr($qstr, 1,1);
    $c = ord(substr($s,0,1)) - 48 + $qsum + 2;
    $code = $code.$xc[$c];
    $c = ord(substr($s,2,1)) - 48 + $qsum + 1;
    $code = $code.$xc[$c]; 
    $c = ord(substr($s,5,1)) - 48 + $qsum + 6;
    $code = $code.$xc[$c]; 
    $c = ord(substr($s,1,1)) - 48 + $qsum + 5;
    $code = $code.$xc[$c];
    $code = $code.substr($qstr,0,1); 
    $c = ord(substr($s,4,1)) - 48 + $qsum + 7;
    $code = $code.$xc[$c]; 
    $c = ord(substr($s,6,1)) - 48 + $qsum + 4;
    $code = $code.$xc[$c]; 
    $content_video = 'http://dlembed.redtube.com/_videos_t4vn23s9jc5498tgj49icfj4678/'.$pathnr."/".$code.".flv";
    return $content_video;
  	
  	/*-----------------------------------------------------*/
  }
    /*------------------------------- Картинки --------------------------------*/
  
    
   function image_link_grabbing($url){
  	$this ->url = $url;
  	if ($this ->url != null){
   	 $part = explode("redtube.com/",$this ->url);
   	 $id = $part[1];
  	}
	if ($id == ""){
		
	   $id = "1";
	    
	}
	$pathnr = floor($id/1000);
	$l = strlen($pathnr); 
	for ($i = 1; $i <= 7 - $l; $i++) { 
	 $pathnr = "0".$pathnr; 
	}
	$a = strlen($id);
	$dir = $id;
	for ($b = 1; $b <= 7 - $a; $b++) { 
	 $dir = "0".$dir; 
	}
	for ($i = 1; $i < 20; $i++){
	if ($name < 10){
	  $img_name = "00".$i;
	} elseif (($name > 10) || ($name < 100)){
	  $img_name = "0".$i;
	}
	 $static_link = "http://thumbs.redtube.com/_thumbs/".$pathnr."/".$dir."/".$dir."_".$img_name.".jpg";
	 $static_link_1 = "http://thumbs.redtube.com/_thumbs/".$pathnr."/".$dir."/".$dir."_".$img_name."s.jpg";
	 $size = "";
	 $size_1 = "";
	 $size = @getimagesize($static_link);
	 $size_1 = @getimagesize($static_link_1);
	 $good_link = "";
	 if ($size != null){
	   if (($size['mime'] == "image/jpeg") && (($size[0] != null) && ($size[1] != null))){
	    $good_link = $static_link;
	   }
	   break; 	
	 } elseif ($size_1 != null){
	   if (($size_1['mime'] == "image/jpeg") && (($size_1[0] != null) && ($size_1[1] != null))){
	    $good_link = $static_link_1;
	   }
	   break;	
	 } else $good_link = "";
	}
	return $good_link;
  	
  }
  
  function extract_image($content , $image, $site){
  	 $this ->content = $content;
  	 $this ->site = $site;
	 
     if ($this ->site == "xvideos"){
  	  $this ->content = preg_match_all('~<img.+src="(.*)" width="120".+>~U', $this ->content, $matches);
  	  $this ->content = $matches[1][0];
  	 } elseif ($this ->site == "redtube"){
  	   $this ->content = $content;  		
  	} elseif ($this ->site == "pornhub"){
  	  $gen_url = "http://pics1.pornhub.com/thumbs/";
  	  $name_file = "small.jpg";
  	  $this ->content = preg_match_all('~<input.+id="video_0" value="(.*)"/>~',$this ->content, $matches);
  	  $this ->content = $matches[1][0];
  	  $len = strlen($this ->content);
  	  $first_dir = substr($this ->content, 0,1);
  	  $sec_dir = substr($this ->content, 1,3);
  	  $third_dir = substr($this ->content, 4,$len);
  	  $l_first = strlen($first_dir);
  	  for ($i=1; $i <= (3-$l_first); $i++){
  	  	$first_dir = "0".$first_dir;
  	  }
      $this ->content = $gen_url.$first_dir."/".$sec_dir."/".$third_dir."/".$name_file;
  	} else die("Картинки пока парсится только с Xvideos.com!");
  	  if (strpos($this ->content , "jpg") > 0){
  	  	
  	  	$extension = "jpg";
  	  	
  	  } elseif (strpos($this ->content , "gif") > 0){
  	  	
  	  	$extension = "gif";
  	  	
  	  } else $extension = "";
   	  if ($extension != ""){
  	  	$name_image = ROOT_DIR."/parse/images/".$image."_tmp.".$extension;
		$imagecont=file_get_contents($this ->content);

		file_put_contents($name_image,$imagecont);
  	  	//copy($this ->content , $name_image);
  	  	$new_image = ROOT_DIR."/parse/images/".$image.".".$extension;
  	  	$nr_image = $image;
  	  	if (is_file($new_image)){
  	  	  unlink($new_image);
  	  	}
        $image_param = getimagesize($name_image);
        switch ($image_param['mime']){
        	case "image/jpeg": {$ext = "jpg"; $image_create = "imagecreatefromjpeg"; $imagemode = "imagejpeg"; break;}
        	case "image/gif": {$ext = "gif"; $image_create = "imagecreatefromgif"; $imagemode = "imagegif"; break;}
        }
        if (($ext == "jpg") || ($ext == "gif")){
          $orig_width = $image_param[0];
          $orig_height = $image_param[1];
          $new_width = 180;
          $new_height = 144;
          $quality = 100;
          $image = imagecreatetruecolor($new_width,$new_height);
          $image_tmp = $image_create($name_image);
          imagecopyresampled($image,$image_tmp, 0, 0, 0, 0, $new_width, $new_height,$orig_width,$orig_height);
          $imagemode($image,$new_image, $quality);
          imagedestroy($image);
          unlink($name_image);
          return ($nr_image.".".$ext);
        }
  	  }
  }
  
}


 $parse = new parsing();
 $address = "";
  /*
  
  if (isset($_GET['delete']) && intval($_GET['delete'])){
    $extensions = array("jpg","gif");
    for ($i = 0; $i < sizeof($extensions); $i++){
     $image = "/home/lolitka/data/www/xlolitka.com/parse/images/".$_GET['delete'].".".$extensions[$i];
     if (is_file($image)){
      unlink($image);
     }
    }
  	$del_query = "DELETE FROM flv_adress WHERE id='{$_GET['delete']}' LIMIT 1"; 
  	$db->query($del_query);
	echo "Удалена ссылка {$_GET['delete']} ";
  }
  if (isset($_GET['redact']) && intval($_GET['redact'])){
  	
  if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['redact_me'])){
  	$redact_form = <<<HTML
<div style="min-width: 100px; margin: 10px; border: 1px solid gray;">
<center>
<p>
<font>Новый адресс сохранен!</font>
</p>
</center>
</div>
HTML;
$new_addr = $_POST['new_addr'];
$query_update = "UPDATE flv_adress SET addr='{$new_addr}' WHERE id='{$_GET['redact']}' LIMIT 1";
$db->query($query_update);
if (strpos($new_addr, "xvideos") > 0){
      $site = "xvideos";
      $agent = 4;
      $res = $parse ->get_page_content($new_addr , $agent);
      $img_name = $_GET['redact'];
      $n_img = $parse ->extract_image($res['content'], $_GET['redact'], $site);
      $update = "UPDATE flv_adress SET img_add = '{$n_img}' WHERE id='{$_GET['redact']}'";
      $db->query($update);
}
if (strpos($new_addr, "redtube") > 0){
      $site = "redtube";
      $agent = 5;
      $img_name = $_GET['redact'];
      $url_img = $parse ->image_link_grabbing($new_addr);
      $n_img = $parse ->extract_image($url_img, $img_name, $site);
      $update = "UPDATE flv_adress SET img_add = '{$n_img}' WHERE id='{$img_name}'";
      $db->query($update);
}
if (strpos($new_addr, "pornhub") > 0){
   	  $site = "pornhub";
      $agent = 5;
      $res = $parse ->get_page_content($new_addr , $agent);
      $img_name = $_GET['redact'];
      $n_img = $parse ->extract_image($res['content'], $img_name, $site);
      $update = "UPDATE flv_adress SET img_add = '{$n_img}' WHERE id='{$img_name}'";
      $db->query($update);
}
header("Refresh: 2; url=/parse/index.php?do=admin&page={$_GET['page']}");
} else {
$q_select = "SELECT addr FROM flv_adress WHERE id='{$_GET['redact']}' LIMIT 1";
$res = $db->query($q_select);
while ($get_row = $db->get_row($res)){
  $addr = $get_row[0];
}
$redact_form = <<<HTML
<div style="min-width: 100px; margin: 10px; border: 1px solid gray;">
<center>
<p>
<font>Редактор адресов</font>
</p>
<form action="" method="post" enctype="multipart/form-data">
 <label for="address">Адрес:&nbsp;</label><input type="text" name="new_addr" value="{$addr}" size="65" style="font-size: 12px;" />
 <p>
 <input type="submit" name="redact_me" value="Сохранить" />
 </p>
</form>
</center>
</div>
HTML;
}
  } else $redact_form = "";
  
  /**/
  function view_video_date($addrss) {
  global $config;
	
	if(!isset($addrss[0]) and sizeof($addrss)>=3 ){
		$i=0;
		foreach($addrss as $n=>$v) {
			$addrss[$i]=$v;
			$i++;
		}
	}

		 if ($i == 1){
      $background = "#F5F5F5;";
    }
    if ($i == 2){
      $i = 0;
      $background = "#fff;";
    }
    $autoplay="";
	if(strpos($addrss[1],"xvideos.com") ) {
		$player_type="play2.swf";
		$autoplay="autostart=true&";
		}
	else 
		$player_type="play.swf";
		
		$autoplay="autostart=true&";
			
	$domain_name=$config['http_home_url'];
	
    $code = '<object id="videoplayer18640" type="application/x-shockwave-flash" data="'.$domain_name.'kino/'.$player_type.'" width="480" height="415"><param name="bgcolor" value="#ffffff" /><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="wmode" value="opaque" /><param name="movie" value="'.$domain_name.'kino/'.$player_type.'" />
<param name="flashvars" value="comment=порно&'.$autoplay.'st='.$domain_name.'player/video8-986.txt&file='.$domain_name.'parse/loading.php?flvid='.$addrss[0].'" /></object>';

if ($addrss[2] != ""){  
$img_add = "[center][img]http://".$_SERVER['HTTP_HOST']."/parse/images/".$addrss[2]."[/img][/center]";
$kartinka = <<<HTML
 
HTML;
} else {
  $img_add = "";
  $kartinka = "";
}
$str_out = <<<HTML
  <tr style="background:{$background}">
   <td>
   {$addrss[0]}
   </td>
   <td>
    <label for='flv_object_{$addrss[0]}' > Код: </label><input type="text" value='{$code}' size="45" name="flv_object_{$addrss[0]}" id="flv_object_{$addrss[0]}"  style="margin: 10px;" onclick="select();"/> <br/>
    <label for='flv_image_{$addrss[0]}' > Картинка: </label><input tyep="text" value='{$img_add}' size="45" name="flv_image_{$addrss[0]}" id="flv_image_{$addrss[0]}" style="margin: 10px;" onclick="select();"/>
   </td>
   <td>
    <a href="javascript:void()" onclick='paste_video_date({$addrss[0]})'  style="margin: 5px; text-decoration:none;">Вставить</a> <br/>
	<!---
	<a href="?do=admin&amp;page={$curr_page}&amp;redact={$addrss[0]}" style="margin: 5px; text-decoration:none;">Редактировать</a> <br/>
	<a href="?do=admin&amp;page={$curr_page}&amp;delete={$addrss[0]}" style="margin: 5px; text-decoration:none;">Удалить</a> <br/>
	--->
   </td>
  </tr>
HTML;

	return $str_out;
  
  }

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<body>
<script> 
function paste_video_date(vid){
	img_date=document.getElementById('flv_image_'+vid);
	vid_date=document.getElementById('flv_object_'+vid);
	window.parent.document.forms['addnews'].short_story.value+=img_date.value;
	window.parent.document.forms['addnews'].full_story_obj.value+=vid_date.value;
	dt=window.parent.document.getElementById("parser_div");
	dt.style.display='none';
} </script>
<style>  a{color:	#000000; }   #links{padding:15px;} .paginator a,.paginator span{padding:5px;} </style>
<!-- <div id='links'> <a href='?do=add'> Добавить </a> | <a href='?do=list'> Список </a></div> -->
<div> 
<?php
	
	if(isset($_GET['page']))
	$page=(int) $_GET['page'];
	else $page=0;
	
	
	if($_GET['do']==="list") {
		/*
		$q="select count(*) as num from flv_adress  ";
		$rez=$db->query($q);
		$count=$db->get_row($rez);
		$total_flv=$count['num'];
		$pages=ceil($total_flv/FLV_PER_PAGE);
		
		for($i=0;$i<$pages;$i++) {
			if($i<4 or ($i>$page-4 and $i<$page+4) or $i>$pages-4)
			if($i==$page) $paginator.="<span>".($i+1)."</span>"; else $paginator.="<a href='?do=list&page=$i'>".($i+1)."</a>";
			if($i>4 and $i<$page-4)  { $paginator.="<span>...</span>"; $i=$page-4;  }
			if($i>$page+4 and $i<$pages-4)  { $paginator.="<span>...</span>"; $i=$pages-4;  }
		}
		
		$q="select * from flv_adress order by id desc limit ".($page*FLV_PER_PAGE).",".FLV_PER_PAGE." ";
		$rez=$db->query($q);
		echo "<div class='paginator'> $paginator </div>";
		echo "<table>";
		while($flv=$db->get_row($rez)) {
			echo view_video_date($flv);
		}
		echo "</table>";
		echo "<div class='paginator'> $paginator </div>";	
		/**/
	} else {
		if (($_SERVER['REQUEST_METHOD'] == "POST") && (isset($_POST['click_me'])) && (strlen($_POST['address']) > 10)){
			$flv_page = $_POST['address'];
			$query = "INSERT INTO flv_adress (id , addr) VALUES('' , '{$flv_page}')";
			$db->query($query);
			echo mysql_error();
			if (strpos($flv_page, "xvideos") > 0){
			  $img_name = $db->insert_id();
			  $site = "xvideos";
			  $agent = 4;
			  $res = $parse ->get_page_content($flv_page , $agent);
$img_data = file_get_contents($flv_page);
//$n_img = $parse ->extract_image($res['content'], $img_name, $site);			  
$n_img = $parse ->extract_image($img_data, $img_name, $site);
			  $update = "UPDATE flv_adress SET img_add = '{$n_img}' WHERE id='{$img_name}'";
			  $db->query($update);
			}
			if (strpos($flv_page, "redtube") > 0){
				$site = "redtube";
				$agent = 5;
				$img_name = $db->insert_id();
				$url_img = $parse ->image_link_grabbing($flv_page);
				$n_img = $parse ->extract_image($url_img, $img_name, $site);
				$update = "UPDATE flv_adress SET img_add = '{$n_img}' WHERE id='{$img_name}'";
				$db->query($update);
			}
		   if (strpos($flv_page, "pornhub") > 0){
			  $site = "pornhub";
			  $agent = 5;
			  $res = $parse ->get_page_content($flv_page , $agent);
			  $img_name = $db->insert_id();
			  $n_img = $parse ->extract_image($res['content'], $img_name, $site);
			  $update = "UPDATE flv_adress SET img_add = '{$n_img}' WHERE id='{$img_name}'";
			  $db->query($update);
		   }
		   
		   echo "<table>".view_video_date(array($img_name,$flv_page,$img_name.".jpg"))."</table>";
		 } else {
		 ?><form action="" method="post" enctype="multipart/form-data" style="width: 300px;">
 <label for="address">Адрес:&nbsp;</label><input type="text" name="address" size="25" />
 <p>
 <input type="submit" name="click_me" value="Сохранить" />
 </p>
</form><?
		 
		 }

	
	
	}
	
	/*
    $q_sel = "SELECT * FROM flv_adress ORDER BY id DESC LIMIT ".$offset.",".$num_per_page."";
     $res = $db->query($q_sel);
     if (mysql_num_rows($res) > 0){
      $address = <<<HTML
<table border="1" style="text-align:center; width:450px;">
 <tr>
  <td>Ид</td>
  <td>Адрес</td>
  <td>Действия</td>
 </tr>
HTML;
   $i = 1;
   while ($addrss = mysql_fetch_row($res)){
    if ($i == 1){
      $background = "#F5F5F5;";
    }
    if ($i == 2){
      $i = 0;
      $background = "#fff;";
    }
    $autoplay="";
	if(strpos($addrss[1],"xvideos.com") ) {
		$player_type="play2.swf";
		$autoplay="autostart=true&";
		}
	else 
		$player_type="play.swf";
		
		$autoplay="autostart=true&";
		
	$domain_name="http://xlolitka.com/";
    $code = '<object id="videoplayer18640" type="application/x-shockwave-flash" data="'.$domain_name.'kino/'.$player_type.'" width="480" height="415"><param name="bgcolor" value="#ffffff" /><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="wmode" value="opaque" /><param name="movie" value="'.$domain_name.'kino/'.$player_type.'" />
<param name="flashvars" value="comment=порно&'.$autoplay.'st='.$domain_name.'player/video8-986.txt&file='.$domain_name.'parse/loading.php?flvid='.$addrss[0].'" /></object><br /><a target="_blank" title="Секс форум" href="http://xlolitka.com/go.php?b1f66a20f39e613680b96f9279ee7e93"><img alt="Секс форум" src="http://xlolitka.com/g.jpg" border="0" /></a>';

if ($addrss[2] != ""){  
$img_add = "[center][img]http://".$_SERVER['HTTP_HOST']."/parse/images/".$addrss[2]."[/img][/center]";
$kartinka = <<<HTML
 Картинка:&nbsp;<input tyep="text" value='{$img_add}' size="45" style="margin: 10px;" onclick="select();"/>
HTML;
} else {
  $img_add = "";
  $kartinka = "";
}
$address .= <<<HTML
  <tr style="background:{$background}">
   <td>
   {$addrss[0]}
   </td>
   <td>
    Код:&nbsp;<input type="text" value='{$code}' size="45" style="margin: 10px;" onclick="select();"/>
    {$kartinka}
   </td>
   <td>
    <a href="?do=admin&amp;page={$curr_page}&amp;redact={$addrss[0]}" style="margin: 5px; text-decoration:none;">Редактировать</a>&nbsp;|&nbsp;<a href="?do=admin&amp;page={$curr_page}&amp;delete={$addrss[0]}" style="margin: 5px; text-decoration:none;">Удалить</a>
   </td>
  </tr>
HTML;
 $i++;
}
$address .= "</table>";
     } else $address = "<font>-==Адрессов нету==-</font>";
?>
<?php echo $redact_form;?>
<div style="min-width: 100px; margin: 10px; border: 1px solid gray;">
<?php echo $address;?>
</div>
<div style="min-width: 150px; margin: 10px;">
<center>
 <?php
  if ($curr_page == 0){
  	$curr_page +=1;
  }
  $parse ->paginator($pages , $curr_page , "admin");
  */
  
 ?></div>
 </body>
</html>