<?php

require_once "vendor/autoload.php";

if (!isset($argv)) {
    throw new \LogicException("This script can be started only from CLI!");
}

\Amp\Loop::run(function () use (&$argv) {

    $isElevateProcess = !empty($argv[1]) ? true : false;

    $taskkillProc = \Nicodinus\Amp\Extensions\Process\WindowsProcess::createProcess("taskkill", ["/F", "/IM", "ZOSI VIEW.exe"], $isElevateProcess);
    $zosiviewProc = \Nicodinus\Amp\Extensions\Process\WindowsProcess::createProcess("C:\Program Files (x86)\ZOSI VIEW\ZOSI VIEW.exe", [], $isElevateProcess);

    yield $taskkillProc->start();
    yield $taskkillProc->join();

    yield $zosiviewProc->start();
    yield $zosiviewProc->join();

});