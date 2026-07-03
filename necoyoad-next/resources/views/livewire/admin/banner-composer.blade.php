{{--
    Banner Composer — visual drag-drop banner editor with absolute XYZ layer positioning.

    Layout:
      - Left panel: slide list (add/remove/select/reorder)
      - Center: canvas with absolute-positioned layers (drag to move)
      - Right panel: layer properties (x, y, z, width, height, content, animation)
      - Bottom: banner engine config (engine select, autoplay, transitions)
--}}
<div x-data="bannerComposer()" class="banner-composer">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4 p-4 bg-white rounded-lg shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $bannerName }} — Visual Composer</h1>
            <p class="text-sm text-gray-500">Drag layers on the canvas to position them. Use the right panel to edit properties.</p>
        </div>
        <div class="flex gap-2">
            <a href="/admin/banners" class="px-4 py-2 text-gray-600 hover:text-gray-800">Back to Banners</a>
            <button wire:click="save" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                Save Banner
            </button>
        </div>
    </div>

    {{-- Toast notification --}}
    <div x-show="saved" x-cloak x-transition class="fixed top-4 right-4 z-50 px-6 py-3 bg-green-500 text-white rounded-lg shadow-lg">
        Banner saved successfully!
    </div>

    {{-- Main 3-column layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- LEFT: Slide List --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="font-semibold mb-3 text-gray-700">Slides</h3>
                <div class="space-y-2">
                    @foreach ($slides as $index => $slide)
                        <div class="flex items-center gap-2 p-2 rounded cursor-pointer transition-colors"
                             :class="{{ $index === $selectedSlide ? 'bg-blue-100 border-blue-300' : 'bg-gray-50 hover:bg-gray-100' }}"
                             wire:click="selectSlide({{ $index }})">
                            <div class="w-12 h-12 bg-gray-200 rounded overflow-hidden flex-shrink-0">
                                @if (!empty($slide['image']))
                                    <img src="/storage/{{ $slide['image'] }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No img</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-700 truncate">Slide {{ $index + 1 }}</p>
                                <p class="text-xs text-gray-400">{{ count($slide['layers'] ?? []) }} layers</p>
                            </div>
                            @if (count($slides) > 1)
                                <button wire:click="deleteSlide({{ $index }})" wire:confirm="Delete this slide?"
                                        class="text-red-500 hover:text-red-700 text-xs" onclick="event.stopPropagation()">
                                    ✕
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button wire:click="addSlide" class="mt-3 w-full px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm font-medium">
                    + Add Slide
                </button>
            </div>
        </div>

        {{-- CENTER: Canvas --}}
        <div class="lg:col-span-7">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-700">Canvas — Slide {{ $selectedSlide + 1 }}</h3>
                    <div class="flex gap-1">
                        <button @click="addLayerType('text')" class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">+ Text</button>
                        <button @click="addLayerType('image')" class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200">+ Image</button>
                        <button @click="addLayerType('button')" class="px-2 py-1 text-xs bg-purple-100 text-purple-700 rounded hover:bg-purple-200">+ Button</button>
                        <button @click="addLayerType('shape')" class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">+ Shape</button>
                    </div>
                </div>

                {{-- Canvas with absolute-positioned layers --}}
                <div x-ref="canvas"
                     class="banner-canvas relative bg-gray-800 rounded-lg overflow-hidden"
                     style="width: 100%; height: 450px; background-image: url({{ !empty($slides[$selectedSlide]['image'] ?? '') ? '/storage/' . $slides[$selectedSlide]['image'] : '' }}); background-size: cover; background-position: center;">

                    @if (empty($slides[$selectedSlide]['image'] ?? ''))
                        <div class="absolute inset-0 flex items-center justify-center text-gray-500 text-sm">
                            Set a background image in the right panel →
                        </div>
                    @endif

                    {{-- Render layers --}}
                    @php $currentSlide = $slides[$selectedSlide] ?? ['layers' => []]; @endphp
                    @foreach ($currentSlide['layers'] ?? [] as $layerIndex => $layer)
                        <div class="absolute cursor-move border-2 transition-colors"
                             :class="{{ $layerIndex === $selectedLayer ? 'border-blue-400' : 'border-transparent hover:border-gray-400' }}"
                             style="left: {{ $layer['x'] ?? 0 }}px; top: {{ $layer['y'] ?? 0 }}px; z-index: {{ $layer['z'] ?? 1 }}; width: {{ $layer['width'] ?? 'auto' }}; height: {{ $layer['height'] ?? 'auto' }};"
                             wire:click="selectLayer({{ $layerIndex }})"
                             x-data="{ dragging: false, startX: 0, startY: 0, layerX: {{ $layer['x'] ?? 0 }}, layerY: {{ $layer['y'] ?? 0 }} }"
                             @mousedown="startDrag($event, {{ $layerIndex }})">

                            @if (($layer['type'] ?? '') === 'text')
                                <div style="color: {{ $layer['color'] ?? '#fff' }}; font-size: {{ $layer['font_size'] ?? 24 }}px; font-weight: {{ $layer['font_weight'] ?? 'normal' }}; text-align: {{ $layer['text_align'] ?? 'left' }}; padding: 4px;">
                                    {{ $layer['content'] ?? 'Text' }}
                                </div>
                            @elseif (($layer['type'] ?? '') === 'image')
                                <img src="{{ !empty($layer['image']) ? '/storage/' . $layer['image'] : '' }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @elseif (($layer['type'] ?? '') === 'button')
                                <a href="{{ $layer['link_url'] ?? '#' }}" class="inline-block px-4 py-2 rounded font-semibold text-sm"
                                   style="background: {{ $layer['background'] ?? '#3b82f6' }}; color: {{ $layer['color'] ?? '#fff' }};">
                                    {{ $layer['content'] ?? 'Click Here' }}
                                </a>
                            @elseif (($layer['type'] ?? '') === 'shape')
                                <div style="width: 100%; height: 100%; background: {{ $layer['background'] ?? '#3b82f6' }}; border-radius: 4px;"></div>
                            @endif
                        </div>
                    @endforeach

                    {{-- No layers message --}}
                    @if (empty($currentSlide['layers']))
                        <div class="absolute inset-0 flex items-center justify-center text-white text-sm opacity-50 pointer-events-none">
                            Click + Text / + Image / + Button / + Shape above to add layers
                        </div>
                    @endif
                </div>

                {{-- Layer z-index controls --}}
                @if (!empty($currentSlide['layers']) && isset($currentSlide['layers'][$selectedLayer]))
                    <div class="mt-2 flex items-center gap-2 text-sm text-gray-600">
                        <span>Layer {{ $selectedLayer + 1 }} Z-Index: {{ $currentSlide['layers'][$selectedLayer]['z'] ?? 1 }}</span>
                        <button wire:click="updateLayerZ({{ $selectedLayer }}, 1)" class="px-2 py-1 bg-gray-100 rounded hover:bg-gray-200">↑ Forward</button>
                        <button wire:click="updateLayerZ({{ $selectedLayer }}, -1)" class="px-2 py-1 bg-gray-100 rounded hover:bg-gray-200">↓ Backward</button>
                        <button wire:click="deleteLayer({{ $selectedLayer }})" wire:confirm="Delete this layer?" class="px-2 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200">Delete</button>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT: Properties Panel --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="font-semibold mb-3 text-gray-700">Properties</h3>

                {{-- Slide properties --}}
                <div class="mb-4 pb-4 border-b">
                    <h4 class="text-sm font-semibold text-gray-600 mb-2">Slide Settings</h4>
                    <label class="block text-xs text-gray-500 mb-1">Background Image Path</label>
                    <input type="text" wire:model="slides.{{ $selectedSlide }}.image" placeholder="banners/slide1.jpg"
                           class="w-full px-2 py-1 text-sm border rounded">

                    <label class="block text-xs text-gray-500 mb-1 mt-2">Slide Link URL</label>
                    <input type="text" wire:model="slides.{{ $selectedSlide }}.link" placeholder="https://..."
                           class="w-full px-2 py-1 text-sm border rounded">

                    <label class="block text-xs text-gray-500 mb-1 mt-2">Transition In</label>
                    <select wire:model="slides.{{ $selectedSlide }}.transition_in" class="w-full px-2 py-1 text-sm border rounded">
                        <option value="fade">Fade</option>
                        <option value="slide-left">Slide Left</option>
                        <option value="slide-up">Slide Up</option>
                        <option value="scale">Scale</option>
                        <option value="rotate">Rotate</option>
                        <option value="flip">3D Flip</option>
                        <option value="distort">WebGL Distort</option>
                        <option value="particle-dissolve">Particle Dissolve</option>
                    </select>

                    <label class="block text-xs text-gray-500 mb-1 mt-2">Ken Burns Effect</label>
                    <select wire:model="slides.{{ $selectedSlide }}.ken_burns" class="w-full px-2 py-1 text-sm border rounded">
                        <option value="none">None</option>
                        <option value="zoom-in">Zoom In</option>
                        <option value="zoom-out">Zoom Out</option>
                        <option value="pan-left">Pan Left</option>
                        <option value="pan-right">Pan Right</option>
                        <option value="pan-up">Pan Up</option>
                        <option value="pan-down">Pan Down</option>
                    </select>
                </div>

                {{-- Layer properties --}}
                @if (!empty($currentSlide['layers']) && isset($currentSlide['layers'][$selectedLayer]))
                    @php $layer = $currentSlide['layers'][$selectedLayer]; @endphp
                    <div>
                        <h4 class="text-sm font-semibold text-gray-600 mb-2">Layer {{ $selectedLayer + 1 }} ({{ ucfirst($layer['type'] ?? 'unknown') }})</h4>

                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <label class="block text-xs text-gray-500">X (px)</label>
                                <input type="number" wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.x" class="w-full px-2 py-1 text-sm border rounded">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Y (px)</label>
                                <input type="number" wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.y" class="w-full px-2 py-1 text-sm border rounded">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Z Index</label>
                                <input type="number" wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.z" class="w-full px-2 py-1 text-sm border rounded">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Width</label>
                                <input type="text" wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.width" class="w-full px-2 py-1 text-sm border rounded">
                            </div>
                        </div>

                        @if (($layer['type'] ?? '') === 'text')
                            <label class="block text-xs text-gray-500 mb-1">Content</label>
                            <textarea wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.content" rows="2" class="w-full px-2 py-1 text-sm border rounded"></textarea>

                            <div class="grid grid-cols-2 gap-2 mt-2">
                                <div>
                                    <label class="block text-xs text-gray-500">Color</label>
                                    <input type="color" wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.color" class="w-full h-8 border rounded">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Font Size</label>
                                    <input type="number" wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.font_size" class="w-full px-2 py-1 text-sm border rounded">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Font Weight</label>
                                    <select wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.font_weight" class="w-full px-2 py-1 text-sm border rounded">
                                        <option value="normal">Normal</option>
                                        <option value="bold">Bold</option>
                                        <option value="300">Light</option>
                                        <option value="600">Semi-Bold</option>
                                        <option value="800">Extra-Bold</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Text Align</label>
                                    <select wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.text_align" class="w-full px-2 py-1 text-sm border rounded">
                                        <option value="left">Left</option>
                                        <option value="center">Center</option>
                                        <option value="right">Right</option>
                                    </select>
                                </div>
                            </div>
                        @elseif (($layer['type'] ?? '') === 'image')
                            <label class="block text-xs text-gray-500 mb-1">Image Path</label>
                            <input type="text" wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.image" placeholder="banners/logo.png" class="w-full px-2 py-1 text-sm border rounded">
                        @elseif (($layer['type'] ?? '') === 'button')
                            <label class="block text-xs text-gray-500 mb-1">Button Text</label>
                            <input type="text" wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.content" class="w-full px-2 py-1 text-sm border rounded">

                            <label class="block text-xs text-gray-500 mb-1 mt-2">Link URL</label>
                            <input type="text" wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.link_url" class="w-full px-2 py-1 text-sm border rounded">

                            <div class="grid grid-cols-2 gap-2 mt-2">
                                <div>
                                    <label class="block text-xs text-gray-500">BG Color</label>
                                    <input type="color" wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.background" class="w-full h-8 border rounded">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Text Color</label>
                                    <input type="color" wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.color" class="w-full h-8 border rounded">
                                </div>
                            </div>
                        @elseif (($layer['type'] ?? '') === 'shape')
                            <label class="block text-xs text-gray-500 mb-1">Background Color</label>
                            <input type="color" wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.background" class="w-full h-8 border rounded">
                        @endif

                        {{-- Animation properties (all layer types) --}}
                        <div class="mt-3 pt-3 border-t">
                            <h5 class="text-xs font-semibold text-gray-500 mb-2">Animation</h5>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs text-gray-500">In</label>
                                    <select wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.animation_in" class="w-full px-1 py-1 text-xs border rounded">
                                        <option value="fade">Fade</option>
                                        <option value="slide-left">Slide Left</option>
                                        <option value="slide-up">Slide Up</option>
                                        <option value="scale">Scale</option>
                                        <option value="rotate">Rotate</option>
                                        <option value="flip">3D Flip</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Out</label>
                                    <select wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.animation_out" class="w-full px-1 py-1 text-xs border rounded">
                                        <option value="fade">Fade</option>
                                        <option value="slide-left">Slide Left</option>
                                        <option value="slide-up">Slide Up</option>
                                        <option value="scale">Scale</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Delay (ms)</label>
                                    <input type="number" wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.delay" class="w-full px-1 py-1 text-xs border rounded">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Duration (ms)</label>
                                    <input type="number" wire:model="slides.{{ $selectedSlide }}.layers.{{ $selectedLayer }}.duration" class="w-full px-1 py-1 text-xs border rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-400 text-center py-4">Select a layer to edit its properties</p>
                @endif
            </div>

            {{-- Banner engine config --}}
            <div class="bg-white rounded-lg shadow-sm p-4 mt-4">
                <h3 class="font-semibold mb-3 text-gray-700">Banner Engine</h3>
                <label class="block text-xs text-gray-500 mb-1">Animation Engine</label>
                <select wire:model="engine" class="w-full px-2 py-1 text-sm border rounded mb-2">
                    <option value="swiper">Swiper (Standard Carousel)</option>
                    <option value="gsap-cube">GSAP 3D Cube</option>
                    <option value="gsap-coverflow">GSAP 3D Coverflow</option>
                    <option value="gsap-flip">GSAP 3D Flip</option>
                    <option value="three-distort">Three.js WebGL Distortion</option>
                    <option value="canvas-particles">Canvas Particle Dissolve</option>
                    <option value="svg-morph">SVG Path Morph</option>
                    <option value="ken-burns">Ken Burns Cinematic</option>
                </select>

                <div class="grid grid-cols-2 gap-2 mt-2">
                    <label class="flex items-center gap-1 text-xs text-gray-600">
                        <input type="checkbox" wire:model="autoplay" class="rounded"> Autoplay
                    </label>
                    <label class="flex items-center gap-1 text-xs text-gray-600">
                        <input type="checkbox" wire:model="loop" class="rounded"> Loop
                    </label>
                    <label class="flex items-center gap-1 text-xs text-gray-600">
                        <input type="checkbox" wire:model="showNavigation" class="rounded"> Navigation
                    </label>
                    <label class="flex items-center gap-1 text-xs text-gray-600">
                        <input type="checkbox" wire:model="showPagination" class="rounded"> Pagination
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-2 mt-2">
                    <div>
                        <label class="block text-xs text-gray-500">Autoplay (ms)</label>
                        <input type="number" wire:model="autoplaySpeed" class="w-full px-2 py-1 text-sm border rounded">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">Transition (ms)</label>
                        <input type="number" wire:model="transitionSpeed" class="w-full px-2 py-1 text-sm border rounded">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JS: drag-drop layer positioning --}}
    <script>
    function bannerComposer() {
        return {
            saved: false,
            init() {
                // Listen for save event from Livewire
                window.addEventListener('banner-saved', (e) => {
                    this.saved = true;
                    setTimeout(() => this.saved = false, 3000);
                });
            },
            addLayerType(type) {
                // Dispatch Livewire event to add a layer
                this.$wire.addLayer(type);
            },
        };
    }

    // Global drag handler for layers (Vanilla JS, no jQuery)
    document.addEventListener('mousedown', function(e) {
        const layerEl = e.target.closest('[wire\\:click^="selectLayer"]');
        if (!layerEl) return;

        const canvas = layerEl.closest('.banner-canvas');
        if (!canvas) return;

        e.preventDefault();
        const rect = canvas.getBoundingClientRect();
        const layerRect = layerEl.getBoundingClientRect();
        const offsetX = e.clientX - layerRect.left;
        const offsetY = e.clientY - layerRect.top;

        // Extract layer index from the wire:click attribute
        const clickAttr = layerEl.getAttribute('wire:click');
        const match = clickAttr && clickAttr.match(/selectLayer\((\d+)\)/);
        if (!match) return;
        const layerIndex = parseInt(match[1]);

        function onMouseMove(ev) {
            const x = Math.max(0, Math.min(rect.width - layerRect.width, ev.clientX - rect.left - offsetX));
            const y = Math.max(0, Math.min(rect.height - layerRect.height, ev.clientY - rect.top - offsetY));
            layerEl.style.left = x + 'px';
            layerEl.style.top = y + 'px';
        }

        function onMouseUp(ev) {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);

            // Save final position to Livewire
            const x = parseInt(layerEl.style.left) || 0;
            const y = parseInt(layerEl.style.top) || 0;

            // Use Livewire's wire directive to call updateLayerPosition
            const livewire = window.Livewire;
            if (livewire) {
                // Find the Livewire component instance
                const component = livewire.find(layerEl.closest('[wire\\:id]')?.getAttribute('wire:id'));
                if (component) {
                    component.call('updateLayerPosition', layerIndex, x, y);
                }
            }
        }

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    });
    </script>
</div>

<style>
[x-cloak] { display: none !important; }
.banner-canvas { user-select: none; }
.banner-canvas .absolute { transition: border-color 0.15s; }
</style>
