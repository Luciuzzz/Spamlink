<?php

namespace App\Forms\RichEditor;

use Filament\Forms\Components\RichEditor\FileAttachmentProviders\Contracts\FileAttachmentProvider;
use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class LocalFileAttachmentProvider implements FileAttachmentProvider
{
    protected ?RichContentAttribute $attribute = null;

    public function __construct(
        protected string $disk = 'public',
        protected string $directory = 'landing-images',
        protected ?int $minWidth = null,
        protected ?int $minHeight = null,
        protected ?int $maxWidth = null,
        protected ?int $maxHeight = null,
    ) {}

    public function attribute(RichContentAttribute $attribute): static
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getFileAttachmentUrl(mixed $file): ?string
    {
        if (! $file) {
            return null;
        }

        return Storage::disk($this->disk)->url($file);
    }

    public function saveUploadedFileAttachment(TemporaryUploadedFile $file): mixed
    {
        $this->validateImageDimensions($file);

        return $file->store($this->directory, $this->disk);
    }

    public function getDefaultFileAttachmentVisibility(): ?string
    {
        return 'public';
    }

    public function isExistingRecordRequiredToSaveNewFileAttachments(): bool
    {
        return false;
    }

    public function cleanUpFileAttachments(array $exceptIds): void
    {
        // No cleanup to avoid deleting files referenced elsewhere.
    }

    protected function validateImageDimensions(TemporaryUploadedFile $file): void
    {
        $path = $file->getRealPath();
        if (! $path) {
            return;
        }

        $info = @getimagesize($path);
        if (! $info) {
            return;
        }

        [$width, $height] = $info;

        if ($this->minWidth && $width < $this->minWidth) {
            $this->failValidation();
        }

        if ($this->minHeight && $height < $this->minHeight) {
            $this->failValidation();
        }

        if ($this->maxWidth && $width > $this->maxWidth) {
            $this->failValidation();
        }

        if ($this->maxHeight && $height > $this->maxHeight) {
            $this->failValidation();
        }
    }

    protected function failValidation(): void
    {
        throw ValidationException::withMessages([
            $this->attribute?->getName() ?? 'content' => 'La imagen debe medir entre 960x540 y 1920x1080 px.',
        ]);
    }
}
