<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;
use App\Jobs\NotifyAllVoters;
use Illuminate\Http\Response;

class SetStatus extends Component
{
    public $idea;
    public $status;
    public $notifyAllVoters;

    public function mount(Idea $idea)
    {
       $this->idea = $idea;
       $this->status = $idea->status_id;
    }
    
    public function setStatus()
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->idea->status_id = $this->status;
        $this->idea->save();

        if ($this->notifyAllVoters) {
            NotifyAllVoters::dispatch($this->idea);
            
            session()->flash('success_message', 'Emails sent successfully.');
        }

        $this->emit('statusWasUpdated');
    }

    public function render()
    {
        return view('livewire.set-status');
    }
}