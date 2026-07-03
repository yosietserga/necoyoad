<x-filament-panels::page>
    <div x-data="themeEditor()" x-init="init()">
        {{-- Toolbar --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <select x-model="selectedTheme" @change="loadFiles()" class="filament-input">
                    <template x-for="theme in themes" :key="theme">
                        <option :value="theme" x-text="theme"></option>
                    </template>
                </select>
                <select x-model="selectedType" @change="loadFiles()" class="filament-input">
                    <option value="blade">Blade Templates</option>
                    <option value="css">CSS / SCSS</option>
                    <option value="js">JavaScript</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button @click="showVersions = !showVersions" class="filament-button" :disabled="!selectedFile">
                    Version History
                </button>
                <button @click="save()" class="filament-button filament-button-primary" :disabled="!selectedFile || !hasChanges">
                    Save
                </button>
            </div>
        </div>

        {{-- Main content: file tree + editor --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            {{-- File tree sidebar --}}
            <div class="lg:col-span-1">
                <div class="filament-card p-4 h-[600px] overflow-y-auto">
                    <h3 class="font-semibold mb-2">Files</h3>
                    <ul class="space-y-1">
                        <template x-for="file in filteredFiles" :key="file.path">
                            <li>
                                <button @click="openFile(file)"
                                        class="flex items-center gap-2 text-sm w-full text-left hover:text-primary-600"
                                        :class="{ 'text-primary-600 font-semibold': selectedFile === file.path }">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span x-text="file.path"></span>
                                </button>
                            </li>
                        </template>
                        <li x-show="filteredFiles.length === 0" class="text-sm text-gray-400">No files found</li>
                    </ul>
                </div>
            </div>

            {{-- Code editor --}}
            <div class="lg:col-span-3">
                <div class="filament-card p-4">
                    <div x-show="selectedFile" x-cloak>
                        <p class="text-sm text-gray-500 mb-2">
                            Editing: <span class="font-mono" x-text="selectedFile"></span>
                            <span x-show="hasChanges" class="ml-2 text-orange-600">(unsaved changes)</span>
                        </p>
                        <textarea x-ref="editor"
                                  x-model="fileContent"
                                  @input="hasChanges = true"
                                  class="w-full h-[500px] font-mono text-sm p-3 border border-gray-300 rounded-lg"
                                  spellcheck="false"></textarea>
                    </div>
                    <div x-show="!selectedFile" class="text-center py-24 text-gray-400">
                        Select a file from the sidebar to edit
                    </div>
                </div>
            </div>
        </div>

        {{-- Version history panel --}}
        <div x-show="showVersions" x-cloak class="mt-4 filament-card p-4">
            <h3 class="font-semibold mb-2">Version History</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Checksum</th>
                        <th class="text-left py-2">User</th>
                        <th class="text-left py-2">Date</th>
                        <th class="text-right py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="version in versions" :key="version.id">
                        <tr class="border-b">
                            <td class="py-2 font-mono text-xs" x-text="version.checksum"></td>
                            <td class="py-2" x-text="version.user_id || 'System'"></td>
                            <td class="py-2" x-text="new Date(version.created_at).toLocaleString()"></td>
                            <td class="py-2 text-right">
                                <button @click="restoreVersion(version.id)" class="filament-button text-xs">Restore</button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="versions.length === 0">
                        <td colspan="4" class="py-4 text-center text-gray-400">No version history</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
    function themeEditor() {
        return {
            themes: ['choroni'],
            selectedTheme: 'choroni',
            selectedType: 'blade',
            allFiles: [],
            selectedFile: null,
            fileContent: '',
            originalContent: '',
            hasChanges: false,
            showVersions: false,
            versions: [],

            async init() {
                this.selectedTheme = '{{ $activeTheme ?? 'choroni' }}';
                await this.loadFiles();
            },

            get filteredFiles() {
                return this.allFiles[this.selectedType] || [];
            },

            async loadFiles() {
                try {
                    const res = await fetch('/admin/api/theme/files?theme=' + encodeURIComponent(this.selectedTheme), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (res.ok) { this.allFiles = await res.json(); this.allFiles = this.allFiles.files || {}; }
                } catch (e) { console.error('Failed to load files:', e); }
            },

            async openFile(file) {
                if (this.hasChanges && !confirm('Discard unsaved changes?')) return;
                this.selectedFile = file.path;
                try {
                    const res = await fetch('/admin/api/theme/file?theme=' + encodeURIComponent(this.selectedTheme) + '&path=' + encodeURIComponent(file.path), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (res.ok) {
                        const data = await res.json();
                        this.fileContent = data.content;
                        this.originalContent = data.content;
                        this.hasChanges = false;
                    }
                } catch (e) { console.error('Failed to read file:', e); }
            },

            async save() {
                if (!this.selectedFile) return;
                try {
                    const res = await fetch('/admin/api/theme/file', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ theme: this.selectedTheme, path: this.selectedFile, content: this.fileContent }),
                    });
                    if (res.ok) {
                        this.originalContent = this.fileContent;
                        this.hasChanges = false;
                    }
                } catch (e) { console.error('Save failed:', e); }
            },

            async loadVersions() {
                if (!this.selectedFile) return;
                try {
                    const res = await fetch('/admin/api/theme/versions?theme=' + encodeURIComponent(this.selectedTheme) + '&path=' + encodeURIComponent(this.selectedFile), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (res.ok) { const d = await res.json(); this.versions = d.versions || []; }
                } catch (e) { console.error('Failed to load versions:', e); }
            },

            async restoreVersion(versionId) {
                if (!confirm('Restore this version? Current unsaved changes will be lost.')) return;
                try {
                    const res = await fetch('/admin/api/theme/restore', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ version_id: versionId }),
                    });
                    if (res.ok) { await this.openFile({ path: this.selectedFile }); this.showVersions = false; }
                } catch (e) { console.error('Restore failed:', e); }
            },
        };
    }
    </script>
    @endpush
</x-filament-panels::page>
