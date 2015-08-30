<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;

class NotesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $user = Auth::user();
        $notes = Note::where('user_id', '=', $user->id)->get();
        $result = [];
        foreach ($notes as $note) {
            $ts = new \DateTime($note->updated_at);
            $result[] = [
                'id' => $note->id,
                'title' => $note->title,
                'updated_at' => $ts->getTimestamp()
            ];
        }
        return Response::json(
            [
                'data' => $result
            ]
        );
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->get('data');
        $note = new Note();
        $note->user_id = Auth::user()->id;
        $note->title = $data['title'];
        $note->content = $data['content'];
        $note->save();
        return $this->show($note->id);
    }
    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $result = [];
        $note = Note::where('user_id', '=', Auth::user()->id)->where('id', '=', $id)->firstOrFail();
        $ts = new \DateTime($note->updated_at);
        $result = [
            'id' => $note->id,
            'title' => $note->title,
            'content' => $note->content,
            'updated_at' => $ts->getTimestamp()
        ];
        return Response::json(
            [
                'data' => $result
            ]
        );
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        $data = $request->get('data');
        $note = Note::where('user_id', '=', Auth::user()->id)->where('id', '=', $id)->firstOrFail();
        if(isset($data['title'])){
            $note->title = $data['title'];
        }
        if(isset($data['content'])){
            $note->content = $data['content'];
        }
        $note->save();
        return $this->show($id);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $note = Note::where('user_id', '=', Auth::user()->id)->where('id', '=', $id)->firstOrFail();
        $note->delete();
        return [
            'result' => 'success'
        ];
    }
}
