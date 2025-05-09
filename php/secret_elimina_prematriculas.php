<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

if ($mysqli->errno>0) exit("server");

$mysqli->set_charset("utf8");

$mysqli->query("DELETE FROM premat_bach");
if ($mysqli->errno > 0) exit("error_premat_bach");

$mysqli->query("DELETE FROM premat_eso");
if ($mysqli->errno > 0) exit("error_premat_eso");



$rootPath = "../docs";

function deleteDirectoryContents($dir) {
    if (!is_dir($dir)) return;
    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            deleteDirectoryContents($path);
            rmdir($path);
        } else {
            unlink($path);
        }
    }
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::CHILD_FIRST
);

foreach ($iterator as $fileInfo) {
    if ($fileInfo->isDir() && $fileInfo->getFilename() === "prematriculas") {
        deleteDirectoryContents($fileInfo->getPathname());
    }
}