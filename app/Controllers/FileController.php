<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class FileController extends BaseController
{

    public function show($path, $filename)
    {
        $path = WRITEPATH . 'uploads/' . $path . '/' . $filename;

        if (!file_exists($path)) {
            return null;
        }

        return $this->response->setHeader('Content-Type', mime_content_type($path))
            ->setBody(file_get_contents($path));
    }

    public function showSub($path1, $filename)
    {
        $path = WRITEPATH . 'uploads/' . $path1 . '/sub/' . $filename;

        if (!file_exists($path)) {
            return null;
        }

        return $this->response->setHeader('Content-Type', mime_content_type($path))
            ->setBody(file_get_contents($path));
    }
}
