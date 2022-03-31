<?php

namespace io3x1\FilamentTranslations\Resources\TranslationResource\Pages;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\ManageRecords;
use io3x1\FilamentTranslations\Services\SaveScan;
use io3x1\FilamentTranslations\Resources\TranslationResource;


class ManageTranslations extends ManageRecords
{
    protected static string $resource = TranslationResource::class;

    protected function getTitle(): string
    {
        return trans('translation.title');
    }

    protected function getActions(): array
    {
        return [
            ButtonAction::make('scan')
                ->icon('heroicon-o-document-search')
                ->action('scan')
                ->label(trans('translation.scan')),
            ButtonAction::make('settings')
                ->label('Settings')
                ->icon('heroicon-o-cog')
                ->modalHeading(trans('translation.modal.heading'))
                ->modalButton(trans('translation.modal.button'))
                ->form([
                    Select::make('language')
                        ->label('Language')
                        ->default(auth()->user()->lang)
                        ->options(config('filament-translations.locals'))
                        ->required(),
                ])
                ->action('settings'),
        ];
    }

    public function settings(): void
    {
        $user = User::find(auth()->user()->id);

        if ($user->lang === 'ar') {
            $user->lang = 'en';
            $user->save();
        } else if ($user->lang === 'en') {
            $user->lang = 'ar';
            $user->save();
        }

        session()->flash('notification', [
            'message' => __(trans('translation.notification') . $user->lang),
            'status' => "success",
        ]);

        redirect()->to('admin/translations');
    }

    public function scan()
    {
        $scan = new SaveScan();
        $scan->save();

        $this->notify('success', trans('translation.loaded'));
    }
}
