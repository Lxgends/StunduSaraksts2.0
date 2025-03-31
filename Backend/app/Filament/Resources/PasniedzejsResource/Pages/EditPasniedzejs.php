<?php

namespace App\Filament\Resources\PasniedzejsResource\Pages;

use App\Filament\Resources\PasniedzejsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rule;

class EditPasniedzejs extends EditRecord
{
    protected static string $resource = PasniedzejsResource::class;

    protected function beforeSave(): void
    {
        $data = $this->form->getState();

        $validator = \Validator::make($data, [
            'Vards' => [
                'required',
                Rule::unique('pasniedzejs')
                    ->ignore($this->record->id)
                    ->where(function ($query) use ($data) {
                        return $query
                            ->where('Vards', $data['Vards'])
                            ->where('Uzvards', $data['Uzvards']);
                    }),
            ],
            'Uzvards' => 'required',
        ]);

        if ($validator->fails()) {
            Notification::make()
                ->title('Validation Error')
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