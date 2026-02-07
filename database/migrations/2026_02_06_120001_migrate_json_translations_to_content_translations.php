<?php

use App\Models\ContentTranslation;
use App\Models\Language;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migrate existing JSON translations (pages.translations, products.translations)
     * into content_translations, then drop JSON columns.
     */
    public function up(): void
    {
        $defaultCode = Language::getDefaultCode();

        if (Schema::hasTable('pages') && Schema::hasColumn('pages', 'translations')) {
            $pages = DB::table('pages')->whereNotNull('translations')->get();
            foreach ($pages as $page) {
                $decoded = is_string($page->translations)
                    ? json_decode($page->translations, true)
                    : $page->translations;
                if (! is_array($decoded)) {
                    continue;
                }
                foreach ($decoded as $languageCode => $fields) {
                    if ($languageCode === $defaultCode || ! is_array($fields)) {
                        continue;
                    }
                    foreach ($fields as $field => $value) {
                        if (! is_string($field) || ! in_array($field, ['title', 'content', 'meta_title', 'meta_description'], true)) {
                            continue;
                        }
                        ContentTranslation::query()->updateOrCreate(
                            [
                                'translatable_type' => \App\Models\Page::class,
                                'translatable_id' => $page->id,
                                'language_code' => $languageCode,
                                'field' => $field,
                            ],
                            ['value' => $value === null ? '' : (string) $value]
                        );
                    }
                }
            }
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('translations');
            });
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'translations')) {
            $products = DB::table('products')->whereNotNull('translations')->get();
            foreach ($products as $product) {
                $decoded = is_string($product->translations)
                    ? json_decode($product->translations, true)
                    : $product->translations;
                if (! is_array($decoded)) {
                    continue;
                }
                foreach ($decoded as $languageCode => $fields) {
                    if ($languageCode === $defaultCode || ! is_array($fields)) {
                        continue;
                    }
                    foreach ($fields as $field => $value) {
                        if (! is_string($field) || ! in_array($field, ['name', 'description'], true)) {
                            continue;
                        }
                        ContentTranslation::query()->updateOrCreate(
                            [
                                'translatable_type' => \Modules\Product\App\Models\Product::class,
                                'translatable_id' => $product->id,
                                'language_code' => $languageCode,
                                'field' => $field,
                            ],
                            ['value' => $value === null ? '' : (string) $value]
                        );
                    }
                }
            }
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('translations');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('content_translations')) {
            ContentTranslation::query()->whereIn('translatable_type', [
                \App\Models\Page::class,
                \Modules\Product\App\Models\Product::class,
            ])->delete();
        }

        if (Schema::hasTable('pages') && ! Schema::hasColumn('pages', 'translations')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->json('translations')->nullable()->after('meta_description');
            });
        }
        if (Schema::hasTable('products') && ! Schema::hasColumn('products', 'translations')) {
            Schema::table('products', function (Blueprint $table) {
                $table->json('translations')->nullable()->after('meta_keywords');
            });
        }
    }
};
