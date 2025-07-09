<?php

namespace App\Livewire\Dashboard;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class DashboardLivewire extends Component
{
    use LivewireAlert;
    public $modal = false;
    public $id;
    
    public function create($id = null){
        $this->id = $id;
        if($id){
            $this->modal = true;
        }else{
            $this->modal = true;
        }
    }


    public function delete($id){
        
    }

    public function update(){
        
    }

    public function store(){
        $this->validate([

        ]);
    }

    public function cancel(){
        $this->reset(["modal","id"]);
        $this->dispatch('modal-cancel');
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-livewire');
    }
}
