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
			echo "����� ��� ����������: $dir. ���������<br>";
			DirectoryUtils::delDir($dir);
		} else {
			echo "����� �� ����������: $dir. ��������<br>";
		}
		DirectoryUtils::createDir($dir, $perms);
	}

	/** Taken from user notes in PHP-doc (rmdir() function)*/
	function delDir($dir) {
		if (NULL == $dir || !is_dir($dir)) {
			echo "������: \"$dir\" �� �������� ������<br>";
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
			echo("������� �����: $dir<br>");
			return TRUE;
		} else {
			echo("�������� ����� �� �������: $dir<br>");
			return FALSE;
		}
	}
}
?>