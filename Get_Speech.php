<?

function GetSpeech($text,$which){//вытаскивание речи из фрагмента речи
	//$which - какой член массива нам нужен
	$arr = explode('"',$text);
	$need = '';
	$arrZnak = array('.','!','?');
	
	if(count($arr>2)){
		if($which == 'left'){
			//проверяем наличие окончаний предложения
			$zamen = str_replace($arrZnak,'ĀQÐ', $arr[count($arr)-1]);//2
			if($zamen == $arr[count($arr)-1]){//если между ковычками и словом не было таких знаков
				$need = $arr[count($arr)-2];
			}
		}elseif($which == 'right'){
			//проверяем наличие окончаний предложения
			$zamen = str_replace($arrZnak,'ĀQÐ', $arr['0']);//2
			if($zamen == $arr['0']){//если между ковычками и словом не было таких знаков
				$need = $arr['1'];
			}
		}
	}
 return $need;
}
?>