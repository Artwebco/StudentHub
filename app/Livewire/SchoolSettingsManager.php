<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\SchoolSetting;

class SchoolSettingsManager extends Component
{
    use WithFileUploads;

    public $school_name;
    public $activity;
    public $registration_number;
    public $swift_number;
    public $iban_number;
    public $tax_number;
    public $address;
    public $bank_account;
    public $bank_name;

    public $email;
    public $website;
    public $phone;

    public $logo; // temporary uploaded file
    public $existingLogo; // path to existing logo in storage

    protected $rules = [
        'school_name' => 'required|string|max:255',
        'activity' => 'nullable|string|max:255',
        'registration_number' => 'nullable|string|max:255',
        'swift_number' => 'nullable|string|max:255',
        'iban_number' => 'nullable|string|max:255',
        'tax_number' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:500',
        'bank_account' => 'nullable|string|max:255',
        'bank_name' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'website' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:50',
        'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ];

    public function mount()
    {
        $settings = SchoolSetting::first();

        if ($settings) {
            $this->school_name = $settings->school_name;
            $this->activity = $settings->activity;
            $this->registration_number = $settings->registration_number;
            $this->swift_number = $settings->swift;
            $this->iban_number = $settings->iban;
            $this->tax_number = $settings->tax_number;
            $this->address = $settings->address;
            $this->bank_account = $settings->bank_account;
            $this->bank_name = $settings->bank_name;
            $this->email = $settings->email;
            $this->website = $settings->website;
            $this->phone = $settings->phone;
            $this->existingLogo = $settings->logo_path;
        }
    }

    public function updatedLogo()
    {
        $this->validateOnly('logo');
    }

    public function save()
    {
        $this->validate();

        $settings = SchoolSetting::first();

        if (!$settings) {
            $settings = new SchoolSetting();
        }

        if ($this->logo) {
            $path = $this->logo->store('logos', 'public');
            $settings->logo_path = $path;
            $this->existingLogo = $path;
        }

        $settings->school_name = $this->school_name;
        $settings->activity = $this->activity;
        $settings->registration_number = $this->registration_number;
        $settings->swift = $this->swift_number;
        $settings->iban = $this->iban_number;
        $settings->tax_number = $this->tax_number;
        $settings->address = $this->address;
        $settings->bank_account = $this->bank_account;
        $settings->bank_name = $this->bank_name;
        $settings->email = $this->email;
        $settings->website = $this->website;
        $settings->phone = $this->phone;

        $settings->save();

        session()->flash('message', __('admin.settings.saved'));
    }

    public function render()
    {
        return view('livewire.school-settings-manager')->layout('layouts.app');
    }
}
