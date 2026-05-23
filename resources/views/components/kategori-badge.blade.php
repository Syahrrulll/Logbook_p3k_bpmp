{{--
    Komponen Badge Kategori AI
    Usage: @include('components.kategori-badge', ['logbook' => $logbook])
--}}

@if($logbook->kategori_ai)
    @php
        $info = $logbook->kategoriLabel();
        $colorMap = [
            'blue'   => 'bg-blue-100 text-blue-800 border-blue-200',
            'green'  => 'bg-green-100 text-green-800 border-green-200',
            'purple' => 'bg-purple-100 text-purple-800 border-purple-200',
            'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'pink'   => 'bg-pink-100 text-pink-800 border-pink-200',
            'gray'   => 'bg-gray-100 text-gray-700 border-gray-200',
        ];
        $iconMap = [
            'administrasi' => 'fa-file-alt',
            'lapangan'     => 'fa-map-marker-alt',
            'pelatihan'    => 'fa-chalkboard-teacher',
            'dokumentasi'  => 'fa-folder-open',
            'pelayanan'    => 'fa-hands-helping',
            'lainnya'      => 'fa-tag',
        ];
        $colorClass = $colorMap[$info['color']] ?? $colorMap['gray'];
        $iconClass  = $iconMap[$logbook->kategori_ai] ?? 'fa-tag';
        $confidence = $logbook->ai_confidence ? round($logbook->ai_confidence * 100) . '%' : '';
    @endphp

    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold border {{ $colorClass }}"
          title="Kategori AI">
        <i class="fas {{ $iconClass }} text-[10px]"></i>
        {{ $info['label'] }}
    </span>

@endif

{{-- Badge Duplikat --}}
@if($logbook->is_duplicate)
    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold border bg-red-100 text-red-700 border-red-200"
          title="Terdeteksi sebagai kemungkinan duplikat">
        <i class="fas fa-copy text-[10px]"></i>
        Duplikat
    </span>
@endif
