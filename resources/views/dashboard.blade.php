<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BCA Finance - Sistem Pengajuan Kredit Kendaraan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-blue-600 mb-2">Sistem Pengajuan Kredit Kendaraan</h1>
            <p class="text-gray-600">BCA Finance - Solusi Kredit Terpercaya</p>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 relative">
                <span class="block sm:inline">{{ session('success') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <title>Close</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Stats Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-gray-500">Total Pengajuan</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-gray-500">Menunggu Persetujuan</h3>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['submitted'] }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-gray-500">Disetujui</h3>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-gray-500">Ditolak</h3>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Pengajuan -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Form Pengajuan Kredit</h2>
                    <p class="text-sm text-gray-600 mb-6">Silakan lengkapi data di bawah ini untuk mengajukan kredit kendaraan</p>
                    
                    <form action="{{ route('credit.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap Konsumen</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name') }}" 
                                   class="w-full border border-gray-300 p-3 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('customer_name') border-red-500 @enderror" 
                                   placeholder="Masukkan nama lengkap" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NIK (16 Digit)</label>
                            <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16"
                                   class="w-full border border-gray-300 p-3 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nik') border-red-500 @enderror" 
                                   placeholder="Contoh: 1234567890123456" required pattern="[0-9]{16}">
                            <p class="text-xs text-gray-500 mt-1">NIK harus 16 digit angka sesuai KTP</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kendaraan</label>
                            <select name="vehicle_type" class="w-full border border-gray-300 p-3 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('vehicle_type') border-red-500 @enderror" required>
                                <option value="">Pilih jenis kendaraan</option>
                                <option value="Motor" {{ old('vehicle_type') == 'Motor' ? 'selected' : '' }}>Motor</option>
                                <option value="Mobil" {{ old('vehicle_type') == 'Mobil' ? 'selected' : '' }}>Mobil</option>
                                <option value="Truck" {{ old('vehicle_type') == 'Truck' ? 'selected' : '' }}>Truck</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Kendaraan</label>
                            <input type="number" name="vehicle_price" value="{{ old('vehicle_price') }}" 
                                   class="w-full border border-gray-300 p-3 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('vehicle_price') border-red-500 @enderror" 
                                   placeholder="Contoh: 150000000" required min="10000000" max="2000000000">
                            <p class="text-xs text-gray-500 mt-1">Minimal Rp 10.000.000, Maksimal Rp 2.000.000.000</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto KTP</label>
                            <input type="file" name="ktp_image" accept="image/jpeg,image/png,image/jpg" 
                                   class="w-full border border-gray-300 p-3 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ktp_image') border-red-500 @enderror" required>
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, Maksimal 2MB</p>
                        </div>

                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium">
                            Kirim Pengajuan Kredit
                        </button>
                    </form>
                </div>
            </div>

            <!-- Dashboard Persetujuan -->
            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 sm:mb-0">Dashboard Persetujuan</h2>
                        
                        <!-- Filter dan Search -->
                        <div class="flex flex-col sm:flex-row gap-2">
                            <form method="GET" class="flex gap-2">
                                <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                    <option value="all">Semua Status</option>
                                    <option value="Submitted" {{ request('status') == 'Submitted' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                                
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Cari nama/NIK..." 
                                       class="border border-gray-300 rounded-md px-3 py-2 text-sm w-40">
                                <button type="submit" class="bg-gray-500 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-600">
                                    Cari
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($data->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 border-b">
                                        <th class="p-4 text-sm font-medium text-gray-700">Konsumen</th>
                                        <th class="p-4 text-sm font-medium text-gray-700">Kendaraan</th>
                                        <th class="p-4 text-sm font-medium text-gray-700">KTP</th>
                                        <th class="p-4 text-sm font-medium text-gray-700">Status</th>
                                        <th class="p-4 text-sm font-medium text-gray-700">Tanggal</th>
                                        <th class="p-4 text-sm font-medium text-gray-700">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $item)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-4">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $item->customer_name }}</p>
                                                <p class="text-sm text-gray-500">NIK: {{ $item->nik }}</p>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <div>
                                                <p class="font-medium">{{ $item->vehicle_type }}</p>
                                                <p class="text-sm text-gray-600">{{ $item->formatted_vehicle_price }}</p>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <img src="{{ $item->ktp_image_base64 }}" 
                                                 alt="KTP {{ $item->customer_name }}" 
                                                 class="h-12 w-20 object-cover rounded border cursor-pointer hover:scale-110 transition-transform"
                                                 onclick="showImageModal('{{ $item->ktp_image_base64 }}', '{{ $item->customer_name }}')">
                                        </td>
                                        <td class="p-4">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium text-white {{ $item->status_badge_class }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            <div class="text-sm">
                                                <p>{{ $item->created_at->format('d/m/Y') }}</p>
                                                <p class="text-gray-500">{{ $item->created_at->format('H:i') }}</p>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            @if($item->status == 'Submitted')
                                                <div class="flex gap-2">
                                                    <button onclick="showApprovalModal({{ $item->id }})" 
                                                            class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition">
                                                        Setujui
                                                    </button>
                                                    <button onclick="showRejectModal({{ $item->id }})" 
                                                            class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition">
                                                        Tolak
                                                    </button>
                                                </div>

                                                <!-- Modal Approve -->
                                                <div id="approvalModal{{ $item->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                                                    <div class="bg-white p-6 rounded-lg max-w-md w-full mx-4">
                                                        <h3 class="text-lg font-semibold mb-4">Konfirmasi Persetujuan</h3>
                                                        <p class="text-gray-600 mb-4">Setujui pengajuan kredit atas nama <strong>{{ $item->customer_name }}</strong>?</p>
                                                        <form action="{{ route('credit.approve', $item->id) }}" method="POST">
                                                            @csrf
                                                            <div class="mb-4">
                                                                <label class="block text-sm font-medium mb-2">Nama Penyetuju</label>
                                                                <input type="text" name="approved_by" required 
                                                                       class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Masukkan nama Anda">
                                                            </div>
                                                            <div class="mb-4">
                                                                <label class="block text-sm font-medium mb-2">Catatan (Opsional)</label>
                                                                <textarea name="notes" class="w-full border border-gray-300 rounded px-3 py-2" rows="3" placeholder="Tambahkan catatan approval..."></textarea>
                                                            </div>
                                                            <div class="flex gap-2 justify-end">
                                                                <button type="button" onclick="hideApprovalModal({{ $item->id }})" 
                                                                        class="px-4 py-2 text-gray-600 hover:text-gray-800 border rounded">Batal</button>
                                                                <button type="submit" 
                                                                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                                                    ✅ Setujui Pengajuan
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                                <!-- Modal Reject -->
                                                <div id="rejectModal{{ $item->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                                                    <div class="bg-white p-6 rounded-lg max-w-md w-full mx-4">
                                                        <h3 class="text-lg font-semibold mb-4">Konfirmasi Penolakan</h3>
                                                        <p class="text-gray-600 mb-4">Tolak pengajuan kredit atas nama <strong>{{ $item->customer_name }}</strong>?</p>
                                                        <form action="{{ route('credit.reject', $item->id) }}" method="POST">
                                                            @csrf
                                                            <div class="mb-4">
                                                                <label class="block text-sm font-medium mb-2">Alasan Penolakan *</label>
                                                                <textarea name="notes" required class="w-full border border-gray-300 rounded px-3 py-2" rows="4" placeholder="Berikan alasan penolakan yang jelas..."></textarea>
                                                            </div>
                                                            <div class="flex gap-2 justify-end">
                                                                <button type="button" onclick="hideRejectModal({{ $item->id }})" 
                                                                        class="px-4 py-2 text-gray-600 hover:text-gray-800 border rounded">Batal</button>
                                                                <button type="submit" 
                                                                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                                                    ❌ Tolak Pengajuan
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-sm">
                                                    @if($item->status == 'Approved')
                                                        <div class="text-green-600 font-medium">
                                                            <p>✅ Disetujui</p>
                                                            @if($item->approved_by)
                                                                <p class="text-gray-500 text-xs">oleh {{ $item->approved_by }}</p>
                                                            @endif
                                                            @if($item->approved_at)
                                                                <p class="text-gray-500 text-xs">{{ $item->approved_at->format('d/m/Y H:i') }}</p>
                                                            @endif
                                                        </div>
                                                    @elseif($item->status == 'Rejected')
                                                        <div class="text-red-600 font-medium">
                                                            <p>❌ Ditolak</p>
                                                            <p class="text-gray-500 text-xs">{{ $item->updated_at->format('d/m/Y H:i') }}</p>
                                                        </div>
                                                    @endif
                                                    @if($item->notes)
                                                        <p class="text-gray-500 text-xs mt-1 italic">"{{ Str::limit($item->notes, 30) }}"</p>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $data->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-400 text-6xl mb-4">📋</div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Pengajuan</h3>
                            <p class="text-gray-600">Pengajuan kredit akan muncul di sini setelah ada yang mengajukan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk menampilkan gambar KTP -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="max-w-4xl max-h-screen p-4">
            <div class="bg-white rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="imageModalTitle" class="text-lg font-semibold">Foto KTP</h3>
                    <button onclick="closeImageModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                </div>
                <img id="imageModalImg" src="" alt="KTP" class="max-w-full max-h-96 object-contain mx-auto">
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <script>
        function showImageModal(imageSrc, customerName) {
            document.getElementById('imageModalImg').src = imageSrc;
            document.getElementById('imageModalTitle').textContent = `Foto KTP - ${customerName}`;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Modal functions for approval/rejection
        function showApprovalModal(id) {
            document.getElementById('approvalModal' + id).classList.remove('hidden');
        }

        function hideApprovalModal(id) {
            document.getElementById('approvalModal' + id).classList.add('hidden');
        }

        function showRejectModal(id) {
            document.getElementById('rejectModal' + id).classList.remove('hidden');
        }

        function hideRejectModal(id) {
            document.getElementById('rejectModal' + id).classList.add('hidden');
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
                // Close all modals when clicking outside
                const modals = document.querySelectorAll('[id^="approvalModal"], [id^="rejectModal"]');
                modals.forEach(modal => {
                    modal.classList.add('hidden');
                });
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modals = document.querySelectorAll('[id^="approvalModal"], [id^="rejectModal"]');
                modals.forEach(modal => {
                    modal.classList.add('hidden');
                });
            }
        });
    </script>
</body>
</html>