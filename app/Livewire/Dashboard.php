<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard')
            ->layout('layouts.app');
    }
}