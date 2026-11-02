{{-- Organisme: tabel rekap nilai (S05) — satu baris per siswa, satu kolom per tugas --}}
@php
    $rows = [
        ['Ahmad Fauzi', '92', '95', '94'],
        ['Budi Santoso', '75', '–', '75'],
        ['Citra Lestari', '–', '88', '88'],
        ['Dewi Anggraini', '88', '90', '89'],
        ['Eka Putri', '80', '85', '83'],
    ];
@endphp

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
    <table class="w-full text-left text-base">
        <thead class="border-b border-slate-200 bg-slate-50 text-sm font-bold text-slate-900">
            <tr>
                <th scope="col" class="px-4 py-3">Nama</th>
                <th scope="col" class="px-2 py-3 text-center">T1</th>
                <th scope="col" class="px-2 py-3 text-center">T2</th>
                <th scope="col" class="px-3 py-3 text-center">Rata</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @foreach ($rows as [$name, $t1, $t2, $avg])
                <tr>
                    <th scope="row" class="px-4 py-3 text-base font-semibold text-slate-900">{{ $name }}</th>
                    <td class="px-2 py-3 text-center text-slate-800">{{ $t1 }}</td>
                    <td class="px-2 py-3 text-center text-slate-800">{{ $t2 }}</td>
                    <td class="px-3 py-3 text-center font-bold text-slate-900">{{ $avg }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="m-3 rounded-xl bg-slate-100 p-3 text-sm leading-5 text-slate-700">
        <p class="font-bold text-slate-900">Keterangan kolom</p>
        <p>T1 = Latihan Pecahan Senilai</p>
        <p>T2 = PR Perkalian Dasar</p>
        <p>– = belum mengumpulkan</p>
    </div>
</div>
