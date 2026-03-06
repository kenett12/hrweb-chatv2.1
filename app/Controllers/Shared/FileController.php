<?php

namespace App\Controllers\Shared;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class FileController extends BaseController
{
    /**
     * serveTicketAttachment
     * Securely serves ticket attachments from the writable directory.
     * This prevents direct public access to sensitive files.
     */
    public function serveTicketAttachment($filename)
    {
        $path = WRITEPATH . 'uploads/tickets/' . $filename;

        if (!file_exists($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody(file_get_contents($path));
    }
}
