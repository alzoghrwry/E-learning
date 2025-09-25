<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageReadEvent;
use App\Events\NewMessageEvent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{
    /**
     * الحصول على جميع محادثات المستخدم
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $conversations = Conversation::with([
                'student:id,name,profile_photo,role',
                'admin:id,name,profile_photo,role',
                'lastMessage:sender_id,message,created_at,conversation_id'
            ])
            ->where(function ($query) use ($user) {
                if ($user->role === 'student') {
                    $query->where('student_id', $user->id);
                } else {
                    $query->where('admin_id', $user->id);
                }
            })
            ->withCount(['messages as unread_count' => function ($query) use ($user) {
                $query->where('sender_id', '!=', $user->id)
                      ->where('is_read', false);
            }])
            ->orderBy('last_message_at', 'desc')
            ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $conversations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في تحميل المحادثات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * بدء محادثة جديدة
     */
    public function startConversation(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'admin_id' => 'required|exists:users,id',
                'initial_message' => 'sometimes|string|max:1000'
            ]);

            $student = $request->user();

            if ($student->role !== 'student') {
                throw ValidationException::withMessages([
                    'role' => ['يجب أن تكون طالباً لبدء محادثة جديدة']
                ]);
            }

            $admin = User::where('id', $request->admin_id)
                ->whereIn('role', ['admin', 'instructor'])
                ->where('is_active', true)
                ->firstOrFail();

            // البحث عن محادثة موجودة أو إنشاء جديدة
            $conversation = Conversation::firstOrCreate([
                'student_id' => $student->id,
                'admin_id' => $admin->id
            ], [
                'title' => "محادثة بين {$student->name} و {$admin->name}",
                'last_message_at' => now()
            ]);

            // إذا كانت هناك رسالة ابتدائية
            if ($request->has('initial_message') && !empty($request->initial_message)) {
                $message = $conversation->messages()->create([
                    'sender_id' => $student->id,
                    'message' => $request->initial_message
                ]);

                $conversation->updateLastMessageTime();
                
                // بث الرسالة
                broadcast(new NewMessageEvent($message));
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $conversation->load(['student', 'admin', 'lastMessage'])
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'فشل في بدء المحادثة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إرسال رسالة
     */
    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'message' => 'required|string|max:5000'
            ]);

            $user = $request->user();

            // التحقق من صلاحية المستخدم للمحادثة
            if (!in_array($user->id, [$conversation->student_id, $conversation->admin_id])) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بالمشاركة في هذه المحادثة'
                ], 403);
            }

            // فتح المحادثة إذا كانت مغلقة
            if ($conversation->is_closed) {
                $conversation->markAsOpen();
            }

            // إنشاء الرسالة
            $message = $conversation->messages()->create([
                'sender_id' => $user->id,
                'message' => $request->message
            ]);

            // تحديث وقت آخر رسالة
            $conversation->updateLastMessageTime();

            // بث الرسالة
            broadcast(new NewMessageEvent($message));

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $message->load('sender')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'فشل في إرسال الرسالة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على رسائل المحادثة
     */
    public function getMessages(Conversation $conversation, Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // التحقق من الصلاحية
            if (!in_array($user->id, [$conversation->student_id, $conversation->admin_id])) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بمشاهدة هذه المحادثة'
                ], 403);
            }

            $messages = $conversation->messages()
                ->with('sender:id,name,profile_photo,role')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // وضع علامة مقروء على الرسائل
            if ($user->id !== $conversation->getOtherUserAttribute()->id) {
                $conversation->messages()
                    ->where('sender_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

                // بث حدث القراءة
                broadcast(new MessageReadEvent($conversation, $user->id));
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'conversation' => $conversation->load(['student', 'admin']),
                    'messages' => $messages
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في تحميل الرسائل',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على المديرين المتاحين
     */
    public function getAvailableAdmins(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $admins = User::whereIn('role', ['admin', 'instructor'])
                ->where('is_active', true)
                ->where('id', '!=', $user->id)
                ->select('id', 'name', 'email', 'role', 'profile_photo', 'specialization')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $admins
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في تحميل قائمة المديرين',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث حالة القراءة
     */
    public function markAsRead(Conversation $conversation, Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $conversation->messages()
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            // بث حدث القراءة
            broadcast(new MessageReadEvent($conversation, $user->id));

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة القراءة'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في تحديث حالة القراءة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إغلاق المحادثة
     */
    public function closeConversation(Conversation $conversation, Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!in_array($user->id, [$conversation->student_id, $conversation->admin_id])) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بإغلاق هذه المحادثة'
                ], 403);
            }

            $conversation->markAsClosed();

            return response()->json([
                'success' => true,
                'message' => 'تم إغلاق المحادثة بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في إغلاق المحادثة',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}