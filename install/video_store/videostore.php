<?

if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}
#############################
######## Настройки #############
############################
$VIDEOS_PER_PAGE=20;   // количество видео на одной странице 
$PLAYER_URL="http://1kino.com/player.swf"; // путь кплэеру
$MODULE_URL="?mod=videostore"; // базовый путь к этому модулю 
$MODULE_TITLE="<a href='$MODULE_URL' style='font-size:14px; font-weight:bold;'>VIDEO Store</a>"; 
$DISPLAY_SKIN=true; // Вывод шаблона или просто данных (для ajax)
$DISPLAY_TABLE=true;// Вывод таблицы записей 
$ERROR=false; 
$MESSAGE=false;
$IMAGE_DIR=ROOT_DIR."/play/images"; // Путь для картинок 
// размеры картинок 
$IMAGE_WIDTH = 570;
$IMAGE_HEIGHT = 430;
// Парамерты редактора 
$EDIT_LINK="add";
$EDITOR_BUTTON="Добавить";
$EDITOR_TITLE="Загрузчик";


// Сохранение данных о текущей странице 
if(isset($_GET['page']) and is_numeric($_GET['page'])) {
	$MODULE_URL .= "&page=".$_GET['page'];
}

function safequery($str) {
	$patterns = array();
	$patterns[0] = "/'/";
	$patterns[1] = '/"/';

	$replacements = array();
	$replacements[0] = '&#39;';
	$replacements[1] = '&quot;';

	$str=htmlspecialchars($str);
	$str=preg_replace($patterns, $replacements, $string);
	return $str;	
}

// функция для преобразования размеров картинки 
function resize_img($file_src, $file_dest, $new_width, $new_height){
	
	$info_img = getimagesize($file_src);
	list($orig_width,$orig_height) = $info_img;
	$type_img = $info_img['mime'];

	switch ($type_img){
		case 'image/jpeg':
			$image_create_func = 'imagecreatefromjpeg';
            $image_save_func = 'imagejpeg';
            $new_image_ext = 'jpg';
            break;
		case 'image/png':
			$image_create_func = 'imagecreatefrompng';
			$image_save_func = 'imagejpeg';
			$new_image_ext = 'jpg';
			break;
		case 'image/gif':
			$image_create_func = 'imagecreatefromgif';
			$image_save_func = 'imagejpeg';
			$new_image_ext = 'jpg';
			break;
		case 'image/bmp':
			$image_create_func = 'imagecreatefrombmp';
			$image_save_func = 'imagejpeg';
			$new_image_ext = 'jpg';
			break;
		default:
			$image_create_func = 'imagecreatefromjpeg';
            $image_save_func = 'imagejpeg';
            $new_image_ext = 'jpg';
	}

	$new_image = imagecreatetruecolor($new_width, $new_height);
	$img_tmp = $image_create_func($file_src);
	imagecopyresampled($new_image,$img_tmp,0,0,0,0,$new_width,$new_height,$orig_width,$orig_height);

	$image_save_func($new_image, $file_dest, 100);
	imagedestroy($new_image);
}


// Функции доступные только администраторам 
if($member_id['user_group']==1) {
switch($_GET['action']) {

	case "edit" : {
		// Сохранение данных
		if($_SERVER['REQUEST_METHOD'] === "POST") {
			
			if (empty($_POST['video_url']) || empty($_POST['video_name']))
				$ERROR='<font style="color:red;">Название или адрес ролика отсутствует!</font><br/>';
		  if(!$ERROR) {
			
			 $VIDEO_URL=$db->safesql($_POST['video_url']);
			 $VIDEO_NAME=$db->safesql($_POST['video_name']);
			 $vid=(int) $_GET['id'];
			 $db->query("update flv_player set address='$VIDEO_URL',name_flv='$VIDEO_NAME' where id='$vid'");
			
			// Обработка картинок 
			for ($i=1; $i<=3; $i++){
				$fileimg_temp=$IMAGE_DIR."/".$vid."_".$i."temp.jpg";
			  if($_POST['image_up_type']==="link") {
				if($_POST['url_file_'.$i]!=="") {
				$img_date=file_get_contents($_POST['url_file_'.$i]);
				file_put_contents($fileimg_temp,$img_date);
				}
				else $fileimg_temp="";
			  }
			  else {
				if(isset($_FILES['file_'.$i]) and $_FILES['file_'.$i]['name']!=="")
				move_uploaded_file($_FILES['file_'.$i]['tmp_name'], $fileimg_temp);
				else $fileimg_temp="";
			  }
			  if($fileimg_temp!=="") {
				  $file_dest = $IMAGE_DIR."/".$vid."_".$i.".jpg";
				  resize_img($fileimg_temp, $file_dest, $IMAGE_WIDTH, $IMAGE_HEIGHT);
				  unlink($fileimg_temp);
			  }
			}
			
			$MESSAGE="Video отредактировано <br/> <a href='$MODULE_URL'> Переход к списку </a>";
		  }

		} else {
			// Вывод редактора тут настраиваются данные к нему 
			$vid=(int) $_GET['id'];
			$DISPLAY_TABLE=false; // Отключаем таблицы выводиться только редактор 
			$q="select * from flv_player where id='$vid'";
			
			$vid_ed=$db->super_query($q);
			
			$VIDEO_EDIT_NAME=$vid_ed['name_flv'];
			$VIDEO_EDIT_URL=$vid_ed['address'];
			$EDIT_LINK="edit&id=".$vid;
			$EDITOR_TITLE="Редактирование записи $vid ";
			$EDITOR_BUTTON="Редактировать";
		}
	
	} break;
	
	case "add" : { // добавление записи 

		if (($_SERVER['REQUEST_METHOD'] === "POST") && isset($_POST['insert'])){

			$VIDEO_EDIT_URL=$db->safesql($_POST['video_url']);
			$VIDEO_EDIT_NAME=$db->safesql($_POST['video_name']);
			
			 if (empty($VIDEO_EDIT_URL) || empty($VIDEO_EDIT_NAME))
				$ERROR='<font style="color:red;">Название или адрес ролика отсутствует!</font><br/>';
			 if($_POST['image_up_type']==="link") {
				 if (empty($_POST['url_file_1']) && empty($_POST['url_file_2']) && empty($_POST['url_file_3']))
					$ERROR ='<font style="color:red;">Добавьте хоть одну фотографию!</font><br/>';
			 } else {
				if (empty($_FILES['file_1']['name']) && empty($_FILES['file_2']['name']) && empty($_FILES['file_3']['name'])) $ERROR ='<font style="color:red;">Добавьте хоть одну фотографию!</font><br/>';
			 }
	
		  if(!$ERROR) {
			 $db->query("INSERT INTO flv_player (id,address,name_flv) VALUES('','$VIDEO_EDIT_URL','$VIDEO_EDIT_NAME')");
			 $vid = $db->insert_id();
			// Обработ5ка картинок 
			for ($i=1; $i<=3; $i++){
				$fileimg_temp=$IMAGE_DIR."/".$vid."_".$i."temp.jpg";
			  if($_POST['image_up_type']==="link") {
				if($_POST['url_file_'.$i]!=="") {
					$img_date=file_get_contents($_POST['url_file_'.$i]);
					file_put_contents($fileimg_temp,$img_date);
				}
				else $fileimg_temp="";
			  }
			  else {
				if(isset($_FILES['file_'.$i]) and $_FILES['file_'.$i]['name']!=="")
				move_uploaded_file($_FILES['file_'.$i]['tmp_name'], $fileimg_temp);
				else $fileimg_temp="";
			  }
			  if($fileimg_temp!=="") {
				  $file_dest = $IMAGE_DIR."/".$vid."_".$i.".jpg";
				  resize_img($fileimg_temp, $file_dest, $IMAGE_WIDTH, $IMAGE_HEIGHT);
				  unlink($fileimg_temp);
			  }
			}
			$MESSAGE="Video добавлено <br/> <a href='$MODULE_URL'> Переход к списку </a>";
		  } else $DISPLAY_TABLE=false;
  	  }
	} break;
		
	case "delete" : { // Удаление записи 
		$vid=(int) $_GET['id'];
		if(isset($_GET['confirm'])) {
		$q="select * from flv_player where id='$vid'";
		$rez_del=$db->query($q);
		if($db->num_rows($rez_del)>0){
			for($i=1;$i<=3;$i++){
				$img_file=$file_dest = $IMAGE_DIR."/".$id."_".$second_name.".jpg";
				if(is_file($img_file))  unlink($img_file);
			}
			$q="delete from flv_player where id='$vid' ";
			$db->query($q);
			$MESSAGE="Удалена запись с ID $vid ";
		} else $ERROR="Не существует такой записи";
		} else $MESSAGE="Вы действительно хотите удалить запись ID $vid ? <a href='$MODULE_URL&action=delete&id=$vid&confirm=yes'>Да</a>";
		
	} break;
	
	case "meta" : { // Запуск конвертера для добавления мета данных к flv файлам 
			$DISPLAY_SKIN=false;
			$vid=(int) $_GET['id'];
			$q="select * from flv_player where id='$vid'";
			$video_row=$db->super_query($q);
			$address=$video_row['address'];
			$url="http://privetpisea.com/convert.php?addres=".$address;
			$MODULE_CONTENT = "<span style='font-size:10px'>Converter start: ".$url."</span><br/>";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$curlstr=curl_exec($ch);
			$MODULE_CONTENT .= $curlstr;
			curl_close($ch);
	} break;
}
} // Конец операции для администратора 


// Общие операции администратор и радактор 
switch($_GET['action']) {
	case "preview" : {
		$id=(int) $_GET['id'];
		$q="select * from flv_player where id='$id'";

		$row=$db->super_query($q);
		$DISPLAY_TABLE=false; //не выводиться таблица 
		$MODULE_TITLE .= ".  <span class='caption'>". $row['name_flv']."</span>";

		$MODULE_CONTENT .= '<object codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" id="player" name="player" width="620" height="480"><param name="movie" value="'.$PLAYER_URL.'"><param name="quality" value="high"><param name="allowFullScreen" value="true"><param name="allowscriptaccess" value="always"><param name="flashVars" value="id='.$row['id'].'"><param name="wmode" value="Opaque"><embed flashvars="id='.$row['id'].'" allowscriptaccess="always" allowfullscreen="true" src="'.$PLAYER_URL.'" quality="high" wmode="opaque" id="player" name="player" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="620" height="480"><br><a target="_blank" title="Секс форум" href="http://1kino.com/go.php?b1f66a20f39e613680b96f9279ee7e93"><img alt="Секс форум" src="http://1kino.com/g.jpg" border="0" /></a></object>';
		
	} break;
	
	case "description" : {
		$vid=(int) $_POST['video_id'];
		$q="update flv_player set descr='".(($_POST['description']))."' where id='$vid'";
		$db->query($q);
	} break;
}



if($DISPLAY_SKIN) {

// Вывод списка записей 
if($DISPLAY_TABLE) {
	if(isset($_GET['search'])) {
		$word=$db->safesql($_GET['search']);
		$where=" where name_flv LIKE '%$word%' or address LIKE '%$word%'";
	}

	if(!isset($_GET['page'])) $page=0;

	$limit="limit ".($VIDEOS_PER_PAGE*$page).",".$VIDEOS_PER_PAGE."";

	$q="SELECT count(*) as num FROM flv_player $where ";
	$row_count=$db->super_query($q);

	$pages=ceil($row_count['num']/$VIDEOS_PER_PAGE);

	$q="SELECT * FROM flv_player $where ORDER BY id DESC $limit ";
	$rez_vid=$db->query( $q );

	if(isset($_GET['search']))
		$MODULE_URL_PAGE=$MODULE_URL."&search=".$_GET['search'];
		else 
		$MODULE_URL_PAGE=$MODULE_URL;

	for($i=0; $i<$pages;$i++){
		if($i<4 or ($i>$page-3 and $i<$page+3) or $i>$pages-4) {
			if($page==$i)
			$MODULE_PAGE.="<span >".($i+1)."</span>";
			else 
			$MODULE_PAGE.="<a href='".$MODULE_URL_PAGE."&page=".$i."' >".($i+1)."</a>";
		}
		
		if($i>=4 and $i<$page-2) { $MODULE_PAGE.="<span>...</span>"; $i=$page-3; }
		if($i>$page+2 and $i<$pages-4) { $MODULE_PAGE.="<span>...</span>"; $i=$pages-3; }
	}


	$MODULE_CONTENT .="<div class='pagination'> $MODULE_PAGE </div>";
	$MODULE_CONTENT .= "<table id='contentTab' > <tr class='caption'> <td> Иден </td> <td> Код вставки </td> <td> Кнопки </td> <td style='width:255px;'> Описание </td>  </tr>";



	while ( $row = $db->get_row($rez_vid) ) {

		$count_let=strlen(preg_replace("#[^a-zа-яА-Я\.]+#is","",$row['descr']));
					
		$row['flash_code'] = htmlspecialchars('<object codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" id="player" name="player" width="620" height="480"><param name="movie" value="'.$PLAYER_URL.'"><param name="quality" value="high"><param name="allowFullScreen" value="true"><param name="allowscriptaccess" value="always"><param name="flashVars" value="id='.$row['id'].'"><param name="wmode" value="Opaque"><embed flashvars="id='.$row['id'].'" allowscriptaccess="always" allowfullscreen="true" src="'.$PLAYER_URL.'" quality="high" wmode="opaque" id="player" name="player" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="620" height="480"></object>');
		
		$MODULE_CONTENT .= "<tr> <td align='center'> {$row['id']} </td> <td> <h3>{$row['name_flv']}</h3>
		<input type='text' value='{$row['flash_code']}' size='70' onclick='select()' /> </td> 
		<td class='edit_video_buttons'> <a href='".$MODULE_URL."&action=preview&id=".$row['id']."'> Просмотр  </a> ";
		
		if($member_id['user_group']==1) {
		$MODULE_CONTENT .= "<a href='javascript:void(0)' onclick='dometa(".$row['id'].")' id='meta_".$row['id']."'> Мета </a>
	<a href='".$MODULE_URL."&action=edit&id=".$row['id']."'> Редактировать  </a>
	<a href='".$MODULE_URL."&action=delete&id=".$row['id']."'> удалить  </a>	";
	}

	$MODULE_CONTENT .= "</td> <td style='padding-bottom:3px; padding-top:3px; '> 
	<form method='POST'  name='descr_edit_".$row['id']."' action='$MODULE_URL&action=description#meta_".$row['id']."'>
	<textarea name='description' cols='38' rows='4' onchange='count_letter(".$row['id'].")' onkeypress='count_letter(".$row['id'].")' style='width:275px; height:70px;'>".$row['descr']."</textarea>
	<div class='descrBut'>
	<input type='hidden' name='video_id' value='".$row['id']."' /> 
	<input type='text' name='count_let'  value='$count_let' size='10'  />
	<input type='button'  onclick='saveDescription(".$row['id'].");'  style='width:160px;' value='OK' />
	</div>
	</form> </td>
	</tr>";

	}

	$MODULE_CONTENT .= "</table>";
	$MODULE_CONTENT .="<div class='pagination'> $MODULE_PAGE </div>";
} // конец Вывод таблицы записей 

echoheader( "options", $lang['opt_head'] );

echo "<style>
.edit_video_buttons{padding:5px;}
.edit_video_buttons a{display:block;padding:5px;}
.caption {font-size:16px; }
.caption td{padding:5px;border-top:solid 1px #717171;}
.pagination {font-size:14px; margin:8px; }
.pagination a{border:1px solid #CCCCCC; padding:5px; margin:2px; }
.pagination span{border:1px solid #DDDDDD; padding:5px; margin:2px; }
.descrBut input{font-size:16px;}
label {width:200px; display:block; }
#contentTab td{border-bottom:solid 1px #717171; min-width:50px;}
.messageBox{text-align:center; padding:20px; font-size:16px; color:#82A7BE }
.errorBox{color:#FF0000;text-align:center; padding:20px; font-size:16px; }
.metaMes{border:1px solid;}
</style>
<script type=\"text/javascript\" src=\"engine/ajax/jquery.js\"></script>
<script>
function saveDescription(des_id) {
	fm=document.forms['descr_edit_'+des_id];
	var reg=/[^a-zа-я]/ig;
	var temp_str=fm.description.value.replace(reg, \"\");
	if(temp_str.length<300) alert('Слишком короткое описание');
	else fm.submit();
}

function count_letter(des_id) {
	fm=document.forms['descr_edit_'+des_id];
	var reg=/[^a-zа-я]/ig;
	var temp_str=fm.description.value.replace(reg, \"\");
	fm.count_let.value=temp_str.length;
} 

function dometa(vid){
	document.getElementById('meta_'+vid).innerHTML='<img src=\"engine/skins/images/spinner.gif\" />';
	$.ajax({
	url:'$MODULE_URL&action=meta&id='+vid,
	type:'get',
	success:function(data){
		// Действия при получений ответа 
		document.getElementById('meta_'+vid).innerHTML='<div class=\"metaMes\">'+data+'</div>';
	}
	}); 
}

function switch_panel(pan){
	if(pan==1) {
		document.getElementById('upload_images').style.display='none';
		document.getElementById('link_images').style.display='block';
	} else {
		document.getElementById('upload_images').style.display='block';
		document.getElementById('link_images').style.display='none';
	}
}
</script>";


if($member_id['user_group']==1 and $_GET['action']!=="preview") {
$EDITOR_FORM=<<<HTML
<table width='90%' style='margin: 5px 5% 20px 5%;'> <tr> <td> 
<form action="$MODULE_URL&action=$EDIT_LINK" method="post" enctype="multipart/form-data" name="video_editor">
<h3 style="color: #333;">$EDITOR_TITLE</h3>
<table> <tr> <td>
<label>Название ролика:</label>
<input type="text" name="video_name" maxlength="100" value='$VIDEO_EDIT_NAME' /> 
<label><b>Адрес ролика:</b></label>
<input type="text" name="video_url" maxlength="150" value='$VIDEO_EDIT_URL' /> <br/>
<input type="radio" name="image_up_type" value="link" onclick="switch_panel(1);" style="width: 20px;"/> Ссылка
<input type="radio" name="image_up_type" value="photo" checked="checked"  onclick="switch_panel(2);" style="width: 20px;"/> Картинка
</td> <td>


<div id='link_images' style='display:none;'>
	<label>Адрес фото 1:</label>
	<input type="text" name="url_file_1"/>

	<label>Адрес фото 2:</label>
	<input type="text" name="url_file_2"/>

	<label>Адрес фото 3:</label>
	<input type="text" name="url_file_3"/>
</div>
<div id='upload_images'>
	<label>Фото 1:</label>
	<input type="file" name="file_1"/>


	<label>Фото 2:</label>
	<input type="file" name="file_2"/>

	<label>Фото 3:</label>
	<input type="file" name="file_3"/>
</div>
</td> </tr>
<tr> <td colspan='2' align='center'>  <input class="butt" type="submit" name="insert" value="$EDITOR_BUTTON" /> </td> </tr>
</table>
</form>
</td> <td align='right'>
<form  > <input type='text' name='search' value='' /> 
<input type='hidden' name='mod' value='videostore' /><input type='submit' value='Поиск' /></form>
</td> </tr> </table>
HTML;
}

if($MESSAGE)  { $EDITOR_FORM=""; 
$MESSAGE="<div class='messageBox'> $MESSAGE </div>";
}

if($ERROR)  { // $EDITOR_FORM=""; 
$ERROR="<div class='errorBox'> $ERROR </div>";
}

echo <<<HTML

<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tr>
        <td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;">
		<div class="navigation">$MODULE_TITLE</div></td>
    </tr>
</table>
<div class="unterline"></div>

$ERROR

$MESSAGE

$EDITOR_FORM

$MODULE_CONTENT

</td>
        <td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
</div>
HTML;
echofooter();

} else echo $MODULE_CONTENT;
?>