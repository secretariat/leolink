<?php 
	/*
	* автор: Клюев ЕНОТ Яков
	* настройки отображения и кеш обновляються раз в 12 часа
	*/

	//$start_time = round(microtime(true) * 1000);//время запуска скрипта в миллисекундах
	/*------------------------------------------*/
	$leolink_url = md5($_SERVER["REQUEST_URI"]);
	if(empty($leolink_cache_folder_name))
		$leolink_cache_folder_name = "leolink_cache";
	$leolink_cache_folder = dirname(__FILE__).'/'.$leolink_cache_folder_name.'/';
	if(!file_exists($leolink_cache_folder))
		@mkdir($leolink_cache_folder, 0777);
	if(empty($leolink_key))
        $leolink_key = FALSE;
	
	$leolink_data = array();
	if(file_exists($leolink_cache_folder.$leolink_url))
		$cache_m_time = @filemtime($leolink_cache_folder.$leolink_url);//время последнего обновления кеш-файла
	else
		$cache_m_time = 0;
	/*	если кеш не обновлялся 12 часов или его нет пытаемся его обновить	*/
	if((time() - $cache_m_time) > 43200)
	{
		if($handle = @fopen("http://leolink.com.ua/api/get_links.php?key=$leolink_key&url=$leolink_url", 'r'))
		{
			$file_content = @fread($handle, 3072);
			@fclose($handle);
			if($file_content !== FALSE)
			{
				if($handle = @fopen($leolink_cache_folder.$leolink_url, 'w'))
				{
					@fwrite($handle, rtrim($file_content));
					@fclose($handle);
				}
			}
		}
	}
	/*	загрузка ссылок из кеш-файла если такой существует	*/
	if($handle = @fopen($leolink_cache_folder.$leolink_url, 'r'))
	{
		$file_content = @fread($handle, 3072);
		@fclose($handle);
		if($file_content !== FALSE)
		{
			$lines = @explode(PHP_EOL, $file_content);
			foreach ($lines as $line) {
				$tokens = @explode(';', $line);
				if($tokens != FALSE && count($tokens) == 2)
					array_push ($leolink_data, $tokens);
			}
		}
	}
	/*---------------------------------------------------------------------*/
	/*$work_time = round(microtime(true) * 1000) - $start_time;
	echo "Work time : $work_time";*/
	/*--------------------------------------------------------------------*/
?>