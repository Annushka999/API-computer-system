<?php

namespace App\Services;

use App\Http\Requests\File\UploadFileRequest;
use App\Models\File;
use App\Models\User;
use Dflydev\DotAccessData\Data;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    public function uploadFiles(UploadFileRequest $request): array
    {
        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            if ($file->isValid()) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();

                $i = 0;
                $newFileName = $originalName;

                while (Storage::disk('public')->exists($newFileName)) {
                    $i++;
                    $newFileName = pathinfo($originalName, PATHINFO_FILENAME) . " ($i).$extension";
                }

                $fileId = Str::random(10);

                $path = $file->storeAs('', $newFileName, 'public');
                $file = File::query()->create([
                    'user_id' => auth()->id(),
                    'file_id' => $fileId,
                    'name' => $newFileName,
                    'path' => $path
                ]);

                $file->usersWithAccess()->attach(Auth::id(), ['access_type' => 'author']);


                $uploadedFiles[] = [
                    'success' => true,
                    'message' => 'Success',
                    'name' => $newFileName,
                    'url' => url("/files/$fileId"),
                    'file_id' => $fileId
                ];
            } else {
                $uploadedFiles[] = [
                    'success' => false,
                    'message' => 'File not loaded',
                    'name' => $file->getClientOriginalName()
                ];
            }
        }

        return $uploadedFiles;
    }

    public function updateFileName(string $file_id, string $newName): bool|int
    {
        $file = $this->getFile($file_id);
        if (auth()->id() !== $file->user_id) {
            return false;
        }

        return $file->update(['name' => $newName]);
    }

    public function deleteFile(string $file_id): bool|null
    {
        $file = $this->getFile($file_id);

        Storage::disk('public')->delete($file->path);

        return $file->delete();
    }

    public function getFile(string $file_id): File
    {
        return File::query()->where('file_id', $file_id)->firstOrFail();
    }

    public function addAccessByFile(File $file, User $user): \Illuminate\Support\Collection
    {
        if ($file->usersWithAccess()->where('users.id', $user->id)->exists()) {
            return collect();
        }
        $file->usersWithAccess()->attach($user->id, ['access_type' => 'co-author']);

        return $file->usersWithAccess()->get();
    }

    public function deleteAccessByFile(File $file, User $user): Collection|\Illuminate\Support\Collection
    {
        if ($file->usersWithAccess()->where('users.id', $user->id)->doesntExist()) {
            return collect();
        }

        $file->usersWithAccess()->detach($user->id);

        return $file->usersWithAccess()->get();
    }

    public function get(): Collection|array
    {
        return File::query()->with('usersWithAccess')->where('user_id', Auth::id())->get();
    }

    public function shared(): Collection|array
    {
        return File::query()->whereHas('usersWithAccess', function ($query) {
            $query->where('user_id', Auth::id())
            ->whereNot('access_type', 'author');
        })->get();
    }
}
