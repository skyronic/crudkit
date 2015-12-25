<?php

namespace CrudKit\Util;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class Installer {
	private static function recursiveCopy ($source, $dest) {
		if (!is_dir($dest)) {
			mkdir($dest, 0755, true);
		}
		foreach (
			$iterator = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
				\RecursiveIteratorIterator::SELF_FIRST) as $item
			) {
			if ($item->isDir()) {
				$target = $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
				if (!is_dir($target))
					mkdir($target);
			} else {
				echo "Copying:\nSource:". $item."\nDestination:".$dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName()."\n\n";
				copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
			}
		}
	}
	public static function copyFiles (Event $event) {
		$composer = $event->getComposer ();

		$vendorPath = $composer->getConfig()->get('vendor-dir');
		$root = dirname($vendorPath);
		$ds = DIRECTORY_SEPARATOR;

		$src = $vendorPath.$ds."skyronic".$ds."crudkit".$ds."src".$ds."static".$ds."build".$ds;
		$dest = $root.$ds."static".$ds."crudkit";

		echo "Copying crudkit static files... \n\n";
		self::recursiveCopy ($src, $dest);
		echo "Done!\n\n";
	}
}