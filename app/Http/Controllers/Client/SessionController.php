<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Session;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function show(Session $session): View
    {
        $this->authorize('view', $session);

        $session->loadMissing(['client', 'therapist', 'assistant', 'note']);

        $note = $session->note;

        return view('client.sessions.show', [
            'session' => $session,
            'hasEiNotes' => $note ? $this->noteHasAnyValue(
                $note,
                array_merge(Note::EI_BOOLEAN_FIELDS, Note::EI_TEXT_FIELDS, Note::EI_INTEGER_FIELDS)
            ) : false,
            'hasEfNotes' => $note ? $this->noteHasAnyValue(
                $note,
                array_merge(Note::EF_BOOLEAN_FIELDS, Note::EF_TEXT_FIELDS)
            ) : false,
        ]);
    }

    private function noteHasAnyValue(Note $note, array $fields): bool
    {
        foreach ($fields as $field) {
            if (is_bool($note->{$field}) && $note->{$field}) {
                return true;
            }

            if (! is_bool($note->{$field}) && filled($note->{$field})) {
                return true;
            }
        }

        return false;
    }
}
