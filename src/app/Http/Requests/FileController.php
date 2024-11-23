<?php

namespace App\Http\Requests;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AccessFilesRequest;
use App\Http\Resources\File\AccessUserFileResource;
use App\Http\Resources\File\SharedFileResource;
use App\Http\Resources\File\UserFileResource;
use App\Services\UserService;
use App\Http\Requests\File\{RenameFileRequest, UploadFileRequest};
use App\Services\FileService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    public function __construct(private readonly FileService $fileService, private readonly UserService $userService)
    {
    }

    public function getFiles(): JsonResponse
    {
        $files = $this->fileService->get();

        return response()->json(
            UserFileResource::collection($files)
        , 200, ['Content-type' => 'application/json']);
    }

    public function upload(UploadFileRequest $request): JsonResponse
    {
        $uploadedFiles = $this->fileService->uploadFiles($request);

        return response()->json($uploadedFiles, 200, ['Content-type' => 'application/json']);
    }

    public function update(RenameFileRequest $request, string $file_id): JsonResponse
    {
        try {
            $this->fileService->updateFileName($file_id, $request->name);

            return response()->json([
                'success' => true,
                'message' => 'Renamed'
            ], 200, ['Content-type' => 'application/json']);

        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Not found'
            ], 404, ['Content-type' => 'application/json']);
        }
    }

    public function download(string $file_id): BinaryFileResponse|JsonResponse
    {
        try {
            $file = $this->fileService->getFile($file_id);

            return response()->download(public_path("uploads/{$file->path}"), $file->name);
        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Not found'
            ], 404, ['Content-type' => 'application/json']);
        }
    }

    public function destroy(string $file_id): JsonResponse
    {
        try {
            $this->fileService->deleteFile($file_id);

            return response()->json([
                'success' => true,
                'message' => 'File already deleted'
            ], 200, ['Content-type' => 'application/json']);
        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Not found'
            ], 404, ['Content-type' => 'application/json']);
        }
    }

    public function addAccess(AccessFilesRequest $request, string $file_id): JsonResponse
    {
        try {
            $file = $this->fileService->getFile($file_id);
            $user = $this->userService->getUserByEmail($request->email);
            $usersWithAccessByFile = $this->fileService->addAccessByFile($file, $user);

            return response()->json(
                AccessUserFileResource::collection($usersWithAccessByFile),
                200, ['Content-type' => 'application/json']
            );
        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Not found'
            ], 404, ['Content-type' => 'application/json']);
        }
    }

    public function deleteAccess(AccessFilesRequest $request, string $file_id): JsonResponse
    {
        try {
            $file = $this->fileService->getFile($file_id);
            $user = $this->userService->getUserByEmail($request->email);
            $usersWithAccessByFile = $this->fileService->deleteAccessByFile($file, $user);

            return response()->json(
                AccessUserFileResource::collection($usersWithAccessByFile),
                200, ['Content-type' => 'application/json']
            );
        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Not found'
            ], 404, ['Content-type' => 'application/json']);
        }
    }

    public function getSharedFiles(): JsonResponse
    {
        $files = $this->fileService->shared();

        return response()->json(
            SharedFileResource::collection($files)
        , 200, ['Content-type' => 'application/json']);
    }

}
