<?php

declare(strict_types=1);

namespace App\Traits;

use App\Services\AuditService;

/**
 * Auditable — automatically logs model CRUD events to the audit system.
 *
 * This trait replaces the dead-code hooks that were on NecoyoadResource
 * (Filament 3 only calls afterCreate/afterSave/afterDelete on Page classes,
 * not on the Resource class). By using model boot events instead, audit
 * logging works for ALL write paths: Filament admin, API, tinker, seeders.
 *
 * Usage:
 *   class Product extends Model {
 *       use Auditable;
 *   }
 *
 * Logs to AuditService which writes to user_activity table + audit.log channel.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (self $model) {
            try {
                app(AuditService::class)->logModel(
                    event: 'created',
                    modelClass: get_class($model),
                    modelId: $model->getKey(),
                    changes: $model->getAttributes(),
                );
            } catch (\Throwable) {
                // Don't let audit failure break the create
            }
        });

        static::updated(function (self $model) {
            try {
                app(AuditService::class)->logModel(
                    event: 'updated',
                    modelClass: get_class($model),
                    modelId: $model->getKey(),
                    changes: $model->getChanges(),
                );
            } catch (\Throwable) {
                // Don't let audit failure break the update
            }
        });

        static::deleted(function (self $model) {
            try {
                app(AuditService::class)->logModel(
                    event: 'deleted',
                    modelClass: get_class($model),
                    modelId: $model->getKey(),
                );
            } catch (\Throwable) {
                // Don't let audit failure break the delete
            }
        });
    }
}
