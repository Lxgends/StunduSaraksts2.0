<?php

namespace App\Filament\Resources\KurssResource\Pages;

use App\Filament\Resources\KurssResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rule;

class EditKurss extends EditRecord
{
    protected static string $resource = KurssResource::class;

    protected function beforeSave(): void
    {
        $data = $this->form->getState();

        $validator = \Validator::make($data, [
            'Nosaukums' => [
                'required',
                'max:255',
                Rule::unique('kurss', 'Nosaukums')
                    ->ignore($this->record->id)
            ],
        ], [
            'Nosaukums.unique' => 'Kurss ar šādu nosaukumu jau eksistē'
        ]);

        if ($validator->fails()) {
            Notification::make()
                ->title('Validācijas kļūda')
                ->body($validator->errors()->first())
                ->danger()
                ->send();

            $this->halt();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}