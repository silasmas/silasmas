<?php

namespace App\Http\Controllers\API;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Resources\Message as ResourcesMessage;

/**
 * @author Xanders
 * @see https://www.linkedin.com/in/xanders-samoth-b2770737/
 */
class MessageController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $messages = Message::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesMessage::collection($messages), 'Messages trouvés');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get inputs
        $inputs = [
            'message_subject' => $request->message_subject,
            'message_content' => $request->message_content,
            'status_id' => $request->status_id,
            'user_id' => $request->user_id
        ];

        $message = Message::create($inputs);

        return $this->handleResponse(new ResourcesMessage($message), 'Message envoyé');
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $message = Message::find($id);

        if (is_null($message)) {
            return $this->handleError('Message non trouvé');
        }

        return $this->handleResponse(new ResourcesMessage($message), 'Message trouvé');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'message_subject' => $request->message_subject,
            'message_content' => $request->message_content,
            'status_id' => $request->status_id,
            'user_id' => $request->user_id
        ];

        if ($inputs['message_subject'] != null) {
            $message->update([
                'message_subject' => $request->message_subject,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['message_content'] != null) {
            $message->update([
                'message_content' => $request->message_content,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['status_id'] != null) {
            $message->update([
                'status_id' => $request->status_id,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['user_id'] != null) {
            $message->update([
                'user_id' => $request->user_id,
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesMessage($message), 'Message modifié');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        $message->delete();

        $messages = Message::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesMessage::collection($messages), 'Message supprimé');
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Search a message by its content.
     *
     * @param  string $data
     * @return \Illuminate\Http\Response
     */
    public function search($data)
    {
        $messages = Message::where('message_content', $data)->get();

        return $this->handleResponse(ResourcesMessage::collection($messages), 'Messages trouvés');
    }
}
