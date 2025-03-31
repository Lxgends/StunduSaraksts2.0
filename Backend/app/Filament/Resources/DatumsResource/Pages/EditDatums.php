<?php

namespace App\Filament\Resources\DatumsResource\Pages;

use App\Filament\Resources\DatumsResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\Datums;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Filament\Actions;

class EditDatums extends EditRecord
{
    protected static string $resource = DatumsResource::class;

    protected function beforeValidate(): void
    {
        $data = $this->form->getState();

        if (empty($data['PirmaisDatums']) || empty($data['PedejaisDatums'])) {
            return;
        }

        $monday = Carbon::parse($data['PirmaisDatums']);
        $friday = Carbon::parse($data['PedejaisDatums']);

        $errors = [];

        $exists = Datums::where('PirmaisDatums', $monday->format('Y-m-d'))
            ->where('PedejaisDatums', $friday->format('Y-m-d'))
            ->where('id', '!=', $this->record->id)
            ->exists();
            
        if ($exists) {
            $errors['PirmaisDatums'] = 'Šāda nedēļa jau ir ieplānota!';
        }

        if (!$monday->isMonday()) {
            $errors['PirmaisDatums'] = 'Nedēļas sākuma datumam jābūt pirmdienai!';
        }

        if (!$friday->isFriday()) {
            $errors['PedejaisDatums'] = 'Nedēļas beigu datumam jābūt piektdienai!';
        }

        if ($monday->weekOfYear !== $friday->weekOfYear) {
            $errors['PirmaisDatums'] = 'Sākuma un beigu datumam jābūt vienā nedēļā!';
            $errors['PedejaisDatums'] = 'Sākuma un beigu datumam jābūt vienā nedēļā!';
        }

        if ($monday->diffInDays($friday) !== 4) {
            $errors['PirmaisDatums'] = 'Nedēļai jābūt tieši no pirmdienas līdz piektdienai!';
            $errors['PedejaisDatums'] = 'Nedēļai jābūt tieši no pirmdienas līdz piektdienai!';
        }

        if (!empty($errors)) {
            Notification::make()
                ->title('Validācijas kļūda')
                ->body(reset($errors))
                ->danger()
                ->send();

            throw ValidationException::withMessages($errors);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}