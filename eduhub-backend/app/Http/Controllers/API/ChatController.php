<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;

class ChatController extends Controller
{
    public function AllUsers(Request $request)
    {
        $request->validate([
            'search' => 'required|string',
        ]);

        // Initialize the query builder for combining results
        $usersQuery = User::query()
            ->select('id', 'name', DB::raw('"user" as type'))
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            });

        $teachersQuery = Teacher::query()
            ->select('id', 'name', DB::raw('"teacher" as type'))
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            });

        $parentsQuery = ParentModel::query()
            ->select('id', 'name', DB::raw('"parentModel" as type'))
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            });

        $studentsQuery = Student::query()
            ->select('id', 'name', DB::raw('"student" as type'))
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            });

        // Combine all queries into a single query using `unionAll`
        $allUsersQuery = $usersQuery->unionAll($teachersQuery)
            ->unionAll($parentsQuery)
            ->unionAll($studentsQuery);

        // Execute the query and get the results
        $users = $allUsersQuery->get();

        $transformedUsers = $users->map(function ($user) {
            return [
                'label' => $user->name,
                'value' => $user->id,
                'type' => $user->type
            ];
        });
        // Return success response
        return $this->success($transformedUsers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:user,teacher,parentModel,student',
            'id' => 'required',
        ]);

        $sender = auth()->user();
        $receiver_type = $request->type;
        $receiver_id = $request->id;
        $receiver = getModelFromType($receiver_type)->findOrFail($receiver_id);

        // Normalize participant pairs
        [$type1, $id1] = [$sender->getMorphClass(), $sender->id];
        [$type2, $id2] = [$receiver->getMorphClass(), $receiver->id];

        // Always sort the pair lexicographically (to match in any order)
        if (strcmp($type1 . $id1, $type2 . $id2) > 0) {
            [$type1, $id1, $type2, $id2] = [$type2, $id2, $type1, $id1];
        }

        $chat = Chat::firstOrCreate([
            'sender_type' => $type1,
            'sender_id' => $id1,
            'receiver_type' => $type2,
            'receiver_id' => $id2,
        ]);

        return $this->success($chat);
    }

    public function read($message_id)
    {
        $msg = ChatMessage::findOrFail($message_id);
        $msg->update(['is_read' => true]);
        return $this->success(true);
    }

    public function readAll($chat_id)
    {
        $chat = Chat::findOrFail($chat_id);
        $chat->messages()->where('sender_id', '!=', auth()->id())->update(['is_read' => true]);
        return $this->success(true);
    }
}
