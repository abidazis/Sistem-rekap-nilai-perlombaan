<div>
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Overview</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
            <div class="text-gray-500 text-sm">Event Aktif</div>
            <div class="text-2xl font-bold mt-1">LKBB PANDAWA 2026</div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
            <div class="text-gray-500 text-sm">Total Peserta</div>
            <div class="text-2xl font-bold mt-1">15 Sekolah</div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-yellow-500">
            <div class="text-gray-500 text-sm">Status Penilaian</div>
            <div class="text-2xl font-bold mt-1">Sedang Berlangsung</div>
        </div>
    </div>

    <div class="mt-8 bg-white p-6 rounded-lg shadow">
        <h3 class="font-bold text-gray-700 mb-4">Akses Cepat</h3>
        <div class="flex gap-4">
            <a href="{{ route('input.nilai') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-bold">
                Mulai Penilaian Juri &rarr;
            </a>
            <a href="{{ route('master.event') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300">
                Setting Lomba
            </a>
        </div>
    </div>
</div>