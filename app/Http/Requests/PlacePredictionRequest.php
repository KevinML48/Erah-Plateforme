<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\PredictionChoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class PlacePredictionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'prediction' => ['required', new Enum(PredictionChoice::class)],
            'stake_points' => ['required', 'integer', 'min:10', 'max:1000000'],
        ];
    }
}
