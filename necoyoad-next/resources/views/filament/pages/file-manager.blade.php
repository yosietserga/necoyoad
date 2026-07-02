<x-filament-panels::page>
    <div x-data="fileManager()" x-init="init()">
        {{-- Toolbar --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <button @click="goUp()" class="filament-button" :disabled="currentPath === '/'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                    Up
                </button>
                <button @click="showCreateDir = !showCreateDir" class="filament-button">
                    New Folder
                </button>
                <button @click="$refs.fileInput.click()" class="filament-button filament-button-primary">
                    Upload
                </button>
                <input type="file" x-ref="fileInput" class="hidden" @change="uploadFile($event)" multiple>
            </div>
            <div class="text-sm text-gray-500" x-text="currentPath"></div>
        </div>

        {{-- Create directory inline form --}}
        <div x-show="showCreateDir" x-cloak class="mb-4 p-4 bg-gray-50 rounded-lg">
            <input type="text" x-model="newDirName" placeholder="Folder name" @keydown.enter="createDirectory()" class="filament-input">
            <button @click="createDirectory()" class="filament-button filament-button-primary ml-2">Create</button>
        </div>

        {{-- Main content: directory tree + file grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Directory sidebar --}}
            <div class="lg:col-span-1">
                <div class="filament-card p-4">
                    <h3 class="font-semibold mb-2">Directories</h3>
                    <ul class="space-y-1">
                        <template x-for="dir in directories" :key="dir.path">
                            <li>
                                <button @click="navigateTo(dir.path)" class="flex items-center gap-2 text-sm hover:text-primary-600" x-text="dir.name"></button>
                            </li>
                        </template>
                        <li x-show="directories.length === 0" class="text-sm text-gray-400">No subdirectories</li>
                    </ul>
                </div>
            </div>

            {{-- File grid --}}
            <div class="lg:col-span-2">
                <div class="filament-card p-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        <template x-for="file in files" :key="file.path">
                            <div class="filament-file-item group relative" @contextmenu.prevent="showContextMenu($event, file)">
                                <div class="aspect-square flex items-center justify-center bg-gray-100 rounded-lg overflow-hidden">
                                    <img x-show="file.thumb" :src="file.thumb" :alt="file.name" class="w-full h-full object-cover">
                                    <svg x-show="!file.thumb" class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="mt-1 text-xs truncate" x-text="file.name"></p>
                                <p class="text-xs text-gray-400" x-text="formatSize(file.size)"></p>
                                <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="deleteFile(file)" class="p-1 bg-red-500 text-white rounded hover:bg-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div x-show="files.length === 0" class="col-span-full text-center py-12 text-gray-400">
                            No files in this directory
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Context menu --}}
        <div x-show="contextMenu.visible" x-cloak
             :style="`position: fixed; left: ${contextMenu.x}px; top: ${contextMenu.y}px; z-index: 1000;`"
             class="bg-white rounded-lg shadow-lg border border-gray-200 py-1 w-40">
            <button @click="copyUrl(contextMenu.file)" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">Copy URL</button>
            <button @click="renameFile(contextMenu.file)" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">Rename</button>
            <button @click="deleteFile(contextMenu.file)" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 text-red-600">Delete</button>
        </div>
    </div>

    @push('scripts')
    <script>
    function fileManager() {
        return {
            currentPath: '/',
            directories: [],
            files: [],
            showCreateDir: false,
            newDirName: '',
            contextMenu: { visible: false, x: 0, y: 0, file: null },

            async init() {
                await this.loadDirectory('/');
                document.addEventListener('click', () => { this.contextMenu.visible = false; });
            },

            async loadDirectory(path) {
                this.currentPath = path;
                try {
                    const [dirsRes, filesRes] = await Promise.all([
                        fetch('/admin/api/filemanager/directories?path=' + encodeURIComponent(path), { headers: { 'X-Requested-With': 'XMLHttpRequest' } }),
                        fetch('/admin/api/filemanager/files?path=' + encodeURIComponent(path), { headers: { 'X-Requested-With': 'XMLHttpRequest' } }),
                    ]);
                    if (dirsRes.ok) { const d = await dirsRes.json(); this.directories = d.directories || []; }
                    if (filesRes.ok) { const f = await filesRes.json(); this.files = f.files || []; }
                } catch (e) {
                    console.error('Failed to load directory:', e);
                }
            },

            navigateTo(path) { this.loadDirectory(path); },
            goUp() {
                if (this.currentPath === '/') return;
                const parts = this.currentPath.split('/').filter(Boolean);
                parts.pop();
                this.loadDirectory('/' + parts.join('/'));
            },

            async createDirectory() {
                if (!this.newDirName) return;
                const fullPath = this.currentPath === '/' ? '/' + this.newDirName : this.currentPath + '/' + this.newDirName;
                try {
                    const res = await fetch('/admin/api/filemanager/directory', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ path: fullPath }),
                    });
                    if (res.ok) { this.newDirName = ''; this.showCreateDir = false; await this.loadDirectory(this.currentPath); }
                } catch (e) { console.error('Create dir failed:', e); }
            },

            async uploadFile(event) {
                const formData = new FormData();
                for (const file of event.target.files) {
                    formData.append('file', file);
                    formData.append('directory', this.currentPath);
                    try {
                        await fetch('/admin/api/filemanager/upload', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: formData,
                        });
                        formData.delete('file');
                        formData.delete('directory');
                    } catch (e) { console.error('Upload failed:', e); }
                    formData = new FormData();
                }
                event.target.value = '';
                await this.loadDirectory(this.currentPath);
            },

            async deleteFile(file) {
                if (!confirm('Delete ' + file.name + '?')) return;
                try {
                    const res = await fetch('/admin/api/filemanager/file?path=' + encodeURIComponent(file.path), {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    });
                    if (res.ok) await this.loadDirectory(this.currentPath);
                } catch (e) { console.error('Delete failed:', e); }
            },

            showContextMenu(event, file) {
                this.contextMenu = { visible: true, x: event.clientX, y: event.clientY, file };
            },

            copyUrl(file) {
                navigator.clipboard.writeText(file.url);
                this.contextMenu.visible = false;
            },

            renameFile(file) {
                const newName = prompt('New name:', file.name);
                if (!newName || newName === file.name) return;
                const dir = file.path.substring(0, file.path.lastIndexOf('/'));
                const newPath = dir + '/' + newName;
                fetch('/admin/api/filemanager/rename', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ from: file.path, to: newPath }),
                }).then(() => this.loadDirectory(this.currentPath));
                this.contextMenu.visible = false;
            },

            formatSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / 1048576).toFixed(1) + ' MB';
            },
        };
    }
    </script>
    @endpush
</x-filament-panels::page>
