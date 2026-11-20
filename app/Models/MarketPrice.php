<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'ticker_code',
        'category',
        'price_range',
        'change_percent',
        'previous_price',
        'unit',
        'location',
        'trend',
        'trend_label',
        'source',
        'notes',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'change_percent' => 'float',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('updated_at');
    }

    public function getTickerDisplayAttribute(): string
    {
        if (! empty($this->ticker_code)) {
            return strtoupper($this->ticker_code);
        }

        $clean = preg_replace('/[^A-Za-z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $this->product_name));
        $code = strtoupper(substr($clean ?: 'PROD', 0, 4));

        return 'SEN-' . $code;
    }

    public function getIsCheaperAttribute(): bool
    {
        return $this->change_percent !== null && $this->change_percent < 0;
    }

    public function getIsMoreExpensiveAttribute(): bool
    {
        return $this->change_percent !== null && $this->change_percent > 0;
    }

    public function getFormattedChangeAttribute(): string
    {
        if ($this->change_percent === null || (float) $this->change_percent == 0.0) {
            return '0.0%';
        }

        $sign = $this->change_percent > 0 ? '+' : '';
        return $sign . number_format($this->change_percent, 1, ',', '') . '%';
    }

    /**
     * Format Bourse type capture d'écran : "+0.30 %", "-0.40 %"
     */
    public function getBoursePercentDisplayAttribute(): string
    {
        if ($this->change_percent === null || (float) $this->change_percent == 0.0) {
            return '0.00 %';
        }

        $sign = $this->change_percent > 0 ? '+' : '';
        return $sign . number_format($this->change_percent, 2, '.', '') . ' %';
    }

    public function getBoursePercentTextColorAttribute(): string
    {
        if ($this->is_cheaper) {
            return 'text-emerald-600 font-extrabold';
        }

        if ($this->is_more_expensive) {
            return 'text-rose-600 font-extrabold';
        }

        return 'text-slate-500 font-semibold';
    }

    public function getIconEmojiAttribute(): string
    {
        $name = mb_strtolower($this->product_name ?? '');

        if (str_contains($name, 'riz')) return '🌾';
        if (str_contains($name, 'oignon')) return '🧅';
        if (str_contains($name, 'maïs') || str_contains($name, 'mais')) return '🌽';
        if (str_contains($name, 'pomme de terre') || str_contains($name, 'patate')) return '🥔';
        if (str_contains($name, 'arachide')) return '🥜';
        if (str_contains($name, 'tomate')) return '🍅';
        if (str_contains($name, 'bélier') || str_contains($name, 'mouton') || str_contains($name, 'bétail') || str_contains($name, 'ladoum')) return '🐑';
        if (str_contains($name, 'boeuf') || str_contains($name, 'vache')) return '🐂';
        if (str_contains($name, 'chevre') || str_contains($name, 'chèvre')) return '🐐';
        if (str_contains($name, 'labour') || str_contains($name, 'tracteur') || str_contains($name, 'service')) return '🚜';
        if (str_contains($name, 'engrais') || str_contains($name, 'semence') || str_contains($name, 'intrant')) return '🌱';
        if (str_contains($name, 'mangue') || str_contains($name, 'fruit') || str_contains($name, 'agrumes')) return '🥭';

        return match (mb_strtolower($this->category ?? '')) {
            'céréales', 'céréales & oléagineux' => '🌾',
            'légumes & maraîchage', 'maraîchage' => '🥬',
            'élevage & bétail', 'élevage' => '🐑',
            'matériel & services', 'machinisme' => '🚜',
            'fruits' => '🥭',
            default => '🌱',
        };
    }

    public function getSparklineColorAttribute(): string
    {
        if ($this->is_cheaper) {
            return '#10b981';
        }

        if ($this->is_more_expensive) {
            return '#f43f5e';
        }

        return '#94a3b8';
    }

    public function getBourseStripeColorAttribute(): string
    {
        if ($this->is_cheaper) {
            return 'bg-emerald-500';
        }

        if ($this->is_more_expensive) {
            return 'bg-rose-500';
        }

        return 'bg-slate-300';
    }

    public function getBourseSquareColorAttribute(): string
    {
        $palette = [
            '#1e3a8a', // Dark Navy Blue (ex: CAC 40)
            '#d97706', // Golden Orange (ex: FUTURE CAC)
            '#0d9488', // Teal (ex: SBF 120)
            '#dc2626', // Crimson Red (ex: CAC MID)
            '#6366f1', // Indigo Blue (ex: DAX)
            '#0284c7', // Cyan Blue (ex: FTSE 100)
            '#64748b', // Steel Blue (ex: DOW JONES)
            '#334155', // Charcoal (ex: NASDAQ 100)
            '#93c5fd', // Soft Light Blue (ex: S&P 500)
            '#10b981', // Emerald Green
            '#8b5cf6', // Purple
        ];

        $key = ($this->sort_order ?: $this->id ?: 0);
        $idx = abs((int) $key) % count($palette);

        return $palette[$idx];
    }

    /**
     * Règle Bourse demandée :
     * VERT si DEVENU MOINS CHER (change_percent < 0)
     * ROUGE si DEVENU PLUS CHER (change_percent > 0)
     * GRIS si stable
     */
    public function getBourseBadgeClassesAttribute(): string
    {
        if ($this->is_cheaper) {
            return 'bg-emerald-50 text-emerald-700 border-emerald-300 ring-1 ring-emerald-500/20';
        }

        if ($this->is_more_expensive) {
            return 'bg-rose-50 text-rose-700 border-rose-300 ring-1 ring-rose-500/20';
        }

        return 'bg-slate-100 text-slate-700 border-slate-300';
    }

    public function getBourseDarkClassesAttribute(): string
    {
        if ($this->is_cheaper) {
            return 'text-emerald-400 bg-emerald-950/70 border border-emerald-500/30';
        }

        if ($this->is_more_expensive) {
            return 'text-rose-400 bg-rose-950/70 border border-rose-500/30';
        }

        return 'text-slate-300 bg-slate-800/80 border border-slate-700';
    }

    public function getBourseArrowAttribute(): string
    {
        if ($this->is_cheaper) {
            return '▼';
        }

        if ($this->is_more_expensive) {
            return '▲';
        }

        return '=';
    }

    public function getTrendBadgeClassesAttribute(): string
    {
        return match ($this->trend) {
            'up', 'hausse' => 'bg-rose-50 text-rose-700 border-rose-200',
            'down', 'baisse' => 'bg-sky-50 text-sky-700 border-sky-200',
            'active', 'actif' => 'bg-amber-50 text-amber-800 border-amber-200',
            'available', 'disponible' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'variable' => 'bg-slate-100 text-slate-700 border-slate-200',
            default => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        };
    }

    public function getTrendIconNameAttribute(): string
    {
        return match ($this->trend) {
            'up', 'hausse' => 'arrow-up',
            'down', 'baisse' => 'arrow-down',
            'active', 'actif' => 'activity',
            'available', 'disponible' => 'check-circle',
            'variable' => 'sliders',
            default => 'minus',
        };
    }
}

