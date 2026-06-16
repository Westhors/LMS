<?php


namespace App\Helpers;

use App\Mail\Event\EventCancelOneToOneMail;
use App\Mail\Event\EventCompanyStatusApprovedMail;
use App\Mail\Event\EventReceiverOneToOneMail;
use App\Mail\Event\EventResetPasswordMail;
use App\Mail\Event\EventSenderOneToOneMail;
use App\Mail\Event\InvoiceMail;
use App\Mail\Event\InvoiceMailAdmin;
use App\Models\AssistantTeacher;
use App\Models\AssistantTeacherPermission;
use App\Models\Delegate;
use App\Models\EmailTemplate;
use App\Models\Permission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class FileUploadAction
{

    function str_random($length = 4)
    {
        return Str::random($length);
    }

    function str_slug($title, $separator = '-', $language = 'en')
    {
        return Str::slug($title, $separator, $language);
    }

    public function execute($model, $files, $destination)
    {
        foreach ($files as $images) {

            $img = "";
            $img = $this->str_random(4) . $images->getClientOriginalName();
            $originname = time() . '.' . $images->getClientOriginalName();
            $filename = $this->str_slug(pathinfo($originname, PATHINFO_FILENAME), "-");
            $filename = $images->hashName();
            $extention = pathinfo($originname, PATHINFO_EXTENSION);
            $img = $filename;
            $type = $images->extension();
            $size = $images->getSize();
            $images->move(public_path('storage'), $img);
            $model->images()->create(['image' => $img, 'type' => $type, 'size' => $size]);
        }
    }

    public function executeBase64($model, $files, $destination = null)
    {
        foreach ($files as $file) {
            if (preg_match('/^data:image\/(\w+);base64,/', $file)) {
                $extension = explode('/', explode(':', substr($file, 0, strpos($file, ';')))[1])[1];
                $replace = substr($file, 0, strpos($file, ',') + 1);
                $image = str_replace($replace, '', $file);
                $image = str_replace(' ', '+', $image);
                $imageName = Str::random(10) . '.' . $extension;
                Storage::disk('public')->put($imageName, base64_decode($image));
                $model->images()->create(['image' => $imageName, 'type' => $extension]);
            }
        }
    }

    public function checkAssistantPermission($module, $action)
    {
        $user = auth('sanctum')->user();

        if (!$user) return true;

        if (!($user instanceof \App\Models\AssistantTeacher)) {
            return true;
        }

        $permission = Permission::where('name', $module)->first();

        if (!$permission) {
            return response()->json([
                'message' => 'Permission not found'
            ], 404);
        }

        $pivot = $user->permissions()
            ->where('permissions.id', $permission->id)
            ->first();

        if (!$pivot) {
            return response()->json(['message' => 'ليس لديك صلاحية'], 403);
        }

        if (!$pivot->pivot->{$action}) {
            return response()->json(['message' => 'ليس لديك صلاحية'], 403);
        }

        return true;
    }
}
