<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of users (Admin only)
     */
    public function index(Request $request)
    {
        $authUser = $request->user();

        if ($authUser->role !== 'admin') {
            return response()->json([
                'message' => 'ليس لديك صلاحية لرؤية المستخدمين'
            ], 403);
        }

        $users = User::all();
        return UserResource::collection($users);
    }

    /**
     * Store a newly created user in storage
     */
    public function store(Request $request)
    {
        $authUser = $request->user();

        // الطلاب لا يستطيعون إنشاء مستخدمين آخرين
        if ($authUser && $authUser->role !== 'admin') {
            return response()->json([
                'message' => 'ليس لديك صلاحية لإنشاء مستخدمين آخرين'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => ['nullable', Rule::in(['admin', 'student', 'instructor'])],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'student',
        ]);

        return new UserResource($user);
    }

    /**
     * Display the specified user
     */
    public function show(Request $request, string $id)
    {
        $authUser = $request->user();
        $user = User::findOrFail($id);

        if ($authUser->role !== 'admin' && $authUser->id != $user->id) {
            return response()->json(['message' => 'ليس لديك صلاحية لرؤية هذا المستخدم'], 403);
        }

        return new UserResource($user);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, string $id)
    {
        $authUser = $request->user();
        $user = User::findOrFail($id);

        if ($authUser->role !== 'admin' && $authUser->id != $user->id) {
            return response()->json(['message' => 'ليس لديك صلاحية لتعديل هذا المستخدم'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes','email', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:6',
            'role' => ['nullable', Rule::in(['admin', 'student', 'instructor'])],
        ]);

        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->has('password')) $user->password = Hash::make($request->password);
        if ($request->has('role') && $authUser->role === 'admin') $user->role = $request->role;

        $user->save();

        return new UserResource($user);
    }

    /**
     * Remove the specified user
     */
    public function destroy(Request $request, string $id)
    {
        $authUser = $request->user();
        $user = User::findOrFail($id);

        if ($authUser->role !== 'admin') {
            return response()->json(['message' => 'ليس لديك صلاحية لحذف هذا المستخدم'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'تم حذف المستخدم بنجاح'], 200);
    }
    /**
 * Display a listing of instructors
 */
public function instructors(Request $request)
{
    $authUser = $request->user();

    // السماح فقط للمدير أو المدرسين الآخرين حسب الحاجة
    if ($authUser->role !== 'admin') {
        return response()->json([
            'message' => 'ليس لديك صلاحية لرؤية المدرسين'
        ], 403);
    }

    // جلب جميع المستخدمين الذين لديهم دور instructor
    $instructors = User::where('role', 'instructor')->get();

    return UserResource::collection($instructors);
}

}
