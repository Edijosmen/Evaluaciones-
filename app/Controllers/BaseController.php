<?php

class BaseController {
    protected function getBaseUrl() {
        $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        return $baseUrl === '/' ? '' : $baseUrl;
    }

    protected function redirect(string $path) {
        $path = trim($path, '/');
        $baseUrl = $this->getBaseUrl();

        if ($path === '') {
            $location = $baseUrl === '' ? '/' : $baseUrl . '/';
        } else {
            $location = $baseUrl === '' ? '/' . $path : $baseUrl . '/' . $path;
        }

        header('Location: ' . $location);
        exit;
    }
}
