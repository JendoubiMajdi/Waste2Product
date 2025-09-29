<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Don;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DonForm extends Component
{
    use WithFileUploads;

    public $type = 'money';
    public $amount;
    public $description;
    public $status = 'pending';
    public $share = false;
    public $card_number;
    public $card_expiry;
    public $card_cvv;

    public function submit()
    {
        $this->validate([
            'type' => 'required|in:money,product',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string',
            'card_number' => $this->type === 'money' ? 'required|string|size:16' : 'nullable',
            'card_expiry' => $this->type === 'money' ? 'required|string|regex:/^\d{2}\/\d{2}$/' : 'nullable',
            'card_cvv' => $this->type === 'money' ? 'required|string|size:3' : 'nullable',
        ]);

        $don = Auth::user()->dons()->create([
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $this->description,
            'status' => $this->type === 'money' ? 'completed' : 'pending',
        ]);

        if ($this->share) {
            // Create a text-based forum post about the donation
            $donationType = ucfirst($this->type);
            $content = "🎉 Just made a donation! \n\n";
            $content .= "💝 Type: {$donationType}\n";
            $content .= "💰 Amount: {$this->amount}\n";
            $content .= "📝 Description: {$this->description}\n\n";
            $content .= "Thank you for supporting our community! 🌟";

            Auth::user()->posts()->create([
                'content' => $content,
                'media_type' => 'text',
                'media_url' => null,
                'post_type' => 'donation_share',
                'don_id' => $don->id,
            ]);
        }

        session()->flash('success', 'Donation created successfully!');
        $this->redirect(route('dashboard'));
    }

    public function render()
    {
        return view('livewire.don-form');
    }
}