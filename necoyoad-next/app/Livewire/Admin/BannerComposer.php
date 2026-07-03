<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Banner;
use App\Models\BannerItem;
use App\Services\AuditService;
use App\Services\EavService;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * BannerComposer — visual drag-drop banner composer with absolute XYZ layer positioning.
 *
 * Replaces the legacy flat Repeater form with a modern visual editor:
 *   - Left panel: slide list (add/remove/reorder)
 *   - Center: canvas with absolute-positioned layers (drag to move)
 *   - Right panel: layer properties (x, y, z, width, height, content, animation)
 *
 * Layer data is stored as EAV on each BannerItem (group: 'slide', key: 'layers').
 * Banner engine config is stored as EAV on the Banner (group: 'banner').
 *
 * @see docs/reports/1782968369_modern_banner_module_3d_canvas_svg_composer.md
 */
#[Layout('components.layouts.app')]
class BannerComposer extends Component
{
    public int $bannerId;
    public string $bannerName = '';
    public string $engine = 'swiper';
    public bool $autoplay = true;
    public int $autoplaySpeed = 5000;
    public int $transitionSpeed = 800;
    public bool $loop = true;
    public bool $showNavigation = true;
    public bool $showPagination = true;
    public int $parallaxDepth = 0;

    public array $slides = [];
    public int $selectedSlide = 0;
    public int $selectedLayer = 0;

    public function mount(int $bannerId): void
    {
        $banner = Banner::findOrFail($bannerId);
        $this->bannerId = $banner->id;
        $this->bannerName = $banner->name ?? '';

        $eav = app(EavService::class);

        $config = $eav->getGroup($banner, 'banner');
        $this->engine = $config['engine'] ?? 'swiper';
        $this->autoplay = $config['autoplay'] ?? true;
        $this->autoplaySpeed = $config['autoplay_speed'] ?? 5000;
        $this->transitionSpeed = $config['transition_speed'] ?? 800;
        $this->loop = $config['loop'] ?? true;
        $this->showNavigation = $config['show_navigation'] ?? true;
        $this->showPagination = $config['show_pagination'] ?? true;
        $this->parallaxDepth = $config['parallax_depth'] ?? 0;

        $this->slides = $banner->items()
            ->orderBy('sort_order')
            ->get()
            ->map(function ($item) use ($eav) {
                $slideEav = $eav->getGroup($item, 'slide');
                $layers = $slideEav['layers'] ?? [];
                if (is_string($layers)) {
                    $layers = json_decode($layers, true) ?? [];
                }
                return [
                    'id' => $item->id,
                    'image' => $item->image,
                    'link' => $item->link,
                    'title' => $item->getTitle() ?? '',
                    'transition_in' => $slideEav['transition_in'] ?? 'fade',
                    'transition_out' => $slideEav['transition_out'] ?? 'fade',
                    'ken_burns' => $slideEav['ken_burns'] ?? 'none',
                    'layers' => $layers,
                ];
            })->values()->toArray();

        if (empty($this->slides)) {
            $this->addSlide();
        }
    }

    public function addSlide(): void
    {
        $bannerItem = BannerItem::create([
            'banner_id' => $this->bannerId,
            'image' => '',
            'link' => '',
            'sort_order' => count($this->slides),
            'status' => true,
        ]);

        $this->slides[] = [
            'id' => $bannerItem->id,
            'image' => '',
            'link' => '',
            'title' => '',
            'transition_in' => 'fade',
            'transition_out' => 'fade',
            'ken_burns' => 'none',
            'layers' => [],
        ];

        $this->selectedSlide = count($this->slides) - 1;
        $this->selectedLayer = 0;
    }

    public function deleteSlide(int $index): void
    {
        if (count($this->slides) <= 1) {
            return;
        }

        $slide = $this->slides[$index] ?? null;
        if ($slide && !empty($slide['id'])) {
            BannerItem::find($slide['id'])?->delete();
        }

        unset($this->slides[$index]);
        $this->slides = array_values($this->slides);

        if ($this->selectedSlide >= count($this->slides)) {
            $this->selectedSlide = count($this->slides) - 1;
        }
    }

    public function selectSlide(int $index): void
    {
        $this->selectedSlide = $index;
        $this->selectedLayer = 0;
    }

    public function addLayer(string $type): void
    {
        $layer = [
            'id' => Str::uuid()->toString(),
            'type' => $type,
            'content' => $type === 'text' ? 'New Text' : '',
            'image' => '',
            'x' => 50,
            'y' => 50,
            'z' => count($this->slides[$this->selectedSlide]['layers'] ?? []) + 1,
            'width' => $type === 'image' ? 200 : 'auto',
            'height' => $type === 'image' ? 150 : 'auto',
            'color' => '#ffffff',
            'background' => 'transparent',
            'font_size' => $type === 'text' ? 24 : 16,
            'font_weight' => 'normal',
            'text_align' => 'left',
            'link_url' => '',
            'animation_in' => 'fade',
            'animation_out' => 'fade',
            'delay' => 0,
            'duration' => 600,
            'easing' => 'power2.out',
        ];

        $this->slides[$this->selectedSlide]['layers'][] = $layer;
        $this->selectedLayer = count($this->slides[$this->selectedSlide]['layers']) - 1;
    }

    public function deleteLayer(int $index): void
    {
        unset($this->slides[$this->selectedSlide]['layers'][$index]);
        $this->slides[$this->selectedSlide]['layers'] = array_values($this->slides[$this->selectedSlide]['layers']);

        if ($this->selectedLayer >= count($this->slides[$this->selectedSlide]['layers'])) {
            $this->selectedLayer = max(0, count($this->slides[$this->selectedSlide]['layers']) - 1);
        }
    }

    public function selectLayer(int $index): void
    {
        $this->selectedLayer = $index;
    }

    public function updateLayerPosition(int $layerIndex, int $x, int $y): void
    {
        if (!isset($this->slides[$this->selectedSlide]['layers'][$layerIndex])) {
            return;
        }
        $this->slides[$this->selectedSlide]['layers'][$layerIndex]['x'] = $x;
        $this->slides[$this->selectedSlide]['layers'][$layerIndex]['y'] = $y;
    }

    public function updateLayerZ(int $layerIndex, int $direction): void
    {
        $layers = &$this->slides[$this->selectedSlide]['layers'];
        if (!isset($layers[$layerIndex])) {
            return;
        }
        $layers[$layerIndex]['z'] = max(0, $layers[$layerIndex]['z'] + $direction);
    }

    public function reorderSlides(array $order): void
    {
        $newSlides = [];
        foreach ($order as $index) {
            if (isset($this->slides[$index])) {
                $newSlides[] = $this->slides[$index];
            }
        }
        $this->slides = $newSlides;
        $this->selectedSlide = 0;
    }

    public function save(): void
    {
        $banner = Banner::findOrFail($this->bannerId);
        $eav = app(EavService::class);
        $audit = app(AuditService::class);

        $eav->setMany($banner, [
            'banner' => [
                'engine' => $this->engine,
                'autoplay' => $this->autoplay,
                'autoplay_speed' => $this->autoplaySpeed,
                'transition_speed' => $this->transitionSpeed,
                'loop' => $this->loop,
                'show_navigation' => $this->showNavigation,
                'show_pagination' => $this->showPagination,
                'parallax_depth' => $this->parallaxDepth,
            ],
        ]);

        foreach ($this->slides as $index => $slideData) {
            $item = BannerItem::find($slideData['id']);
            if (!$item) {
                continue;
            }

            $item->update([
                'image' => $slideData['image'],
                'link' => $slideData['link'],
                'sort_order' => $index,
                'status' => true,
            ]);

            $eav->setMany($item, [
                'slide' => [
                    'layers' => $slideData['layers'],
                    'transition_in' => $slideData['transition_in'],
                    'transition_out' => $slideData['transition_out'],
                    'ken_burns' => $slideData['ken_burns'],
                ],
            ]);
        }

        $audit->logModel(
            event: 'banner_composer_saved',
            modelClass: Banner::class,
            modelId: $banner->id,
            changes: ['slide_count' => count($this->slides), 'engine' => $this->engine],
        );

        $this->dispatch('banner-saved', message: 'Banner saved successfully');
    }

    public function render()
    {
        return view('livewire.admin.banner-composer');
    }
}
