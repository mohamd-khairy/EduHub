<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionMethod;

trait HasAutoRelations
{
    /**
     * Get all Eloquent relation methods defined in the model.
     */
    public static function relationsList(): array
    {
        $model = new static();

        return collect(get_class_methods($model))
            ->reject(fn ($method) => str_starts_with($method, '__'))
            ->filter(function ($method) use ($model) {
                try {
                    $reflection = new ReflectionMethod($model, $method);

                    if ($reflection->getNumberOfParameters() > 0) {
                        return false;
                    }

                    $result = $model->$method();

                    return $result instanceof Relation;
                } catch (\Throwable $e) {
                    return false;
                }
            })
            ->values()
            ->all();
    }
}
