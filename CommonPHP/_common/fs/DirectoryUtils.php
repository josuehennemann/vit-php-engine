<?
/** Helper class holding various fs directory utilities. */
class DirectoryUtils {
	/** @return directory contents array (together with subdirs contents). */
	function listDir($dir) {
		$toRet = array();

		$current_dir = opendir($dir);
		while($entryname = readdir($current_dir)) {
			if ( is_dir("$dir/$entryname") && ($entryname != "." and $entryname!="..") ) {
       			$toRet = array_merge($toRet, DirectoryUtils::listDir("$dir/$entryname"));
    		} elseif ( $entryname != "." && $entryname!=".." ) {
				array_push($toRet, "$dir/$entryname");
			}
		}
		closedir($current_dir);
		return $toRet;
	}

	function recreateDir($dir, $perms) {
		if (file_exists($dir)) {
			echo "Папка уже существует: $dir. Удаляется<br>";
			DirectoryUtils::delDir($dir);
		} else {
			echo "Папка не существует: $dir. Создаётся<br>";
		}
		DirectoryUtils::createDir($dir, $perms);
	}

	/** Taken from user notes in PHP-doc (rmdir() function)*/
	function delDir($dir) {
		if (NULL == $dir || !is_dir($dir)) {
			echo "Ошибка: \"$dir\" не является папкой<br>";
			return FALSE;
		}

		$current_dir = opendir($dir);
		while($entryname = readdir($current_dir)) {
			if ( is_dir("$dir/$entryname") && ($entryname != "." and $entryname!="..") ) {
       			DirectoryUtils::delDir("$dir/$entryname");
    		} elseif ( $entryname != "." && $entryname!=".." ) {
				unlink("$dir/$entryname");
			}
		}
		closedir($current_dir);
		rmdir($dir);
	}

	function createDir($dir, $perms) {
		if ( mkdir($dir, $perms) ) {
			echo("Создана папка: $dir<br>");
			return TRUE;
		} else {
			echo("Создание папки не удалось: $dir<br>");
			return FALSE;
		}
	}
}
?>