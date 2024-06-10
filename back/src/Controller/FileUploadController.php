<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FileUploadController extends AbstractController
{
    #[Route('/file/upload', name: 'app_file_upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        // Obtiene el archivo enviado en la solicitud
        $file = $request->files->get('file');

        // Verifica si se envió un archivo
        if ($file) {
            // Define el directorio de destino donde se guardará el archivo
            $uploadDirectory = $this->getParameter('upload_directory');

            // Obtiene el nombre original del archivo
            $originalFilename = $file->getClientOriginalName();
            // Genera un nombre único para el archivo
            $filename = $originalFilename;

            // Mueve el archivo al directorio de destino
            $file->move($uploadDirectory, $filename);

            // Retorna una respuesta JSON con el nombre del archivo guardado
            return $this->json(['message' => 'File uploaded successfully', 'filename' => $filename]);
        }

        // Si no se envió ningún archivo, retorna un error
        return $this->json(['message' => 'No file uploaded'], JsonResponse::HTTP_BAD_REQUEST);
    }
}
