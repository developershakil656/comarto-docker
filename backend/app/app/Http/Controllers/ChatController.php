<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConversationRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Vinkla\Hashids\Facades\Hashids;

class ChatController extends Controller
{
    // Start chat (User -> Business or Business -> User)
    public function startConversation(ConversationRequest $request)
    {
        if(auth()->user()->id == $request->user_id && auth()->user()?->business?->id == $request->business_id)
            return error_response('invalid data');

        $conversation = Conversation::firstOrCreate([
            'user_id' => $request->user_id,
            'business_id' => $request->business_id,
        ]);
        return success_response('conversation created successfully', new ConversationResource($conversation));
    }

    // List conversations
    public function conversations()
    {
        $auth = auth()->user();

        // Fetch all conversations (both as User and Business)
        $conversations = Conversation::with(['user', 'business', 'messages' => function ($q) {
            $q->latest(); // Order by latest message
        }])
            ->where(function ($query) use ($auth) {
                $query->where('user_id', $auth->id) // Conversations where the user is involved
                    ->orWhere('business_id', $auth->business ? $auth->business->id : null); // Conversations where the business is involved
            })
            ->get()
            ->sortByDesc(function ($conversation) {
                // Sort conversations by the latest message timestamp
                return $conversation->messages->first() ? $conversation->messages->first()->created_at : now();
            });

        return success_response('Conversations fetched successfully', ConversationResource::collection($conversations));
    }

    // Fetch messages
    public function messages($conversationId)
    {
        $conversationId = $conversationId ? Hashids::decode($conversationId) : [];
        $conversation = Conversation::findOrFail($conversationId[0]);
        $messages = $conversation->messages;

        $auth = auth()->user();
        $isUser = $auth->id === $conversation->user_id;
        // 🔹 Then mark them as read immediately
        $conversation->messages()
            ->where('is_read', false)
            ->where('sender_type', $isUser ? 'business' : 'user')
            ->update(['is_read' => true]);

        return success_response('messages fetched successfully', MessageResource::collection($messages));
    }

    // Send message
    public function sendMessage(Request $request, $conversationId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf,doc,docx,xlsx|max:5120', // 5MB limit
        ]);
        if ($validator->fails()) {
            return error_response('Validation error!', $validator->errors());
        }

        $conversationId = $conversationId ? Hashids::decode($conversationId) : [];
        $conversation = Conversation::findOrFail($conversationId[0]);
        $auth = auth()->user();
        $isUser = $auth->id === $conversation->user_id;

        // Check if auth user is part of this conversation
        // if ($conversation->user_id === $auth->id) {
        //     $senderType = 'user';
        //     $senderId   = $auth->id;
        // } elseif ($auth->business && $conversation->business_id === $auth->business->id) {
        //     $senderType = 'business';
        //     $senderId   = $auth->business->id;
        // } else {
        //     return error_response('You are not allowed in this conversation.');
        // }
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $isUser ? $auth->id : $auth->business->id,
            'sender_type'     => $isUser ? 'user' : 'business',
            'message'         => $request->message,
        ]);

        // 🔹 Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $file_url = image_link_generator(
                    $file,
                    '/messages/',
                    $conversation->id
                );

                // Store attachment in DB
                $message->attachments()->create([
                    'url' => $file_url,
                    // 'message_id' => $message->id,
                ]);
            }
        }

        return success_response('Message sent successfully', new MessageResource($message));
    }
}
